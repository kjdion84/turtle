<?php

namespace Kjdion84\Turtle\Traits;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait Shellshock
{
    public function shellshock(Request $request, $rules, $allow_demo = false, $billing_model = null)
    {
        $billing_limit = (auth()->check() && auth()->user()->billable) ? config('turtle.billing.plans.'.auth()->user()->billing_plan.'.limits.'.$billing_model) : null;

        if (config('turtle.demo_mode') && !$allow_demo) {
            // stop request if in demo mode
            throw new HttpResponseException(response()->json([
                'message' => 'Feature disabled in demo.',
            ], 422));
        }
        else if ($billing_limit && app($billing_model)->get()->count() >= $billing_limit) {
            // stop request if billing plan limit reached
            throw new HttpResponseException(response()->json([
                'message' => 'Billing plan limit reached! Please upgrade to a higher plan.',
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