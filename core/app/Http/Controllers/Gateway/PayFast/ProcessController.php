<?php

namespace App\Http\Controllers\Gateway\PayFast;

use App\Constants\Status;
use App\Models\Deposit;
use App\Http\Controllers\Gateway\PaymentController;
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
        header( 'HTTP/1.0 200 OK' );
        flush();

        define( 'SANDBOX_MODE', true );
        Log::info($request->all());
        Log::info('--- PayFast ITN received ---');

        $pfHost = SANDBOX_MODE ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
        $pfData = $request->all();

//        foreach( $pfData as $key => $val ) {
//            $pfData[$key] = stripslashes( $val );
//        }
//
//        foreach( $pfData as $key => $val ) {
//            if( $key !== 'signature' ) {
//                $pfParamString .= $key .'='. urlencode( $val ) .'&';
//            } else {
//                break;
//            }
//        }

//        $check2 = $this->pfValidIP();
        $check3 = $this->pfValidPaymentData('124.12', $pfData);
//        $check4 = $this->pfValidServerConfirmation($pfParamString, $pfHost);

        if($check3) {
            Log::info('check 3 passed');
        } else {
            Log::info('failed');
        }

//        $track = Session::get('Track');
//        $deposit = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
//        if ($deposit->status == Status::PAYMENT_SUCCESS) {
//            $notify[] = ['error', 'Invalid request.'];
//            return redirect($deposit->failed_url)->withNotify($notify);
//        }
//        $request->validate([
//            'cardNumber' => 'required',
//            'cardExpiry' => 'required',
//            'cardCVC' => 'required',
//        ]);
//
//        $cc = $request->cardNumber;
//        $exp = $request->cardExpiry;
//        $cvc = $request->cardCVC;
//
//        $exp = explode("/", $_POST['cardExpiry']);
//        if (!@$exp[1]) {
//            $notify[] = ['error', 'Invalid expiry date provided'];
//            return back()->withNotify($notify);
//        }
//        $emo = trim($exp[0]);
//        $eyr = trim($exp[1]);
//        $cents = round($deposit->final_amount, 2) * 100;
//
//        $stripeAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
//
//
//        Stripe::setApiKey($stripeAcc->secret_key);
//
//        Stripe::setApiVersion("2020-03-02");
//
//        try {
//            $token = Token::create(array(
//                    "card" => array(
//                    "number" => "$cc",
//                    "exp_month" => $emo,
//                    "exp_year" => $eyr,
//                    "cvc" => "$cvc"
//                )
//            ));
//            try {
//                $charge = Charge::create(array(
//                    'card' => $token['id'],
//                    'currency' => $deposit->method_currency,
//                    'amount' => $cents,
//                    'description' => 'item',
//                ));
//
//                if ($charge['status'] == 'succeeded') {
//                    PaymentController::userDataUpdate($deposit);
//                    $notify[] = ['success', 'Payment captured successfully'];
//                    return redirect($deposit->success_url)->withNotify($notify);
//                }
//            } catch (\Exception $e) {
//                $notify[] = ['error', $e->getMessage()];
//            }
//        } catch (\Exception $e) {
//
//            $notify[] = ['error', $e->getMessage()];
//        }
//
//        return back()->withNotify($notify);
    }
}
