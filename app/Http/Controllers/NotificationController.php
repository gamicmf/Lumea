<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Notifications\GroupNotification;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Events\NotificationPusher;


class NotificationController extends Controller
{

    public function rejectInvite($groupId)
    {
        $user = Auth::user();
        $group = Group::findOrFail($groupId);

        // Remover a notificação
        GroupNotification::whereHas('notification', function ($query) use ($user) {
            $query->where('received_user', $user->id);
        })->where('id_group', $group->id)->delete();

        // Remover a entrada na tabela invite_member
        DB::table('invite_member')
            ->where('id_group', $group->id)
            ->where('id_user', $user->id)
            ->delete();

        return redirect()->to(url()->previous())->with('success', 'You have rejected the invite.');
    }

    

    public function markAsViewed($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->viewed = true;
        $notification->save();

        return response()->json(['success' => true]);
    }
}
