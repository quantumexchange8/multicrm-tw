<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentAccountRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\PaymentAccount;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\SettingCountry;
use Spatie\Activitylog\Models\Activity;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function detail(Request $request): Response
    {
        $countries = SettingCountry::all();
        $avatar = Auth::user()->getFirstMediaUrl('profile_photo');
        $frontIdentity = Auth::user()->getFirstMediaUrl('front_identity');
        $backIdentity = Auth::user()->getFirstMediaUrl('back_identity');

        return Inertia::render('Profile/ProfileDetail', [
            'status' => session('status'),
            'countries' => $countries,
            'avatar' => $avatar,
            'frontIdentity' => $frontIdentity,
            'backIdentity' => $backIdentity,
        ]);
    }

    public function edit(Request $request): Response
    {
        $countries = SettingCountry::all();
        $avatar = Auth::user()->getFirstMediaUrl('profile_photo');
        $frontIdentity = Auth::user()->getFirstMediaUrl('front_identity');
        $backIdentity = Auth::user()->getFirstMediaUrl('back_identity');

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'countries' => $countries,
            'avatar' => $avatar,
            'frontIdentity' => $frontIdentity,
            'backIdentity' => $backIdentity,
        ]);
    }

    public function payment_account(Request $request): Response
    {
        $countries = SettingCountry::get('name_en');
        $avatar = Auth::user()->getFirstMediaUrl('profile_photo');
        $frontIdentity = Auth::user()->getFirstMediaUrl('front_identity');
        $backIdentity = Auth::user()->getFirstMediaUrl('back_identity');

        return Inertia::render('Profile/PaymentAccount', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'countries' => $countries,
            'avatar' => $avatar,
            'frontIdentity' => $frontIdentity,
            'backIdentity' => $backIdentity,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        $description = '';

        if ($request->hasFile('avatar')) {
            $request->user()->clearMediaCollection('profile_photo');
            $request->user()->addMedia($request->avatar)->toMediaCollection('profile_photo');
            $description = 'has uploaded a profile photo';
        }

        if ($request->hasFile('front_identity')) {
            $request->user()->clearMediaCollection('front_identity');
            $request->user()->addMedia($request->front_identity)->toMediaCollection('front_identity');
            $description = ($description ? $description . ' and ' : '') . 'has uploaded the front identity document';
        }

        if ($request->hasFile('back_identity')) {
            $request->user()->clearMediaCollection('back_identity');
            $request->user()->addMedia($request->back_identity)->toMediaCollection('back_identity');
            $description = ($description ? $description . ' and ' : '') . 'has uploaded the back identity document';
        }

        if ($description) {
            Activity::create([
                'log_name' => 'user', // Specify the log name here
                'description' => Auth::user()->first_name . ' ' . $description,
                'subject_type' => User::class,
                'subject_id' => Auth::id(),
                'causer_type' => get_class(auth()->user()),
                'causer_id' => auth()->id(),
                'event' => 'updated',
            ]);
        }

        return Redirect::route('profile.detail')->with('toast', 'Successfully Updated Profile');
    }

    public function create_payment_account(PaymentAccountRequest $request)
    {
        $payment_platform = $request->payment_platform;

        if ($payment_platform == 'bank') {
            $paymentAccount = PaymentAccount::create([
                'user_id' => Auth::id(),
                'payment_platform' => $payment_platform,
                'payment_platform_name' => $request->payment_platform_name,
                'bank_branch_address' => $request->bank_branch_address,
                'payment_account_name' => $request->payment_account_name,
                'account_no' => $request->account_no,
                'bank_swift_code' => $request->bank_swift_code,
                'bank_code_type' => $request->bank_code_type,
                'bank_code' => $request->bank_code,
                'country' => $request->country,
                'currency' => $request->currency,
            ]);

            if ($request->hasFile('proof_of_bank')){
                $paymentAccount->addMedia($request->proof_of_bank)->toMediaCollection('proof_of_bank_account');
            }
        } else {
            PaymentAccount::create([
                'user_id' => Auth::id(),
                'payment_platform' => $payment_platform,
                'payment_platform_name' => $request->payment_platform_name,
                'payment_account_name' => $request->payment_account_name,
                'account_no' => $request->account_no,
                'currency' => 'USD'
            ]);
        }

        return to_route('profile.detail')->with('toast', 'Your payment account has been created successfully!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function getPaymentAccount()
    {
        $user = Auth::user();

        $bankAccounts = PaymentAccount::query()
            ->where('user_id', $user->id)
            ->where('payment_platform', '=', 'bank')
            ->latest()
            ->paginate(5);

        $cryptoAccounts = PaymentAccount::query()
            ->where('user_id', $user->id)
            ->where('payment_platform', '=', 'crypto')
            ->latest()
            ->paginate(5);

        return response()->json([
            'bankAccounts' => $bankAccounts,
            'cryptoAccounts' => $cryptoAccounts
        ]);
    }

    public function payment_delete(Request $request): RedirectResponse
    {
        $paymentAccount = PaymentAccount::find($request->id);

        $paymentAccount->delete();

        return redirect()->back()->with('toast', 'Your payment account has been deleted successfully!');
    }
}
