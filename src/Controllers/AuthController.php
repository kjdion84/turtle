<?php

namespace Kjdion84\Turtle\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use GrahamCampbell\Throttle\Facades\Throttle;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Stripe\Customer;
use Stripe\Stripe;
use Stripe\Subscription;
use Stripe\Token;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->only(['loginForm', 'login', 'registerForm', 'register', 'passwordEmailForm', 'passwordEmail', 'passwordResetForm', 'passwordReset']);
        $this->middleware('auth')->only(['logout', 'profileForm', 'profile', 'passwordChangeForm', 'passwordChange', 'billing', 'billingPlanModal', 'billingPlan', 'billingCancelModal', 'billingCancel']);
        $this->middleware('allow:registration')->only(['registerForm', 'register']);
        $this->middleware('allow:billing')->only(['billing', 'billingPlanModal', 'billingPlan', 'billingCancelModal', 'billingCancel', 'billingWebhook']);
    }

    // show login form
    public function loginForm()
    {
        return view('turtle::auth.login');
    }

    // login
    public function login()
    {
        $this->shellshock(request(), [
            'email' => 'required|email',
            'password' => 'required',
        ], true);

        $throttler = Throttle::get(request()->instance(), 5, 1);

        if (auth()->guard()->attempt(request()->only(['email', 'password']), request()->has('remember')) && $throttler->check()) {
            $throttler->clear();
            request()->session()->regenerate();

            activity('Logged In');
            flash('success', 'Logged in!');

            return response()->json(['redirect' => request()->session()->pull('url.intended', route('index'))]);
        }
        else if (!$throttler->check()) {
            return response()->json(['errors' => ['email' => ['Too many failures, try again in one minute.']]], 422);
        }
        else {
            $throttler->attempt();

            return response()->json(['errors' => ['email' => [trans('auth.failed')]]], 422);
        }
    }

    // logout
    public function logout()
    {
        activity('Logged Out');
        
        auth()->guard()->logout();
        request()->session()->invalidate();

        return redirect()->route('index');
    }

    // show registration form
    public function registerForm()
    {
        return view('turtle::auth.register');
    }

    // register account
    public function register()
    {
        $this->shellshock(request(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'g-recaptcha-response' => 'sometimes|recaptcha',
        ]);

        // hash password
        request()->merge(['password' => Hash::make(request()->input('password'))]);

        // set billing if allowed
        if (config('turtle.allow.billing')) {
            request()->merge([
                'billable' => true,
                'billing_trial_ends' => Carbon::createFromTimestamp(strtotime(config('turtle.billing.trial_period'))),
            ]);
        }

        $user = app(config('turtle.models.user'))->create(request()->all());
        event(new Registered($user));
        auth()->guard()->login($user);

        activity('Registered Account');
        flash('success', 'Account registered!');

        return response()->json(['redirect' => route('index')]);
    }

    // show profile edit form
    public function profileForm()
    {
        return view('turtle::auth.profile');
    }

    // edit profile
    public function profile()
    {
        $this->shellshock(request(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . auth()->user()->id,
            'timezone' => 'required|in:' . implode(',', timezone_identifiers_list()),
        ]);

        auth()->user()->update(request()->all());

        activity('Edited Profile');
        flash('success', 'Profile edited!');

        return response()->json(['reload_page' => true]);
    }

    // show password reset link email form
    public function passwordEmailForm()
    {
        return view('turtle::auth.password.email');
    }

    // email password reset link
    public function passwordEmail()
    {
        $this->shellshock(request(), [
            'email' => 'required|email',
        ]);

        if (($user = app(config('turtle.models.user'))->where('email', request()->input('email'))->first())) {
            $token = Password::getRepository()->create($user);

            Mail::send(['text' => 'turtle::emails.password'], ['token' => $token], function (Message $message) use ($user) {
                $message->subject(config('app.name') . ' Password Reset Link');
                $message->to($user->email);
            });

            flash('success', 'Password reset link emailed!');

            return response()->json(['reload_page' => true]);
        }
        else {
            return response()->json(['errors' => ['email' => [trans('auth.failed')]]], 422);
        }
    }

    // show password reset form
    public function passwordResetForm($token)
    {
        return view('turtle::auth.password.reset', compact('token'));
    }

    // reset password
    public function passwordReset()
    {
        $this->shellshock(request(), [
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        $response = Password::broker()->reset(request()->except('_token'), function ($user, $password) {
            $user->password = Hash::make($password);
            $user->setRememberToken(Str::random(60));
            $user->save();
            event(new PasswordReset($user));
            auth()->guard()->login($user);
        });

        if ($response == Password::PASSWORD_RESET) {
            activity('Reset Password');
            flash('success', 'Password reset!');

            return response()->json(['redirect' => route('index')]);
        }
        else {
            return response()->json(['errors' => ['email' => [trans($response)]]], 422);
        }
    }

    // show password change form
    public function passwordChangeForm()
    {
        return view('turtle::auth.password.change');
    }

    // change password
    public function passwordChange()
    {
        $this->shellshock(request(), [
            'current_password' => 'required',
            'password' => 'required|confirmed',
        ]);

        if (Hash::check(request()->input('current_password'), auth()->user()->password)) {
            auth()->user()->update(['password' => Hash::make(request()->input('password'))]);

            activity('Changed Password');
            flash('success', 'Password changed!');

            return response()->json(['reload_page' => true]);
        }
        else {
            return response()->json(['errors' => ['current_password' => [trans('auth.failed')]]], 422);
        }
    }

    // show billing
    public function billing()
    {
        return view('turtle::auth.billing.index');
    }

    // show billing plan payment modal
    public function billingPlanModal($key)
    {
        return view('turtle::auth.billing.plan', compact('key'));
    }

    // show billing plan payment modal
    public function billingPlan($key)
    {
        $this->shellshock(request(), [
            'number' => 'required|numeric',
            'exp_month' => 'required|numeric',
            'exp_year' => 'required|numeric',
            'cvc' => 'required|numeric',
        ]);

        Stripe::setApiKey(config('turtle.billing.stripe_secret_key'));

        // create card token
        $token = Token::create([
            'card' => [
                'number' => request()->input('number'),
                'exp_month' => request()->input('exp_month'),
                'exp_year' => request()->input('exp_year'),
                'cvc' => request()->input('cvc'),
            ],
        ]);

        // create/update customer
        if (!auth()->user()->billing_customer) {
            $customer = Customer::create([
                'source' => $token->id,
                'email' => auth()->user()->email,
            ]);
        }
        else {
            $customer = Customer::retrieve(auth()->user()->billing_customer);
            $customer->source = $token->id;
            $customer->save();
        }

        // create/update subscription
        if (!auth()->user()->billing_subscription) {
            $subscription = Subscription::create([
                'customer' => $customer->id,
                'items' => [['plan' => $key]],
            ]);
        }
        else {
            $subscription = Subscription::retrieve(auth()->user()->billing_subscription);
            Subscription::update($subscription->id, [
                'items' => [[
                    'id' => $subscription->items->data[0]->id,
                    'plan' => $key,
                ]],
            ]);
        }

        // update user
        auth()->user()->update([
            'billing_customer' => $customer->id,
            'billing_subscription' => $subscription->id,
            'billing_plan' => $key,
            'billing_cc_last4' => $token->card->last4,
            'billing_period_ends' => Carbon::createFromTimestamp($subscription->current_period_end),
        ]);

        activity('Subscribed to '.config('turtle.billing.plans.'.$key.'.name'));
        flash('success', 'Thanks for subscribing!');

        return response()->json(['reload_page' => true]);
    }

    // show billing cancel modal
    public function billingCancelModal()
    {
        return view('turtle::auth.billing.cancel');
    }

    // cancel current subscription
    public function billingCancel()
    {
        Stripe::setApiKey(config('turtle.billing.stripe_secret_key'));

        // cancel subscription
        Subscription::retrieve(auth()->user()->billing_subscription)->cancel();

        // update user
        auth()->user()->update([
            'billing_subscription' => null,
            'billing_plan' => null,
            'billing_cc_last4' => null,
            'billing_period_ends' => null,
        ]);

        activity('Cancelled Subscription');
        flash('success', 'Subscription cancelled!');

        return response()->json(['reload_page' => true]);
    }

    // handle subscription payments
    public function billingWebhook()
    {
        if (request()->input('type') == 'invoice.payment_succeeded') {
            $user = app(config('turtle.models.user'))->where('billing_customer', request()->input('data.object.customer'))->whereNotNull('billing_plan')->first();

            if ($user) {
                $user->update(['billing_period_ends' => Carbon::createFromTimestamp(request()->input('data.object.lines.data.0.period.end'))]);
                $user->billing()->create([
                    'user_id' => $user->id,
                    'plan_name' => request()->input('data.object.lines.data.0.plan.name'),
                    'amount' => number_format(request()->input('data.object.total') / 100, 2, '.', ' ').' '.strtoupper(request()->input('data.object.currency')),
                    'cc_last4' => $user->billing_cc_last4,
                ]);
            }
        }

        return response('Success');
    }
}