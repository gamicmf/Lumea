<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification; 
use App\Models\Notifications\UserNotification;
use Illuminate\Support\Facades\DB;
use App\Models;
use App\Models\Report;
use App\Models\FollowRequest;
use App\Models\Challenge;
use App\Models\Group;
use App\Models\Publication;
use App\Events\NotificationPusher;



class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the authenticated user's profile.
     *
     * @return \Illuminate\View\View
     */
   
    public function show()
    {
        // Retrieve the currently authenticated user
        $user = Auth::user();

        $isAdmin = DB::table('administrator')->where('id_user', $user->id)->exists();

        $publications = $user->publications()->with('post.user')->orderBy('created_date', 'desc')->get();
        $notifications = $user->notification()->orderBy('date', 'desc')->get();

        // Pass the user data to the profile view
        return view('profile.show', compact('user', 'publications', 'notifications', 'isAdmin'));
    }
    
    public function showProfileByUsername($username)
    {
        $user = User::where('username', $username)->firstOrFail();
    
        // Verificar se o perfil do usuário é privado
        $isPrivate = !$user->public; // Se 'public' for FALSE, o perfil é privado
    
        // Verificar se o usuário é admin (se necessário para lógica)
        $isAdmin = DB::table('administrator')->where('id_user', $user->id)->exists();
    
        // Se o perfil for privado e não for o usuário logado ou um admin, não mostrar publicações
        if ($isPrivate && Auth::user()->id !== $user->id && Auth::check() && !Auth::user()->isAdmin()){
            $publications = null; // Não carrega as publicações para perfis privados
        } else {
            // Se o perfil for público ou o usuário está logado ou é admin, carrega as publicações
            $publications = $user->publications()->with('post.user')->orderBy('created_date', 'desc')->get();
        }
    
        return view('profile.show_other', compact('user', 'publications', 'isAdmin', 'isPrivate'));
    }
    

    public function edit($id){
        $user = User::findOrFail($id); // Fetch the target user by ID
        

        return view('profile.mudar', compact('user'));
    }

    
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id); // Fetch the target user by ID
        

        // Validate the request data
        $validatedData = $request->validate([
            'username' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'birthdate' => 'nullable|date',
            'public' => 'nullable|boolean',
            'description' => 'nullable|string|max:500',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        // Update user data
        $user->name = $validatedData['name'];
        $user->username = $validatedData['username'];
        $user->birthdate = $validatedData['birthdate'];
        $user->public = $validatedData['public'] ?? $user->public;
        if (!empty($validatedData['password'])) {
            $user->password = bcrypt($validatedData['password']);
        }
        $user->email = $validatedData['email'];
        $user->description = $validatedData['description'] ?? $user->description;
        //Handle the profile picture
        if ($request->hasFile('profile_picture')) {
            // Store the new profile picture in the storage folder
            $path = $request->file('profile_picture')->store('public/profile_pictures');
            $user->profile_picture = basename($path);
        }
        $user->save();

        return redirect()->route('profile.show', ['id' => $user->id])->with('success', 'Profile updated successfully!');
    }

    public function reportProfile(Request $request, $id)
    {
        $request->validate([
            'reportable_id' => 'required|integer',
            'reportable_type' => 'required|string',
            'description' => 'required|string|max:255',        ]);

        $report = new Report();
        $report->id_user = Auth::id();
        $report->reportable_id = $id;
        $report->reportable_type = 'user';
        $report->description = $request->input('description');
        $report->created_at = now();
        $report->updated_at = now();

        $report->save();

        return redirect()->back()->with('success', 'User reported successfully!');
    }



    public function follow($id)
    {
        $userToFollow = User::findOrFail($id);
        $currentUser = Auth::user();

        if ($currentUser->isFollowing($userToFollow)==FALSE) {

            $notification = new Notification();
            $notification->received_user = $userToFollow->id;
            $notification->emitter_user = $currentUser->id;
            $notification->date = now();
            $notification->save();

            event(new NotificationPusher($notification->id, $notification->received_user));


            if($userToFollow->public==FALSE){
                
                $followRequest= DB::table('follow_request')->insert([
                    'id_follower' => $currentUser->id,
                    'id_followed' => $userToFollow->id,
                ]);

                $userNotification= new UserNotification();
                $userNotification->id = $notification->id;
                $userNotification->notification_type='request_follow';
                $userNotification->save();

            }
            else{
                $follower= DB::table('followers')->insert([
                    'user_id' => $userToFollow->id,
                    'follower_id' => $currentUser->id,
                ]);
                \Log::info('User followed successfully', ['follower_id' => $currentUser->id, 'user_id' => $userToFollow->id]);
                $userNotification =new UserNotification();
                $userNotification->id = $notification->id;
                $userNotification->notification_type='started_following';
                $userNotification->save(); 
            }
        

        }
        return redirect()->route('profile.showByUsername', ['username' => $userToFollow->username])->with('success', 'You are now following ' . $userToFollow->username);
    }

    
    public function unfollow($user_following, $user_followed)
    {
        $userToUnfollow = User::findOrFail($user_followed);
        $userFollowing =  User::findOrFail($user_following);

        // Check if the current user is following the user to unfollow
        if ($userFollowing->isFollowing($userToUnfollow)) {
            $userFollowing->following()->detach($userToUnfollow->id);
            \Log::info('User unfollowed successfully', ['follower_id' => $userFollowing->id, 'user_id' => $userToUnfollow->id]);
        } else {
            \Log::info('User is not following', ['follower_id' => $userFollowing->id, 'user_id' => $userToUnfollow->id]);
        }
        

        return redirect()->route('profile.showByUsername', ['username' => $userToUnfollow->username])->with('success', 'You have unfollowed ' . $userToUnfollow->username);
    }

    public function followers($id)
    {
        $user = User::findOrFail($id);
        $followers = DB::table('followers')
            ->join('users', 'followers.follower_id', '=', 'users.id')
            ->where('followers.user_id', $user->id)
            ->select('users.*')
            ->get();
    
        return view('profile.followers', compact('user', 'followers'));
    }

    public function following($id)
    {
        $user = User::findOrFail($id);
        $following = $user->following()->get();
    
        return view('profile.following', compact('user', 'following'));
    }
    public function notifications(){
        // Retrieve the notifications for the authenticated user
        $notifications = Notification::with([
            'emitter',                 // Load emitter user details
            'userNotification',       // Load UserNotification details
            'commentNotification',    // Load CommentNotification details
            'publicationNotification', // Load PublicationNotification details
            'challengeNotification',   // Load ChallengeNotification details
            'groupNotification'        // Load GroupNotification details
        ])
        ->where('receive_user', Auth::id()) // Filter notifications for the logged-in user
        ->orderBy('date', 'desc')          // Order by latest notifications
        ->get();

        // Pass the notifications data to the notifications view
        return view('profile.notifications', compact('notifications'));
    }
    public function acceptFollow($id_followed,$id_follower, $notification_id){

        $follower =User::findOrFail($id_follower);
        $followed = User::findOrFail($id_followed);
        
        if($follower->isFollowing($followed)){
            return redirect()->route('profile.notifications', $followed->id)->with('error', 'You do not have permission to edit this group.');
        }
        DB::table('followers')->insert([
            'user_id'=>$followed->id,
            'follower_id'=>$follower->id,
        ]);

        //\Log::info('User followed successfully', ['follower_id' => $currentUser->id, 'user_id' => $userToFollow->id]);

        $notification = Notification::findOrFail($notification_id);
        $userNotification=UserNotification::where('id', $notification_id)->first();

        DB::table('follow_request')->where('id_follower', $follower->id)->where('id_followed',$followed->id )->delete();

        if($userNotification){
            $userNotification->notification_type='accepted_follow';
            $userNotification->save();
        }

        return redirect()->route('profile.notifications')->with('success', 'Follow request accepted.');        
    }

    public function rejectFollow($id_follower, $id_followed, $notification_id){

        $userHOWFollow =$id_follower;
        
        $currentUser =  $id_followed;

        $notification = Notification::where('received_user', $currentUser->id)->where('emitter_user', $userHOWFollow->id)->first();
        $userNotification=UserNotification::where('id', $notification->id)->first();

        if($userNotification){
            $userNotification->delete();
        }

        $notification->delete();

        DB::table('follow_request')->where('id_follower', $userHOWFollow->id)->where('id_followed',$currentUser->id )->delete();

        return redirect()->route('profile.notifications')->with('success', 'Follow request rejected.');
    }

}
