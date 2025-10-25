<?php

namespace App\Http\Controllers\Api;

use App\Models\Frontend;
use App\Models\Language;
use App\Http\Controllers\Controller;

class AppController extends Controller
{
    public function generalSetting(){
        $notify[] = 'General setting data';
        return response()->json([
            'remark'=>'general_setting',
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[
                'general_setting'=>gs(),
                'social_login_redirect'=> route('user.social.login.callback', '')
            ],
        ]);
    }

    public function getCountries(){
        $countryData = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $notify[] = 'Country List';
        foreach($countryData as $k => $country){
            $countries[] = [
                'country'=>$country->country,
                'dial_code'=>$country->dial_code,
                'country_code'=>$k,
            ];
        }
        return response()->json([
            'remark'=>'country_data',
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[
                'countries'=>$countries,
            ],
        ]);
    }

    public function getLanguage($code){
        $languages = Language::get();
        $languageCodes = $languages->pluck('code')->toArray();

        if (!in_array($code, $languageCodes)) {
            $notify[] = 'Invalid code given';
            return response()->json([
                'remark' => 'validation_error',
                'status' => 'error',
                'message' => ['error' => $notify]
            ]);
        }

        $jsonFile = file_get_contents(resource_path('lang/' . $code . '.json'));

        $notify[] = 'Language';
        return response()->json([
            'remark' => 'language',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'languages' => $languages,
                'file' => json_decode($jsonFile) ?? [],
                'image_path'=>getFilePath('language')
            ],
        ]);
    }

    public function policies()
    {
        $policies = getContent('policy_pages.element', orderById: true);
        $notify[] = 'All policies';
        return response()->json([
            'remark' => 'policy_data',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'policies' => $policies,
            ],
        ]);
    }

    public function policyDetail($id) {

        $policyDetail = Frontend::where('id', $id)->first();
        if (!$policyDetail) {
            $notify[] = 'Policy detail not found';
            return response()->json([
                'remark'  => 'page_not_found',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }

        $notify[] = 'Policy detail';
        return response()->json([
            'remark'  => 'policy_detail',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'policy_detail' => $policyDetail,
            ],
        ]);
    }

    public function faq()
    {
        $faq = getContent('faq.element', orderById: true);
        $notify[] = 'FAQ';
        return response()->json([
            'remark' => 'faq',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'faq' => $faq,
            ],
        ]);
    }
}
