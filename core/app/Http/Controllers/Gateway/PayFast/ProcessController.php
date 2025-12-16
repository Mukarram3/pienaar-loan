<?php

namespace App\Http\Controllers\Gateway\PayFast;

use App\Constants\Status;
use App\Models\Admin;
use App\Models\Deposit;
use App\Http\Controllers\Gateway\PaymentController;
use App\Models\Gateway;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Token;
use Illuminate\Support\Facades\Session;


class ProcessController extends Controller
{

    /*
     * Stripe Gateway
     */
    public static function process($deposit)
    {

        $alias = $deposit->gateway->alias;

        $send['track'] = $deposit->trx;
        $send['view'] = 'user.payment.'.$alias;
        $send['method'] = 'post';
        $send['url'] = route('ipn.'.$alias);
        return json_encode($send);
    }

    function pfValidSignature( $pfData, $pfParamString, $pfPassphrase = null ) {
        // Calculate security signature
        if($pfPassphrase === null) {
            $tempParamString = $pfParamString;
        } else {
            $tempParamString = $pfParamString.'&passphrase='.urlencode( $pfPassphrase );
        }

        $signature = md5( $tempParamString );
        return ( $pfData['signature'] === $signature );
    }

    function pfValidPaymentData( $cartTotal, $pfData ) {
        return !(abs((float)$cartTotal - (float)$pfData['amount_gross']) > 0.01);
    }

    function pfValidIP() {
        // Variable initialization
        $validHosts = array(
            'www.payfast.co.za',
            'sandbox.payfast.co.za',
            'w1w.payfast.co.za',
            'w2w.payfast.co.za',
        );

        $validIps = [];

        foreach( $validHosts as $pfHostname ) {
            $ips = gethostbynamel( $pfHostname );

            if( $ips !== false )
                $validIps = array_merge( $validIps, $ips );
        }

        // Remove duplicates
        $validIps = array_unique( $validIps );
        $referrerIp = gethostbyname(parse_url($_SERVER['HTTP_REFERER'])['host']);
        if( in_array( $referrerIp, $validIps, true ) ) {
            return true;
        }
        return false;
    }

    function pfValidServerConfirmation( $pfParamString, $pfHost = 'sandbox.payfast.co.za', $pfProxy = null ) {
        // Use cURL (if available)
        if( in_array( 'curl', get_loaded_extensions(), true ) ) {
            // Variable initialization
            $url = 'https://'. $pfHost .'/eng/query/validate';

            // Create default cURL object
            $ch = curl_init();

            // Set cURL options - Use curl_setopt for greater PHP compatibility
            // Base settings
            curl_setopt( $ch, CURLOPT_USERAGENT, NULL );  // Set user agent
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );      // Return output as string rather than outputting it
            curl_setopt( $ch, CURLOPT_HEADER, false );             // Don't include header in output
            curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
            curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true );

            // Standard settings
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_POST, true );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $pfParamString );
            if( !empty( $pfProxy ) )
                curl_setopt( $ch, CURLOPT_PROXY, $pfProxy );

            // Execute cURL
            $response = curl_exec( $ch );
            curl_close( $ch );
            if ($response === 'VALID') {
                return true;
            }
        }
        return false;
    }

    public function ipn(Request $request)
    {
        $gateway = Gateway::where('alias', 'PayFast')->first();
        $parameters = collect(json_decode($gateway->gateway_parameters));
        $pfParamString = '';
        $passphrase = $parameters['passphrase']->value;
        $pfData = $_POST;
        foreach( $pfData as $key => $val ) {
            $pfData[$key] = stripslashes( $val );
        }

        foreach( $pfData as $key => $val ) {
            if( $key !== 'signature' ) {
                $pfParamString .= $key .'='. urlencode( $val ) .'&';
            } else {
                break;
            }
        }

        $pfParamString = substr( $pfParamString, 0, -1 );

        $pfHost = $parameters['api_mode']->value == 'live'
            ? 'www.payfast.co.za'
            : 'sandbox.payfast.co.za';
        $pfData = $request->all();

        $deposit = Deposit::where('trx', $request->m_payment_id)->orderBy('id', 'DESC')->first();
        $user = User::find($deposit->user_id);
        notify($user, 'DEPOSIT_REQUEST', [
            'amount' => $deposit->amount
        ]);

        $admins = Admin::all();

//        foreach ($admins as $admin){
//            notify($admin, 'New_Deposit_Pending_Approval', [
//                'name' => $user->username,
//                'amount' => showAmount($deposit->final_amount,currencyFormat:false)
//            ]);
//        }


        $final_amount = number_format(sprintf('%.2f', $deposit->final_amount), 2, '.', '');
        $check1 = $this->pfValidSignature($pfData, $pfParamString, $passphrase);
        $check2 = $this->pfValidIP();
        $check3 = $this->pfValidPaymentData($final_amount, $pfData);
        $check4 = $this->pfValidServerConfirmation($pfParamString, $pfHost);

        if($check1 && $check2 && $check3 && $check4) {
            Log::info('All Checks passed');

            if ($deposit->status == Status::PAYMENT_SUCCESS) {
                $notify[] = ['error', 'Invalid request.'];
                return redirect($deposit->failed_url)->withNotify($notify);
            }

            try {

                if ($request->payment_status == 'COMPLETE') {
                    PaymentController::userDataUpdate($deposit);
                }
            }
            catch (\Exception $e) {
                Log::info($e->getMessage());
            }
        } else {
            Log::info('failed');
        }
    }
}
