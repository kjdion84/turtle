<?php

namespace Kjdion84\Turtle\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

class AppController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('dashboard');
        $this->middleware('allow:contact')->only(['contactForm', 'contact']);
    }

    // determine index route/view
    public function index()
    {
        if (config('turtle.allow.frontend')) {
            return $this->home();
        }
        else if (auth()->guest()) {
            return redirect()->route('login');
        }
        else {
            return redirect()->route('dashboard');
        }
    }

    // redirect /home to index
    public function indexRedirect()
    {
        return redirect()->route('index');
    }

    // show home view
    public function home()
    {
        return view('turtle::home');
    }

    // show dashboard view
    public function dashboard()
    {
        return view('turtle::dashboard');
    }

    // show delete confirmation modal
    public function deleteModal($route, $id)
    {
        return view('turtle::delete', compact('route', 'id'));
    }

    // show contact form
    public function contactForm()
    {
        return view('turtle::contact');
    }

    // send contact email
    public function contact()
    {
        $this->shellshock(request(), [
            'name' => 'required',
            'email' => 'required|email',
            'enquiry' => 'required',
            'g-recaptcha-response' => 'sometimes|recaptcha',
        ]);

        Mail::send(['text' => 'turtle::emails.contact'], request()->all(), function (Message $message) {
            $message->subject(config('app.name') . ' Contact');
            $message->to(config('mail.from.address'));
            $message->replyTo(request()->input('email'));
        });

        flash('success', 'Message sent!');

        return response()->json(['reload_page' => true]);
    }
}