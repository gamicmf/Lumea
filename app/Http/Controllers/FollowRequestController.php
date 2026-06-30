<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\Notifications\AdminNotification;
use App\Models\Admin;
use App\Models\FollowRequest;
use App\Events\NotificationPusher;



class FollowRequestController extends Controller
{

    public function showRequest($id)
    {
        $user = User::findOrFail($id);
        $authUser = Auth::user();

        if(!authUser){
            abort(403,'Unauthorized action.');
        }
        $pendingFollowRequest = FollowRequest::where('id_follower', $authUser->id)
                                            ->where('id_followed', $user->id)
                                            ->exists();

        return view('profile.show_other', compact('user', 'pendingFollowRequest'));
    }

    public function destroy(Request $request)
    {
        

        $followRequest = DB::table('follow_requests')
            ->where('id_follower', $request->id_follower)
            ->where('id_followed', $request->id_followed)
            ->first();
        
        if ($followRequest) {
            $followRequest->delete();

            return redirect()->back()->with('success', 'Follow request canceled.');
        }

        return redirect()->back()->with('error', 'You are not authorized to perform this action.');
    }


}