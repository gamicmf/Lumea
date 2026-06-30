<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Group;
use App\Models\User;
use App\Models\Message;
use App\Models\Challenge;
use App\Models\Notification;
use App\Models\Notifications\GroupNotification;
use App\Events\NotificationPusher;

class GroupController extends Controller

{
    
    public function search(Request $request)
    {
        $query = $request->input('query', '');
    
        try {
            if (empty($query)) {
                // Retorna todos os grupos se a consulta estiver vazia
                $groups = Group::all();
            } else {
                // Pesquisa insensível a maiúsculas e minúsculas
                $groups = Group::where('name', 'ILIKE', "%{$query}%") // PostgreSQL
                    ->orWhere('description', 'ILIKE', "%{$query}%")
                    ->get();
            }
    
            return response()->json($groups);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    

    public function index()
    {
        $userId = Auth::id();
        $groups = Group::with(['members', 'owner'])
            ->orderBy('creation_date', 'desc')
            ->get();

        foreach ($groups as $group) {
            $group->num_participants = $group->members->count();
            $group->is_member = $group->members->contains('id', $userId);
            $group->owner = DB::table('group_owner')
                ->join('users', 'group_owner.id_user', '=', 'users.id')
                ->where('group_owner.id_group', $group->id)
                ->select('users.id', 'users.username')
                ->first();
        }

        return view('groups.index', compact('groups'));
    }

    public function create(): View
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:30',
            'description' => 'required|string|max:200',
            'public' => 'required|boolean',
            'max_participants' => 'required|integer|min:2|max:100',
            'group_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $group = new Group();
        $group->name = $request->input('name');
        $group->description = $request->input('description');
        $group->public = $request->input('public');
        $group->max_participants = $request->input('max_participants');
        $group->creation_date = now();
        
        if ($request->hasFile('group_image')) {
            $imageName = 'group_' . time() . '.' . $request->group_image->extension();
            $request->group_image->storeAs('public/images/group_images', $imageName);
            $group->image = $imageName;
        }
        else{
            $group->image = 'default_group.png';
        }

        $group->save();

        // Adicionar o usuário autenticado como owner do grupo
        DB::table('group_owner')->insert([
            'id_group' => $group->id,
            'id_user' => Auth::id(),
        ]);

        return redirect()->route('groups.show', ['id' => $group->id, 'group_image' => $imageName])->with('success', 'Group created successfully!');
    }

    public function show($id)
    {
        $group = Group::findOrFail($id);
        $group->num_participants = DB::table('group_member')
            ->where('id_group', $group->id)
            ->count();
        $challenges = Challenge::join('group_challenge', 'group_challenge.id_challenge', '=', 'challenge.id')
            ->where('group_challenge.id_group', $group->id)
            ->select('challenge.*')
            ->get();
        $owner = DB::table('group_owner')
            ->join('users', 'group_owner.id_user', '=', 'users.id')
            ->where('group_owner.id_group', $group->id)
            ->select('users.id', 'users.username')
            ->first();

        // Buscar os membros do grupo
        $members = DB::table('group_member')
            ->join('users', 'group_member.id_user', '=', 'users.id')
            ->where('group_member.id_group', $group->id)
            ->select('users.id', 'users.username')
            ->get();
            

        $group_image = $group->image ?? 'default_group.png';

        $requests = DB::table('group_join_request')
        ->join('users', 'group_join_request.id_user', '=', 'users.id')
        ->where('group_join_request.id_group', $id)
        ->select('users.id', 'users.username')
        ->get();

        return view('groups.show', compact('group', 'owner', 'challenges','members', 'group_image', 'requests'));
    }

    public function edit($id)
    {
        $group = Group::findOrFail($id);

        $owner = DB::table('group_owner')
            ->join('users', 'group_owner.id_user', '=', 'users.id')
            ->where('group_owner.id_group', $group->id)
            ->select('users.id', 'users.username')
            ->first();
        if (!Auth::check() || (!Auth::user()->isAdmin() && $owner->id != Auth::id())) {
            return redirect()->route('groups.show', $id)->with('error', 'You do not have permission to edit this group.');
        }
        // Buscar os membros do grupo
        $members = DB::table('group_member')
            ->join('users', 'group_member.id_user', '=', 'users.id')
            ->where('group_member.id_group', $group->id)
            ->select('users.id', 'users.username')
            ->get();

        return view('groups.show', compact('group', 'owner', 'members'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'group_name' => 'required|string|max:30',
            'group_description' => 'required|string|max:200',
            'public' => 'required|boolean',
            'group_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $group = Group::findOrFail($id);
        $group->name = $request->input('group_name');
        $group->description = $request->input('group_description');
        $group->public = $request->input('public');
        
        if ($request->hasFile('group_image')) {
            $imageName = 'group_' . time() . '.' . $request->group_image->extension();
            $request->group_image->storeAs('public/images/group_images', $imageName);
            $group->image = $imageName;
        }
    
        $group->save();
    
        return redirect()->route('groups.show', ['id' => $group->id, 'group_image' => $group->image])->with('success', 'Group updated successfully!');
    }
    
    public function uploadImage(Request $request, $id)
    {
        $request->validate([
            'group_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        $group = Group::findOrFail($id);
    
        if ($request->hasFile('group_image')) {
            $imageName = 'group_' . time() . '.' . $request->group_image->extension();
            $request->group_image->storeAs('public/images/group_images', $imageName);
            $group->image = $imageName;
            $group->save();
        }
    
        return redirect()->route('groups.edit', $group->id)->with('success', 'Group image updated successfully!');
    }

    public function removeMember(Request $request, $groupId, $userId)
    {
        $group = Group::findOrFail($groupId);

        // Verificar se o usuário autenticado é o owner ou um admin
        $owner = DB::table('group_owner')
            ->where('id_group', $group->id)
            ->where('id_user', Auth::id())
            ->exists();

        
        if (Auth::user()->isAdmin() || $owner) {
            DB::table('group_member')
                ->where('id_group', $groupId)
                ->where('id_user', $userId)
                ->delete();
            
            $members= DB::table('group_member')
                ->where('id_group', $groupId)
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
                    $groupNotification->id_group = $groupId;
                    $groupNotification->notification_type = 'removed_f_group';
                    $groupNotification->save();

                    event(new NotificationPusher($notification->id, $notification->received_user));

                }
            }

            $notification = new Notification();
            $notification->emitter_user = Auth::id();
            $notification->received_user = $userId;
            $notification->date = now();
            $notification->save();

            $groupNotification = new GroupNotification();
            $groupNotification->id = $notification->id;
            $groupNotification->id_group = $groupId;
            $groupNotification->notification_type ='expelled_group';
            $groupNotification->save();

            event(new NotificationPusher($notification->id, $notification->received_user));

        }

        return redirect()->route('groups.show', $groupId);
    }
    
    public function inviteMember(Request $request, $id)
    {
        $group = Group::findOrFail($id);
        $user = User::where('username', $request->input('username'))->firstOrFail();

        $owner = DB::table('group_owner')
            ->where('id_group', $group->id)
            ->where('id_user', Auth::id())
            ->exists();

        if (Auth::user()->isAdmin() || $owner) {
            $isInvited = DB::table('invite_member')
                ->where('id_group', $id)
                ->where('id_user', $user->id)
                ->exists();

            if (!$isInvited) {
                DB::table('invite_member')->insert([
                    'id_group' => $group->id,
                    'id_user' => $user->id,
                    'invited_at' => now(),
                ]);

                // Criar notificação
                $notification = new Notification();
                $notification->received_user = $user->id;
                $notification->emitter_user = Auth::id();
                $notification->date = now();
                $notification->save();

                $groupNotification = new GroupNotification();
                $groupNotification->id = $notification->id;
                $groupNotification->id_group = $id;
                $groupNotification->notification_type = 'invite_member';
                $groupNotification->save();

                event(new NotificationPusher($notification->id, $notification->received_user));


                return redirect()->route('groups.show', $id)->with('success', 'User invited successfully!');
        }

        return redirect()->route('groups.show', $id)->with('error', 'User is already invited to the group.');
        }
    }
    

    public function acceptInvite($id_group)
    {
        $user = Auth::user();

        $group = Group::findOrFail($id_group);

        $isMember = DB::table('group_member')
            ->where('id_group', $id_group)  
            ->where('id_user', $user->id)
            ->exists();

        $invite = DB::table('invite_member')
            ->where('id_group', $id_group)
            ->where('id_user', $user->id)
            ->exists();

        if (!$isMember) {
            // Add the user to the group
            DB::table('group_member')->insert([
                'id_group' => $group->id,
                'id_user' => $user->id,
            ]);
        }
        if ($invite) {

            DB::table('invite_member')
                ->where('id_group', $id_group)
                ->where('id_user', Auth::id())
                ->delete();

            GroupNotification::whereHas('notification', function ($query) use ($user) {
                $query->where('received_user', $user->id);
            })->where('id_group', $group->id)->delete();

            $members= DB::table('group_member')
                ->where('id_group', $id_group)
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
                    $groupNotification->id_group = $id_group;
                    $groupNotification->notification_type = 'join_group';
                    $groupNotification->save();

                    event(new NotificationPusher($notification->id, $notification->received_user));

                }
            }

            $owner = DB::table('group_owner')
                ->where('id_group', $id_group)
                ->first();

            $notification = new Notification();
            $notification->emitter_user = $owner->id_user;
            $notification->received_user = Auth::id();
            $notification->date = now();
            $notification->save();

            $groupNotification = new GroupNotification();
            $groupNotification->id = $notification->id;
            $groupNotification->id_group = $id_group;
            $groupNotification->notification_type ='added_t_group';
            $groupNotification->save();

            event(new NotificationPusher($notification->id, $notification->received_user));

            return redirect()->route('groups.show', $id_group)->with('success', 'You have joined the group.');
        }


        return redirect()->route('groups.show', $id_group)->with('error', 'Invite not found.');
    }

    public function removeInvite($groupId, $userId)
    {
        // Remover a entrada na tabela invite_member
        DB::table('invite_member')
            ->where('id_group', $groupId)
            ->where('id_user', $userId)
            ->delete();

        // Remover a notificação gerada para o usuário convidado
        $notification = GroupNotification::whereHas('notification', function ($query) use ($userId) {
            $query->where('received_user', $userId);
        })->where('id_group', $groupId)->first();

        if ($notification) {
            // Primeiro, deletar a entrada na tabela group_notification
            $notification->delete();
            // Depois, deletar a entrada correspondente na tabela notification
            Notification::where('id', $notification->id)->delete();
        }

        return redirect()->route('profile.notifications', $userId)->with('success', 'Invite and notification removed successfully.');
    }

    public function joinGroup($id)
    {
        $group = Group::findOrFail($id);

        // Verificar se o usuário já é membro do grupo
        $isMember = DB::table('group_member')
            ->where('id_group', $id)
            ->where('id_user', Auth::id())
            ->exists();

        if (!$isMember) {

            $members= DB::table('group_member')
                ->where('id_group', $id)
                ->get();
            
            foreach($members as $member){
                $notification = new Notification();
                $notification->received_user = $member->id_user;
                $notification->emitter_user = Auth::id();
                $notification->date = now();
                $notification->save();

                $groupNotification = new GroupNotification();
                $groupNotification->id = $notification->id;
                $groupNotification->id_group = $id;
                $groupNotification->notification_type = 'join_group';
                $groupNotification->save();

                event(new NotificationPusher($notification->id, $notification->received_user));

            }

            DB::table('group_member')->insert([
                'id_group' => $id,
                'id_user' => Auth::id(),
            ]);

        }

        return redirect()->route('groups.show', $id);
    }

    public function leaveGroup($id)
    {
        $group = Group::findOrFail($id);

        // Verificar se o usuário é membro do grupo
        $isMember = DB::table('group_member')
            ->where('id_group', $id)
            ->where('id_user', Auth::id())
            ->exists();

        if ($isMember) {
            DB::table('group_member')
                ->where('id_group', $id)
                ->where('id_user', Auth::id())
                ->delete();
            
        }

        $members= DB::table('group_member')
            ->where('id_group', $id)
            ->get();

        foreach($members as $member){
            $notification = new Notification();
            $notification->received_user = $member->id_user;
            $notification->emitter_user = Auth::id();
            $notification->date = now();
            $notification->save();

            $groupNotification = new GroupNotification();
            $groupNotification->id = $notification->id;
            $groupNotification->id_group = $id;
            $groupNotification->notification_type = 'leave_group';
            $groupNotification->save();

            event(new NotificationPusher($notification->id, $notification->received_user));

        }
        return redirect()->route('groups.show', $id);
    }

    public function requestJoin($id)
    {
        $group = Group::findOrFail($id);

        // Verificar se o usuário já é membro do grupo ou já fez um pedido

        $isMember = DB::table('group_member')
            ->where('id_group', $id)
            ->where('id_user', Auth::id())
            ->exists();

        $hasRequested = DB::table('group_join_request')
            ->where('id_group', $id)
            ->where('id_user', Auth::id())
            ->exists();

        if (!$isMember && !$hasRequested) {
            DB::table('group_join_request')->insert([
                'id_group' => $id,
                'id_user' => Auth::id(),
            ]);
        }

        $group_owner = DB::table('group_owner')
            ->where('id_group', $id)
            ->first();
        
        $notification = new Notification();
        $notification->received_user = $group_owner->id_user;
        $notification->emitter_user = Auth::id();
        $notification->date = now();
        $notification->save();

        $groupNotification = new GroupNotification();
        $groupNotification->id = $notification->id;
        $groupNotification->id_group = $id;
        $groupNotification->notification_type = 'request_join';
        $groupNotification->save();

        event(new NotificationPusher($notification->id, $notification->received_user));

        
        return redirect()->route('groups.show', $id)->with('message', 'You already request to join this group. Wait for the owner’s response');
    }

    public function removeRequest($groupId, $userId)
    {
        DB::table('group_join_request')
            ->where('id_group', $groupId)
            ->where('id_user', $userId)
            ->delete();

        return redirect()->route('profile.show', Auth::id());
    }

    public function acceptRequest($groupId, $userId)
    {
        // Remover o pedido de entrada no grupo
        DB::table('group_join_request')
            ->where('id_group', $groupId)
            ->where('id_user', $userId)
            ->delete();

        $isMember = DB::table('group_member')
            ->where('id_group', $groupId)
            ->where('id_user', $userId)
            ->exists();
        
        if (!$isMember) {

            

            $notification= new Notification();
            $notification->received_user = $userId;
            $notification->emitter_user = Auth::id();
            $notification->date = now();
            $notification->save();

            $groupNotification = new GroupNotification();
            $groupNotification->id = $notification->id;
            $groupNotification->id_group = $groupId;
            $groupNotification->notification_type = 'added_t_group';
            $groupNotification->save();

            event(new NotificationPusher($notification->id, $notification->received_user));

            $members= DB::table('group_member')
                ->where('id_group', $groupId)
                ->get();
            
            foreach($members as $member){
                $notification = new Notification();
                $notification->received_user = $member->id_user;
                $notification->emitter_user = $userId;
                $notification->date = now();
                $notification->save();

                $groupNotification = new GroupNotification();
                $groupNotification->id = $notification->id;
                $groupNotification->id_group = $groupId;
                $groupNotification->notification_type = 'join_group';
                $groupNotification->save();

                event(new NotificationPusher($notification->id, $notification->received_user));

            }
            DB::table('group_member')->insert([
                'id_group' => $groupId,
                'id_user' => $userId
            ]);
            
        }
        return redirect()->route('groups.requests', $groupId);
    }
    
    public function requests($id)
    {
        $group = Group::findOrFail($id);
        $owner = DB::table('group_owner')
            ->join('users', 'group_owner.id_user', '=', 'users.id')
            ->where('group_owner.id_group', $group->id)
            ->select('users.id', 'users.username')
            ->first();

        if (!Auth::check() || (!Auth::user()->isAdmin() && $owner->id != Auth::id())) {
            return redirect()->route('groups.show', $id)->with('error', 'You do not have permission to view group requests.');
        }
        // Buscar pedidos de entrada no grupo
        $requests = DB::table('group_join_request')
            ->join('users', 'group_join_request.id_user', '=', 'users.id')
            ->where('group_join_request.id_group', $id)
            ->select('users.id', 'users.username')
            ->get();
        $invites = DB::table('invite_member')
            ->join('users', 'invite_member.id_user', '=', 'users.id')
            ->where('invite_member.id_group', $id)
            ->select('users.id', 'users.username')
            ->get();
        
        return view('groups.requests', compact('group', 'owner', 'invites', 'requests'));
    }

    public function messages($id)
    {
        $group = Group::with('owner')->findOrFail($id);
        $userId = Auth::id();

        // Verificar se o usuário é membro do grupo ou administrador
        $isMember = $group->members->contains($userId);
        $isAdmin = Auth::user()->isAdmin();

        if (!$isMember && !$isAdmin) {
            return redirect()->route('groups.show', $id)->with('error', 'You do not have access to the group chat.');
        }

        $messages = Message::where('id_group', $id)->with('user')->orderBy('created_at', 'asc')->get();

        $members=DB::table('group_member')
            ->where('id_group', $id)
            ->get();
        
        return view('groups.messages', compact('group', 'messages'));
    }

    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $message = new Message();
        $message->id_group = $id;
        $message->id_user = Auth::id();
        $message->content = $request->input('content');
        $message->created_at = now();
        $message->save();

        $members=DB::table('group_member')
            ->where('id_group', $id)
            ->get();
        foreach($members as $member){
            if($member->id_user != Auth::id()){
                $notification = new Notification();
                $notification->emitter_user = Auth::id();
                $notification->received_user = $member->id_user;
                $notification->date = now();
                $notification->save();

                $groupNotification = new GroupNotification();
                $groupNotification->id = $notification->id;
                $groupNotification->id_group = $id;
                $groupNotification->notification_type = 'received_message';
                $groupNotification->save();

                event(new NotificationPusher($notification->id, $notification->received_user));

            }
        }
        
        return redirect()->route('groups.messages', $id);
    }

    public function deleteMessage($id)
    {
        $message = Message::findOrFail($id);
        $group = $message->group;

        if (Auth::id() == $message->id_user || (isset($group->owner) && Auth::id() == $group->owner->id) || Auth::user()->isAdmin()) {
            $message->delete();
        }

        return redirect()->route('groups.messages', $group->id);
    }

    

    public function destroy($id)
    {
        $group = Group::findOrFail($id);

        // Verificar se o usuário é o owner ou um admin
        $isOwner = DB::table('group_owner')
            ->where('id_group', $group->id)
            ->where('id_user', Auth::id())
            ->exists();

        if (!$isOwner && !Auth::user()->isAdmin()) {
            return redirect()->route('groups.show', $id)->with('error', 'Only the owner or an admin can delete the group.');
        }

        $group->delete();

        return redirect()->route('groups.index')->with('success', 'Group deleted successfully.');
    }

    public function userIsParticipating($groupId, $userId)
    {
        if (!Auth::check()) {
            return false;
        }
        return DB::table('group_member')
            ->where('id_group', $groupId)
            ->where('id_user', $userId)
            ->exists() ? true : false;
    }
}