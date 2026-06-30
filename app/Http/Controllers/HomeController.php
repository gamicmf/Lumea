<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publication;
use App\Models\Group;
use App\Models\Challenge;
use App\Events\NotificationPusher;


class HomeController extends Controller
{
    public function index()
    {
        $latestPublications = Publication::orderBy('created_date', 'desc')->take(4)->get();
        $topGroups = Group::withCount('members')
            ->orderBy('members_count', 'desc')
            ->take(4)
            ->get()
            ->filter(function ($group) {
                return $group->public || auth()->check();
            });
        $topChallenges = Challenge::withCount('participants')->orderBy('participants_count', 'desc')->take(4)->get();

        return view('welcome', compact('latestPublications', 'topGroups', 'topChallenges'));
    }
}