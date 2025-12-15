<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Lib\SocialLogin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\LinkedInOpenIdProvider;

class SocialiteController extends Controller
{

    public function socialLogin($provider)
    {
        $socialLogin = new SocialLogin($provider);
        return $socialLogin->redirectDriver();
    }

    public function callback($provider)
    {

        if ($provider == 'linkedin'){
            try {

                $config = [
                    'client_id'     => '86to9sop9cflo9',
                    'client_secret' => 'WPL_AP1.7HScKVc6kJZnba6v.Ib8KyQ==',
                    'redirect'      => route('user.social.login.callback', ['provider' => 'linkedin']),
                ];

                $user = Socialite::buildProvider(LinkedInOpenIdProvider::class, $config)->user();;

                // Example user handling (adjust for your app)
                $userData = User::firstOrCreate(
                    ['provider_id' => $user->getId()],
                    [
                        'firstname' => $user->getName(),
                        'email' => $user->getEmail(),
                        'provider' => $provider
                    ]
                );

                Auth::login($userData);

                return redirect()->route('user.home');

            }
            catch (\Exception $e) {
                Log::error('Social login failed', [
                    'provider' => $provider,
                    'message' => $e->getMessage()
                ]);

                dd($e->getMessage());

                $notify[] = ['error', 'Login with ' . ucfirst($provider) . ' failed: ' . $e->getMessage()];
                return to_route('home')->withNotify($notify);
            }
        }
        else{
            $socialLogin = new SocialLogin($provider);
            try {
                return $socialLogin->login();
            } catch (\Exception $e) {
                $notify[] = ['error', $e->getMessage()];
                return to_route('home')->withNotify($notify);
            }
        }
    }
}
