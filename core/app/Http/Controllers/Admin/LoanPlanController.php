<?php

// =============================================================
// File: app/Http/Controllers/Admin/LoanPlanController.php
// =============================================================
namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Category;
use App\Models\LoanPlan;
use Illuminate\Http\Request;

class LoanPlanController extends Controller
{

    public function index()
    {
        $pageTitle = 'All Loan Plans';
        $plans     = LoanPlan::with('category')->searchable(['name', 'title', 'installment_interval', 'category:name'])->latest()->paginate(getPaginate());
        return view('admin.plans.loan.index', compact('pageTitle', 'plans'));
    }

    public function create()
    {
        $pageTitle = 'Add New Plan';
        $categories = Category::where('status', Status::ENABLE)->latest()->get();
        return view('admin.plans.loan.form', compact('pageTitle', 'categories'));
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Plan';
        $categories = Category::where('status', Status::ENABLE)->latest()->get();
        $plan      = LoanPlan::findOrFail($id);
        $form      = @$plan->form;
        return view('admin.plans.loan.form', compact('pageTitle', 'plan', 'form', 'categories'));
    }

    public function store(Request $request, $id = 0)
    {
        $this->validation($request);

        $formProcessor = new FormProcessor();

        if ($id) {
            $plan     = LoanPlan::findOrFail($id);
            $generate = $formProcessor->generate('loan_plan', true, 'id', $plan->form_id);
            $message  = 'Plan updated successfully';
        } else {
            $plan     = new LoanPlan();
            $generate = $formProcessor->generate('loan_plan');
            $message  = 'Plan added successfully';
        }

        $plan->category_id                = $request->category_id;
        $plan->name                       = $request->name;
        $plan->title                      = $request->title;
        $plan->total_installment          = $request->total_installment;
        $plan->installment_interval       = $request->installment_interval;
        $plan->per_installment            = $request->per_installment;
        $plan->minimum_amount             = $request->minimum_amount;
        $plan->maximum_amount             = $request->maximum_amount;
        $plan->form_id                    = $generate->id ?? 0;
        $plan->instruction                = $request->instruction ?? null;
        $plan->delay_value                = $request->delay_value;
        $plan->fixed_charge               = $request->fixed_charge;
        $plan->percent_charge             = $request->percent_charge;
        $plan->is_featured                = $request->is_featured;
        $plan->application_fixed_charge   = $request->application_fixed_charge;
        $plan->application_percent_charge = $request->application_percent_charge;
        $plan->is_legacy        = $request->boolean('is_legacy');
        $plan->calculation_type = $request->calculation_type === LoanPlan::CALC_FIXED_CAPITAL
            ? LoanPlan::CALC_FIXED_CAPITAL
            : LoanPlan::CALC_STANDARD;

        if ($plan->is_legacy || $plan->usesFixedCapital()) {
            $capPct  = (float) ($request->capital_ratio_pct ?? 50);
            $profPct = (float) ($request->profit_ratio_pct ?? 50);

            // Allocation must sum to 100%. Values entered by the administrator
            // are NEVER silently adjusted (spec s.9) — the save is rejected
            // instead, so nobody discovers a rewritten ratio after the fact.
            if (abs(($capPct + $profPct) - 100) > 0.01) {
                return back()->withInput()->withErrors([
                    'capital_ratio_pct' => sprintf(
                        'Capital Allocation (%.2f%%) + Profit Allocation (%.2f%%) must equal 100%%. '
                        . 'Currently %.2f%%. Please correct the allocation.',
                        $capPct, $profPct, $capPct + $profPct
                    ),
                ]);
            }

            $plan->capital_ratio = $capPct / 100;
            $plan->profit_ratio  = $profPct / 100;
        } else {
            $plan->capital_ratio = 0.5;
            $plan->profit_ratio  = 0.5;
        }

        $plan->save();

        $notify[] = ['success', $message];

        // Capital recovery advisory (spec s.10). A WARNING, not a block —
        // the administrator's values are saved exactly as entered.
        if ($plan->usesFixedCapital()) {
            $recovery = (float) $plan->per_installment * (int) $plan->total_installment;

            if (abs($recovery - 100) > 0.01) {
                $notify[] = ['warning', sprintf(
                    'Capital recovery check: %s%% per instalment x %d instalments = %.2f%% of '
                    . 'original capital (expected 100%%). The borrower is scheduled to repay %s '
                    . 'than the capital advanced. The plan has been saved as entered.',
                    rtrim(rtrim(number_format((float) $plan->per_installment, 4), '0'), '.'),
                    (int) $plan->total_installment,
                    $recovery,
                    $recovery > 100 ? 'MORE' : 'LESS'
                )];
            }
        }

        return back()->withNotify($notify);
    }

    public function changeStatus($id)
    {
        return LoanPlan::changeStatus($id);
    }

    public function validation($request)
    {
        $validation = [
            'name'                       => 'required|max:40',
            'title'                      => 'required|max:40',
            'total_installment'          => 'required|integer|gt:0',
            'installment_interval'       => 'required|integer|gt:0',
            'per_installment'            => 'required|numeric|gt:0',
            'minimum_amount'             => 'required|numeric|gt:0',
            'maximum_amount'             => 'required|numeric|gt:minimum_amount',
            'instruction'                => 'nullable|max:64000',
            'delay_value'                => 'required|integer|gt:0',
            'fixed_charge'               => 'required|numeric|gte:0',
            'percent_charge'             => 'required|numeric|gte:0',
            'input_form'                 => 'sometimes|required|array',
            'input_form.*.field_name'    => 'sometimes|required|string',
            'input_form.*.type'          => 'sometimes|required|in:text,textarea,file',
            'input_form.*.validation'    => 'sometimes|required|in:required,nullable',
            'is_featured'                => 'nullable|in:0,1',
            'application_fixed_charge'   => 'required|numeric',
            'application_percent_charge' => 'required|numeric',
            'is_legacy'         => 'required|in:0,1',
            'calculation_type'  => 'nullable|in:standard,fixed_capital',
            'capital_ratio_pct' => 'nullable|numeric|min:0|max:100',
            'profit_ratio_pct'  => 'nullable|numeric|min:0|max:100',
        ];

        $formProcessor       = new FormProcessor();
        $generatorValidation = $formProcessor->generatorValidation();
        $validation          = array_merge($validation, $generatorValidation['rules']);
        $validationMessage   = array_merge(@$generatorValidation['messages'] ?? [], [
            'input_form.*.field_name' => 'All Required Information field is required',
            'input_form.*.type'       => 'All Required Information field is required',
            'input_form.*.validation' => 'All Required Information field is required',
        ]);

        $request->validate($validation, $validationMessage);
    }
}
