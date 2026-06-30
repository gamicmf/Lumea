<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Group;
use App\Models\User;
use App\Models\Publication;
use App\Models\Challenge;
use App\Events\NotificationPusher;
use App\Models\Notification;
use App\Models\Notifications\GroupNotification;
use App\Models\Notifications\ChallengeNotification;


class ChallengeController extends Controller{
    public function index(){
        $user = Auth::user();

        $challenges = Challenge::all()->map(function ($challenge) {
            $challenge->num_participants = DB::table('challenge_participants')
            ->where('id_challenge', $challenge->id)
            ->count();
            return $challenge;
        })->filter(function ($challenge) use ($user) {
            if ($user) {
            return !$challenge->private || $user->isAdmin() || $challenge->id_creator == $user->id || DB::table('challenge_participants')
                ->where('id_challenge', $challenge->id)
                ->where('id_user', $user->id)
                ->exists();
            } else {
            return !$challenge->private;
            }
        });

        return view('challenges.index', compact('challenges'));
    }

    public function participating(){
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $challenges = Challenge::all()->map(function ($challenge) {
            $challenge->num_participants = DB::table('challenge_participants')
            ->where('id_challenge', $challenge->id)
            ->count();
            return $challenge;
        })->filter(function ($challenge) use ($user) {
            return DB::table('challenge_participants')
            ->where('id_challenge', $challenge->id)
            ->where('id_user', $user->id)
            ->exists();
        });

        return view('challenges.participating', compact('challenges'));
    }

