<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Faq;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Events\NotificationPusher;


use Carbon\Carbon;

class StaticPagesController extends Controller
{
    public function faq()
    {
        $admins = User::whereIn('id', Admin::pluck('id_user'))->get();
        return view('faq', compact('admins'));
    }

    public function about()
    {
        $admins = User::whereIn('id', Admin::pluck('id_user'))->get();
        return view('about', compact('admins'));
    }

    public function sendQuestion(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'You need to log in first to send a question to the admins.');
        }

        $user = Auth::user();
        $lastQuestion = Faq::where('id_user', $user->id)->orderBy('created_at', 'desc')->first();

        if ($lastQuestion) {
            $minutesSinceLastQuestion = Carbon::parse($lastQuestion->created_at)->diffInMinutes(Carbon::now());
            if ($minutesSinceLastQuestion < 10) {
                $remainingMinutes = 10 - $minutesSinceLastQuestion;
                return redirect()->back()->with('error', "You can send another question in $remainingMinutes minutes.");
            }
        }

        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Invalid question.');
        }

        Faq::create([
            'id_user' => $user->id,
            'question' => $request->question,
        ]);

        return redirect()->back()->with('success', 'Your question was successfully sent.');
    }
}