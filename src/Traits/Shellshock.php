<?php

namespace Kjdion84\Turtle\Traits;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait Shellshock
{
    public function shellshock(Request $request, $rules, $allow_demo = false, $billing_model = null)
    {
        // set billing limit
        if (auth()->check() && auth()->user()->billable && $billing_model) {
            if (auth()->user()->billingTrial()) {
                // user in trial mode
                $billing_limit = config('turtle.billing.trial.limits.'.$billing_model);
                $billing_error = 'Free trial limit reached! Please select a billing plan.';
            }
            else if (auth()->user()->billingActive()) {
                // user has active plan
                $billing_limit = config('turtle.billing.plans.'.auth()->user()->billing_plan.'.limits.'.$billing_model);
                $billing_error = 'Billing plan limit reached! Please upgrade to a higher plan.';
            }
            else {
                // user is inactive
                $billing_limit = 0;
                $billing_error = 'Account inactive! Please select a billing plan.';
            }
        }
        else {
            // user is not billable
            $billing_limit = null;
        }

        if (config('turtle.demo_mode') && !$allow_demo) {
            // stop request if in demo mode
            throw new HttpResponseException(response()->json([
                'message' => 'Feature disabled in demo.',
            ], 422));
        }
        else if (isset($billing_error) && app($billing_model)->get()->count() >= $billing_limit) {
            // stop request if billing plan inactive or limit reached
            throw new HttpResponseException(response()->json([
                'message' => $billing_error,
            ], 422));
        }
        else {
            // validate request, throwing errors if invalid
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw new HttpResponseException(response()->json([
                    'message' => 'Errors have occurred.',
                    'errors' => $validator->errors(),
                ], 422));
            }
        }
    }
}