    public function create(): View{
        if (!Auth::check()) {
            return view('auth.login');
        }
        if (!Auth::user()->isAdmin()) {
            $challenges = Challenge::all();
            abort(403, 'Not allowed to create a challenge.');
        }
        return view('challenges.create');
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:30',
            'description' => 'required|string|max:200',
            'end_date' => 'required|date|after:today',
            'max_participants' => 'required|integer|min:2|max:100',
            'private' => 'required|boolean',
        ]);

        $challenge = new Challenge();
        $challenge->id_creator = Auth::user()->id;
        $challenge->name = $request->input('name');
        $challenge->description = $request->input('description');
        $challenge->begin_date = now();
        $challenge->end_date = $request->input('end_date');
        $challenge->max_participants = $request->input('max_participants');
        $challenge->private = $request->input('private');
        $challenge->save();

        return redirect()->route('challenges.index');
    }

    public function createGroupChallenge($group_id): View{
        $group = Group::findOrFail($group_id);
        if (!Auth::check()) {
            return view('auth.login');
        }
        $user = Auth::user();
        $owner = DB::table('group_owner')
        ->join('users', 'group_owner.id_user', '=', 'users.id')
        ->where('group_owner.id_group', $group->id)
        ->select('users.id', 'users.username')
        ->first();
        if (!$user->isAdmin() && $owner->id != $user->id) {
            return redirect()->route('groups.show', $group->id)->with('error', 'Not allowed to create a challenge for this group.');
        }


        return view('challenges.create_group_challenge', compact('group'));
    }

    public function storeGroupChallenge(Request $request, $group_id){
        $group = Group::findOrFail($group_id);
        $user = Auth::user();
        $owner = DB::table('group_owner')
        ->join('users', 'group_owner.id_user', '=', 'users.id')
        ->where('group_owner.id_group', $group->id)
        ->select('users.id', 'users.username')
        ->first();
        if (!$user->isAdmin() && $owner->id != $user->id) {
            return redirect()->route('groups.show', $group->id)->with('error', 'Not allowed to create a challenge for this group.');
        }

        $request->validate([
            'name' => 'required|string|max:30',
            'description' => 'required|string|max:200',
        ]);
        
        $challenge = new Challenge();
        $challenge->id_creator = Auth::user()->id;
        $challenge->name = $request->input('name');
        $challenge->description = $request->input('description');
        $challenge->begin_date = now();
        $challenge->end_date = $request->input('end_date');
        $challenge->max_participants = $request->input('max_participants');
        $challenge->private = !$group->public;
        $challenge->save();
        DB::table('challenge_participants')->insert([
            'id_challenge' => $challenge->id,
            'id_user' => $user->id,
        ]);        
        DB::table('group_challenge')->insert([
            'id_group' => $group->id,
            'id_challenge' => $challenge->id,
        ]);

        $members= DB::table('group_member')
                ->where('id_group', $group->id)
                ->get();

        foreach($members as $member){
            if($member->id_user != Auth::id()){

                $notification = new Notification();
                $notification->emitter_user = Auth::id();
                $notification->received_user = $member->id_user;
                $notification->date = now();
                $notification->save();
                \Log::info($notification->id);

                $groupNotification = new GroupNotification();
                $groupNotification->id = $notification->id;
                $groupNotification->id_group = $group_id;
                $groupNotification->notification_type = 'created_challenge';
                $groupNotification->save();

                event(new NotificationPusher($notification->id, $notification->received_user));

            }
        }
        return redirect()->route('groups.show', $group->id);
    }

    public function show($id){
        $challenge = Challenge::findOrFail($id);

        $publications = Publication::with(['post', 'user'])
            ->where('id_challenge', $challenge->id)
            ->get();

        $isParticipatingInChallenge = false;
        $isParticipatingInGroup = false;
        if (Auth::check()) {
            $isParticipatingInChallenge = Publication::join('post', 'publications.id_post', '=', 'post.id')
                ->where('publications.id_challenge', $challenge->id)
                ->where('post.id_poster', Auth::id())
                ->exists();
        }
        $isGroupChallenge = DB::table('group_challenge')
            ->where('id_challenge', $challenge->id)
            ->exists();
        if ($isGroupChallenge) {    
            $isParticipatingInGroup = DB::table('group_member')
            ->where('id_group', $challenge->id_group) // Assuming each challenge belongs to a group
            ->where('id_user', Auth::id())
            ->exists();
        }
        else{
            $isParticipatingInGroup = true;
        }

        return view('challenges.show', compact('challenge', 'publications', 'isParticipatingInChallenge','isParticipatingInGroup'));
    }

    public function edit($id): View{
        $challenge = Challenge::findOrFail($id);
        $user = Auth::user();
        if (!$user->isAdmin() && $challenge->id_creator != $user->id) {
            return redirect()->route('challenges.index')->with('error', 'Not allowed to edit this challenge.');
        }
        return view('challenges.edit', compact('challenge'));
    }

    public function update(Request $request, $id){
        $challenge = Challenge::findOrFail($id);
        $user = Auth::user();
        if (!$user->isAdmin() && $challenge->id_creator != $user->id) {
            return redirect()->route('challenges.index')->with('error', 'Not allowed to edit this challenge.');
        }

        $request->validate([
            'name' => 'required|string|max:30',
            'description' => 'required|string|max:200',
            'end_date' => 'required|date|after:begin_date',
            'max_participants' => 'required|integer|min:2|max:100',
            'private' => 'required|boolean',
        ]);

        $challenge->name = $request->input('name');
        $challenge->description = $request->input('description');
        $challenge->end_date = $request->input('end_date');
        $challenge->max_participants = $request->input('max_participants');
        $challenge->private = $request->input('private');
        $challenge->save();

        return redirect()->route('challenges.show', $challenge->id)->with('success', 'Challenge updated successfully!');
    }
    public function delete($id){
        $challenge = Challenge::findOrFail($id);
        $user = Auth::user();
        if (!$user->isAdmin() && $challenge->id_creator != $user->id) {
            return redirect()->route('challenges.index')->with('error', 'Not allowed to delete this challenge.');
        }

        $groupChallenge = DB::table('group_challenge')->where('id_challenge', $id)->first();
        $challenge->delete();

        if ($groupChallenge) {
            return redirect()->route('groups.show', $groupChallenge->id_group)->with('success', 'Challenge deleted successfully!');
        }

        return redirect()->route('challenges.index')->with('success', 'Challenge deleted successfully!');
    }


    public function search(Request $request)
    {
        $query = $request->input('query', '');
    
        try {
            // Se a query estiver vazia, retorna todos os desafios
            if (empty($query)) {
                $challenges = Challenge::all(); // Retorna todos os desafios
            } else {
                // Pesquisa insensível a maiúsculas e minúsculas
                $challenges = Challenge::where('name', 'ILIKE', "%{$query}%") // PostgreSQL

                    ->get();
            }
    
            return response()->json($challenges);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function joinChallenge($id){
        $challenge = Challenge::findOrFail($id);
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if ($challenge->private && !$user->isAdmin() && $challenge->id_creator != $user->id) {
            return redirect()->route('challenges.show', $challenge->id)->with('error', 'Not allowed to join this challenge.');
        }
        if (DB::table('challenge_participants')
            ->where('id_challenge', $challenge->id)
            ->where('id_user', $user->id)
            ->exists()) {
            return redirect()->route('challenges.show', $challenge->id)->with('error', 'Already participating in this challenge.');
        }
        DB::table('challenge_participants')->insert([
            'id_challenge' => $challenge->id,
            'id_user' => $user->id,
        ]);

        $notification = new Notification();
        $notification->emitter_user = Auth::id();
        $notification->received_user = $challenge->id_creator;
        $notification->date = now();
        $notification->save();
        \Log::info($notification->id);
        $challengeNotification = new ChallengeNotification();
        $challengeNotification->id = $notification->id;
        $challengeNotification->id_challenge = $challenge->id;
        $challengeNotification->notification_type = 'joined_challenge';
        $challengeNotification->save();

        event(new NotificationPusher($notification->id, $notification->received_user));

        return redirect()->route('challenges.show', $challenge->id)->with('success', 'Successfully joined the challenge!');
    }

        
}
