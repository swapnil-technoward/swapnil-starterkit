<?php

namespace Swapnil\StarterKit\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Logs;
use App\Models\User;
use Artisan;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class LoginController extends Controller
{
    /*
       |--------------------------------------------------------------------------
       | Login Controller
       |--------------------------------------------------------------------------
       |
       | This controller handles authenticating users for the application and
       | redirecting them to your home screen. The controller uses a trait
       | to conveniently provide its functionality to your applications.
       |
        */
    /**
     * Where to redirect users after login.
     */

    public function login()
    {
        $registration_mode = config('starterkit.register');
        $remember_me = config('starterkit.remember_me');
//        Artisan::call('config:cache');
        return view('swapnil-starterkit::auth.login',compact('registration_mode','remember_me'));
    }

    public function fakeLogin(Request $request)
    {
        $messages = [
            'email.required' => 'Email is required to be filled.',
            'email.email' => 'Email is not in a valid format.',
            'password.required' => 'Password is required to be filled.',
            'password.min' => 'Password must be at least 8 characters long.'
        ];

        $validator = Validator::make($request->all(),[
            'email' => 'bail|required|email:dns,rfc',
            'password' => ['bail','required',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(100)
            ],
        ],$messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }
        $email = $request->input('email');
        $password = $request->input('password');
        $remember_me = $request->input('remember_me',false);
        $user = User::firstWhere('email',$email);
        if(!$user) {
            return redirect()->back()->with(['error' => 'Account not found']);
        }
        if(config('starterkit.remember_me') && $remember_me == '1') {
            $remember_me = true;
        }
        if (Hash::check($password,$user->password)) {
            Auth::login($user,$remember_me);
            if (Auth::check()) {
                Log::info('Login successful');
                return redirect()->route('dashboard');
            } else {
                Log::error('Failed to login');
                return redirect()->back()->with(['error' => 'Login failed!']);
            }
        } else {
            return redirect()->back()->with(['error' => 'Failed to login. Incorrect password!']);
        }
    }

    public function showRegisterForm(Request $request) {
        if (config('starterkit.register')) {
            return view('swapnil-starterkit::auth.register');
        } else {
            abort(404);
        }
    }

    public function registerUser(Request $request) {
        if (config('starterkit.register')) {
            $messages = [
                'name.required' => 'Name is required to be filled.',
                'name.regex' => 'Name can only contain alphabetic characters.',
                'email.required' => 'Email is required to be filled.',
                'email.email' => 'Email is not in a valid format.',
                'password.required' => 'Password is required to be filled.',
                'password.min' => 'Password must be at least 8 characters long.'
            ];

            $validator = Validator::make($request->all(), [
                'name' => 'bail|required|regex:/^[a-z A-Z]+$/',
                'email' => 'bail|required|email:dns,rfc',
                'password' => ['bail', 'required',
                    Password::min(8)
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->symbols()
                        ->uncompromised(100)
                ],
            ], $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }
            $name = $request->input('name');
            $email = $request->input('email');
            $password = $request->input('password');


            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->password = Hash::make($password);
            if ($user->save()) {
                return redirect()->route('login')->with(['success' => 'Registered successfully. Please login.!']);
            } else {
                return redirect()->back()->with(['error' => 'Registration Failed. Please try again.!']);
            }
        } else {
            abort(404);
        }
    }


    public function forgotPassword() {
        return view('swapnil-starterkit::auth.forgot_password');
    }

    public function reset_password(Request $request)
    {
        $message = [
            'forgot_email.required' => 'Please Enter A valid Email',
        ];
        $validator = Validator::make($request->all(), [
            'forgot_email' => 'bail|required|email:rfc,dns',
        ], $message);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $email = $request->forgot_email;
        $user = User::firstWhere(['email' => $email]);
        if ($user) {
            if ($user->reset_hash) {
                $then = $user->reset_hash;
                if (((is_numeric($then) && (time() - $then) > 3600) || !is_numeric($then))) {        //expired link..
                    $user->reset_hash = '';
                }

                if (is_numeric($then) && (time() - $then) < 300) {        //very early to re-generate! < 5 mins.
                    $deficit = 300 - (time() - $then);
                    $after = Carbon::createFromTimestamp($deficit)->diffForHumans(['parts' => 3, 'join' => ', ', 'syntax' => CarbonInterface::DIFF_ABSOLUTE]);
                    $msg = 'Reset link already generated and sent to your email, Please try again after ' . $after;
                    return redirect()->back()->with(['error' => $msg]);
                }
            }

            $reset_hash = time();
            $user->update(['reset_hash' => $reset_hash]);
            $reset_link = route('reset_hash', ['hash' => rawurlencode(Crypt::encryptString($reset_hash . '|' . $user->email))]);
            try {
                $companyName = env('APP_NAME','Your Company Name');
                $name = $user->name;
                Mail::send('views::emails.forgot',
                    ['site_name' => $companyName, 'name' => $name, 'email' => $email, 'subject' => 'Forgot Password Email','reset_link' => $reset_link],
                    function ($m) use ($companyName) {
                        $m->to("nishaniya.swapnil295@proton.me", $companyName)->subject('Forgot Password Email');
                    });
                return redirect()->back()->with(['success' => 'Please check your email for password reset instructions. Do not forget to check spam box as well.']);
            } catch (Exception $e) {
                Log::info("ContactUs form exception => ".$e->getMessage());
                return redirect()->back()->with(['error' => 'Something went wrong. Please try again after sometime.']);
            }
        } else {
            return redirect()->back()->with(['error' => 'Account not found! please contact customer support']);
        }
    }

    public function reset_hash(Request $request)
    {
        $message = [
            'hash.required' => 'hash is required',
            'hash.string' => 'hash should be string',

        ];
        $validator = Validator::make($request->all(), [
            'hash' => 'bail|required|string'
        ], $message);
        if ($validator->fails()) {
            return redirect()->route('login')->with('error', $validator->errors()->first());
        }
        $reset_hash = Crypt::decryptString(urldecode($request->hash));
        if ($reset_hash != "") {
            $data = explode('|', $reset_hash);
            $then = $data[0];
            if ((is_numeric($then) && (time() - $then) > 3600) || !is_numeric($then)) {
                return redirect()->route('login')->with(['error' => 'This link is expired! Please generate another link to continue']);
            }
            $found_user = User::firstWhere(['reset_hash' => $then, 'email' => $data[1]]);
            Log::info('found_user : '.$found_user);
            if ($found_user) {
                $password = $this->generateStrongPassword(8);
                Log::info('password : '.$password);
                $phpass = Hash::make($password);
                if ($found_user->update(['password' => $phpass, 'reset_hash' => ''])) {
                    $companyName = env('APP_NAME','Your Company Name');
                    Mail::send('views::emails.password_email',
                        ['site_name' => $companyName, 'email' => $data[1], 'subject' => 'Your New Password','password' => $password],
                        function ($m) use ($companyName) {
                            $m->to("nishaniya.swapnil295@proton.me", $companyName)->subject('Your New Password');
                        });
                    return redirect()->back()->with(['success' => 'New temporary password sent to your email']);
                }
            } else {
                return redirect()->back()->with(['error' => 'This link is invalid! Please generate another link to continue']);
            }
        }
        return redirect()->back()->with(['error' => 'Something went wrong! Please contact customer support.']);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    protected function generateStrongPassword($length = 12) {
        // Define character pools for password generation
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $specialCharacters = '!@#$%^&*()-_=+{}[]|:;<>,.?/~`';

        // Ensure the password has at least one character from each pool
        $password = '';
        $password .= $uppercase[rand(0, strlen($uppercase) - 1)];
        $password .= $lowercase[rand(0, strlen($lowercase) - 1)];
        $password .= $numbers[rand(0, strlen($numbers) - 1)];
        $password .= $specialCharacters[rand(0, strlen($specialCharacters) - 1)];

        // Generate the rest of the password using all character pools combined
        $allCharacters = $uppercase . $lowercase . $numbers . $specialCharacters;
        $remainingLength = $length - 4; // Deduct the characters already added from each pool

        for ($i = 0; $i < $remainingLength; $i++) {
            $password .= $allCharacters[rand(0, strlen($allCharacters) - 1)];
        }

        // Shuffle the password to randomize the order of characters
        return str_shuffle($password);
    }
}
