<?php

namespace App\Http\Controllers;

use constGuards;
use constDefault;

use Carbon\Carbon;
use constDefaults;
use App\Models\Admin;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

// use App\Http\helpers\helpers;

class AdminController extends Controller
{
    public function loginHandler(Request $request)
    {
        $fieldType = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if ($fieldType == 'email') {
            $request->validate([
                'login_id' => 'required|email|exists:admins,email',
                'password' => 'required|min:5|max:45'

            ], [
                'login_id.required' => 'Email or Username is required',
                'login_id.email' => 'Invalid email address',
                'login_id.exists' => 'Email is not exists in system',
                'password.required' => 'Password is required',

            ]);
        } else {
            $request->validate([
                'login_id' => 'required|exists:admins,username',
                'password' => 'required|min:5|max:45'
            ], [
                'login_id.required' => 'Email or Username is required',

                'login_id.exists' => 'Username is not exists in system',
                'password.required' => 'Password is required',

            ]);
        }

        $creds = array(
            $fieldType => $request->login_id,
            'password' => $request->password
        );
        if (Auth::guard('admin')->attempt($creds)) {
            notify()->success('Welcome to our platform');
            return redirect()->route('admin.home');
        } else {
            notify()->error('Incorrect credentials');

            // session()->flash('fail', 'Incorrect credentials');
            return redirect()->route('admin.login');
        }
    }


    public function logoutHandler(Request $request)
    {
        // die('ee');
        Auth::guard('admin')->logout();
        // session()->flash('fail', 'You are logged out!');
        notify()->info('You are logged out!');
        return redirect()->route('admin.login');
    }


    public function sendPasswordResetLink(Request $request)
    {
        try {
            // phpinfo();
            // dd($request->all());
            $request->validate(
                [
                    'email' => 'required|email|exists:admins,email',
                ],
                [
                    'email.required' => 'Email  is required',
                    'email.email' => 'Invalid email address',
                    'login_id.exists' => 'Email is not exists in system',
                    'password.required' => 'Password is required',

                ]
            );

            //GET ADMIN DETAILS
            $admin = Admin::where('email', $request->email)->first();

            // GENERATE TOKEN
            $token = base64_encode(Str::random(64));

            // CHECK IF THERE IS AN EXISTING RESET PASSWORD TOKEN
            $oldToken = DB::table('password_reset_tokens')
                ->where(['email' => $request->email, 'guard' => constGuards::ADMIN])
                ->first();

            if ($oldToken) {
                // UPDATE TOKEN
                DB::table('password_reset_tokens')
                    ->where(['email' => $request->email, 'guard' => constGuards::ADMIN])
                    ->update([
                        'token' => $token,
                        'created_at' => Carbon::now()
                    ]);
            } else {
                // CREATE NEW TOKEN
                DB::table('password_reset_tokens')
                    ->insert([
                        'email' => $request->email,
                        'guard' => constGuards::ADMIN,
                        'token' => $token,
                        'created_at' => Carbon::now()
                    ]);
            }
            $actionLink = route('admin.reset-password', ['token' => $token, 'email' => $request->email]);
            $data = array(
                'actionLink' => $actionLink,
                'admin' => $admin
            );
            $mail_body = view('email-template.admin-forgot-email-template', $data)->render();
            $mail_config = array(
                'mail_from_email' => env('EMAIL_FROM_ADDRESS'),
                'mail_from_name' => env('EMAIL_FROM_NAME'),
                'mail_recipient_email' => $request->email,
                'mail_recipient_name' => $admin->name,
                'mail_subject' => 'Reset password',
                'mail_body' => $mail_body,
            );

            if (sendEmail($mail_config)) {

                // session()->flash('success', 'We have e-mailed your password reset link.');
                notify()->success('We have e-mailed your password reset link.');
                // return redirect()->route('admin.forget-password');
                return view('back.pages.admin.auth.forget-password');
            } else {
                notify()->error('Something went wrong!');
                // session()->flash('fail', 'Something went wrong!');

                return redirect()->route('admin.forget-password');
            }
        } catch (\Exception $e) {
            // session()->flash('fail', $e->getMessage());
            notify()->error('Something went wrong!');

            return redirect()->route('admin.forget-password');
            // $e->getMessage();
        }
    }

    public function resetPassword(Request $request, $token = null)
    {
        $checkToken = DB::table('password_reset_tokens')
            ->where(['token' => $token, 'guard' => constGuards::ADMIN])
            ->first();

        if ($checkToken) {
            //CHECK IF TOKEN NOT EXPIRED

            $diffMins = Carbon::createFromFormat('Y-m-d H:i:s', $checkToken->created_at)->diffInMinutes(Carbon::now());
            // dd($diffMins);
            if ($diffMins > constDefault::tokenExpireMinnutes) {
                session()->flash('fail', 'token expired,request another reset password link');
                return redirect()->route('admin.forget-password', ['token' => $token]);
            } else {
                return view('back.pages.admin.auth.reset-password')->with(['token' => $token]);
            }
        } else {
            session()->flash('fail', 'Invalid token!,request another reset password link');
            return redirect()->route('admin.forget-password', ['token' => $token]);
        }
    }


    public function resetPasswordHandler(Request $request)
    {
        // dd($request->all());
        $request->validate([

            'new_password' => 'required|min:5|max:45|required_with:confirm_new_password|same:confirm_new_password',

            'confirm_new_password' => 'required'

        ], [
            'new_password.required' => 'New password is required',
            'new_password.min' => 'New password minimun character 4',
            'new_password.max' => 'New password maxmun character 45',
            'confirm_new_password.required' => 'Confirm new password is required',

        ]);

        $token = DB::table('password_reset_tokens')
            ->where(['token' => $request->token, 'guard' => constGuards::ADMIN])
            ->first();

        //GET ADMIN DETAILS
        $admin = Admin::where('email', $token->email)->first();

        // UPDATE ADMIN PASSWORD
        Admin::where('email', $admin->email)->update([
            'password' => Hash::make($request->new_password)
        ]);

        //DELETE TOKEN RECORD 

        DB::table('password_reset_tokens')
            ->where([
                'email' => $admin->email,
                'token' => $request->token,
                'guard' => constGuards::ADMIN
            ])
            ->delete();
        //send email to notify admin

        $data = array(
            'admin' => $admin,
            'new_password' => $request->new_password,
        );

        $mail_body = view('email-template.admin-reset-password-template', $data)->render();

        $mail_config = array(
            'mail_from_email' => env('EMAIL_FROM_ADDRESS'),
            'mail_from_name' => env('EMAIL_FROM_NAME'),
            'mail_recipient_email' => $admin->email,
            'mail_recipient_name' => $admin->name,
            'mail_subject' => 'Password changed',
            'mail_body' => $mail_body,
        );

        sendEmail($mail_config);

        return redirect()->route('admin.login')->with('success', 'Done!, Your password has been changed. Use new password to login into system.');
    }
}
