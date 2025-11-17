<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    public $pageTitle;

    public function index()
    {
        $this->pageTitle = 'All Loans';
        return $this->loanData();
    }

    public function runningLoans($userId = 0)
    {
        $this->pageTitle = 'Running Loans';
        return $this->loanData('running', $userId);
    }

    public function pendingLoans($userId = 0)
    {
        $this->pageTitle = 'Pending Loans';
        return $this->loanData('pending', $userId);
    }

    public function paidLoans($userId = 0)
    {
        $this->pageTitle = 'Paid Loans';
        return $this->loanData('paid', $userId);
    }

    public function rejectedLoans($userId = 0)
    {
        $this->pageTitle = 'Rejected Loans';
        return $this->loanData("rejected", $userId);
    }

    public function dueInstallment()
    {
        $this->pageTitle = 'Due Installment Loans';
        return $this->loanData("due");
    }

    public function details($id)
    {
        $loan      = Loan::where('id', $id)->with('plan', 'user')->firstOrFail();
        $pageTitle = 'Loan Details';
        return view('admin.loan.details', compact('pageTitle', 'loan'));
    }

    public function approve($id)
    {
        $loan              = Loan::with('user', 'plan')->findOrFail($id);
        $loan->status      = Status::LOAN_RUNNING;
        $loan->approved_at = now();
        $loan->save();
        Installment::saveInstallments($loan, now()->addDays((int) $loan->installment_interval));

        $user = $loan->user;
        $user->balance += getAmount($loan->amount);
        $user->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $loan->amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge       = 0;
        $transaction->trx_type     = '+';
        $transaction->details      = 'Loan taken';
        $transaction->trx          = getTrx();
        $transaction->remark       = 'loan_taken';
        $transaction->save();

        $shortCodes                          = $loan->shortCodes();
        $shortCodes['next_installment_date'] = now()->addDays((int) $loan->installment_interval);

        notify($user, "LOAN_APPROVE", $loan->shortCodes());

        $notify[] = ['success', 'Loan approved successfully'];
        return back()->withNotify($notify);
    }

    public function assign(Request $request){
        $user = Admin::find($request->admin_id);
        $loan = Loan::find($request->id);
        $loan->approved_by = $request->admin_id;
        $loan->save();
//        notify($user, "LOAN_APPROVE", $loan->shortCodes());

        $notify[] = ['success', 'Loan Assigned successfully'];
        return back()->withNotify($notify);
    }

    public function reject(Request $request)
    {
        $request->validate([
            'reason' => 'required|string',
        ]);
        $loan                 = Loan::where('id', $request->id)->with('user', 'plan')->firstOrFail();
        $loan->status         = Status::LOAN_REJECTED;
        $loan->admin_feedback = $request->reason;
        $loan->save();

        notify($loan->user, "LOAN_REJECT", $loan->shortCodes());

        $notify[] = ['success', 'Loan rejected successfully'];
        return back()->withNotify($notify);
    }

    protected function loanData($scope = null, $id = 0)
    {
        if (Auth::guard('admin')->user()->id != '1'){
            $query = Loan::orderBy('id', 'DESC')
                ->where('approved_by', Auth::guard('admin')->user()->id);
        }
        else{
            $query = Loan::orderBy('id', 'DESC');
        }

        if ($scope) {
            $query->$scope();
        }

        if ($id) {
            $query = $query->where('user_id', $id);
        }

        $pageTitle = $this->pageTitle;
        $loans     = $query->searchable(['loan_number', 'user:username'])->dateFilter()->filter(['status'])->with('user:id,username', 'plan', 'plan.category', 'nextInstallment')->paginate(getPaginate());
        return view('admin.loan.index', compact('pageTitle', 'loans'));
    }

    public function installments($id)
    {
        $loan         = Loan::with('installments')->findOrFail($id);
        $installments = $loan->installments()->paginate(getPaginate());
        $pageTitle    = "Installments";
        return view('admin.loan.installments', compact('pageTitle', 'installments', 'loan'));
    }
}
