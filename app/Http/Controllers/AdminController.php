<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\User;
use App\Models\Admin;
use App\Models\Group;
use App\Models\Challenge;
use App\Models\Faq;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Events\NotificationPusher;
use App\Models\Notification;
use App\Models\Notifications\AdminNotification;
use App\Models\Notifications\UserNotification;
use App\Models\Notifications\GroupNotification;
use App\Models\Notifications\ChallengeNotification;

class AdminController extends Controller{
    public function showAdminPanel()
    {
        $reports = Report::all();
        $users = User::all()->sortBy(function($user) {
            return $user->isAdmin() ? 0 : 1;
        })->sortBy('id');
        $groups = Group::all();
        $challenges = Challenge::all();
        $faqs = Faq::all();
        return view('admin.panel', compact('reports', 'users', 'groups', 'challenges', 'faqs'));
    }

    public function deleteUser($id)
    {   
        if(Auth::user()->id == $id){
            session()->flash('message', 'You cannot delete yourself.');
            return redirect()->route('admin.panel');
        }

        $user = User::findOrFail($id);

        if($user->isAdmin()){
            session()->flash('message', 'You cannot delete another administrator.');
            return redirect()->route('admin.panel');
        }

        if(Auth::user()->isAdmin()){
            $user->delete();
            session()->flash('message', 'User deleted successfully.');
            return redirect()->route('admin.panel');
        } 

        return redirect()->route('publications.index');
    }

    public function blockUser($id)
    {
        if (Auth::user()->isAdmin()) {
            $user = User::findOrFail($id);
            if (Auth::user()->id == $id) {
                session()->flash('message', 'You cannot block yourself.');
                return redirect()->route('admin.panel');
            } elseif ($user->isAdmin()) {
                session()->flash('message', 'You cannot block another administrator.');
                return redirect()->route('admin.panel');
            } elseif (!$user->blocked) {
                $user->blocked = true;
                $user->save();
                session()->flash('message', 'User ' . $user->username . ' blocked.');
            } else {
                session()->flash('message', 'User ' . $user->username . ' is already blocked.');
            }
            return redirect()->route('admin.panel');
        }
    }

    public function unblockUser($id)
    {
        if (Auth::user()->isAdmin()) {
            $user = User::findOrFail($id);
            if ($user->blocked) {
                $user->blocked = false;
                $user->save();
                session()->flash('message', 'User ' . $user->username . ' unblocked.');
            } else {
                session()->flash('message', 'User ' . $user->username . ' is not blocked.');
            }
            return redirect()->route('admin.panel');
        }
    }

    public function promoteUser($id)
    {
        if (Auth::user()->isAdmin()) {
            $user = User::findOrFail($id);
            if (!Admin::where('id_user', $user->id)->exists()) {
                Admin::create(['id_user' => $user->id]);
                $notification = new Notification();
                $notification->emitter_user = Auth::user()->id;
                $notification->received_user = $id;
                $notification->date= now();
                $notification->save();

                $userNotification = new UserNotification();
                $userNotification->id = $notification->id;
                $userNotification->notification_type = 'promoved_admin';
                $userNotification->save();

                event(new NotificationPusher($notification->id, $notification->received_user));

                session()->flash('message', 'User ' . $user->username . ' promoted to administrator.');
            } else {
                session()->flash('message', 'User ' . $user->username . ' is already an administrator.');
            }
            return redirect()->route('admin.panel');
        }
    }

    public function answerFaq(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);
        $isEdited = !empty($faq->answer); // Verifica se a resposta já existia
        $faq->answer = $request->input('answer');
        $faq->save();

        // Enviar e-mail
        $this->sendFaqAnsweredEmail($faq->user->email, $faq->question, $faq->answer, $isEdited);

        session()->flash('message', 'FAQ answered successfully.');
        return redirect()->route('admin.panel');
    }

    public function viewFaq($id)
    {
        $faq = Faq::findOrFail($id);
        return view('admin.viewFaq', compact('faq'));
    }

    private function sendFaqAnsweredEmail($email, $question, $answer, $isEdited)
    {
        $subject = $isEdited ? 'FAQ Answer Updated' : 'FAQ Answered';
        $messageContent = view('emails.faqAnswered', compact('question', 'answer', 'isEdited'))->render();

        try {
            Mail::send([], [], function ($message) use ($email, $subject, $messageContent) {
                $message->to($email)
                        ->subject($subject)
                        ->html($messageContent);
            });
            \Log::info("E-mail enviado para $email com sucesso.");
        } catch (\Exception $e) {
            \Log::error("Erro ao enviar e-mail para $email: " . $e->getMessage());
        }
    }

    public function deleteFaq($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();
        session()->flash('message', 'FAQ deleted successfully.');
        return redirect()->route('admin.panel');
    }

    public function deleteGroup($id)
    {
        $group = Group::findOrFail($id);
        $members = DB::table('group_user')->where('id_group', $group->id)->get();
        foreach ($members as $member) {
            $notification = new Notification();
            $notification->emitter_user = Auth::user()->id;
            $notification->receiver_user = $member->id_user;
            $notification->date= now();
            $notification->save();

            $userNotification = new GroupNotification();
            $userNotification->id_notification = $notification->id;
            $userNotification->id_group = $group->id;
            $userNotification->notification_type = 'deleted_group';
            $userNotification->save();

            event(new NotificationPusher($notification->id, $notification->received_user));
        }
        $group->delete();
        
        session()->flash('message', 'Group deleted successfully.');

        
        return redirect()->route('admin.panel');
    }

    public function deleteChallenge($id)
    {
        $challenge = Challenge::findOrFail($id);
        $groupId = DB::table('group_challenge')->where('id_challenge', $challenge->id)->first();
        $group = Group::findOrFail($groupId);
        $members = DB::table('group_user')->where('id_group', $group->id)->get();
        foreach ($members as $member) {
            $notification = new Notification();
            $notification->emitter_user = Auth::user()->id;
            $notification->receiver_user = $member->id_user;
            $notification->date= now();
            $notification->save();

            $userNotification = new ChallengeNotification();
            $userNotification->id_notification = $notification->id;
            $userNotification->id_challenge = $challenge->id;
            $userNotification->notification_type = 'deleted_challenge';
            $userNotification->save();

            event(new NotificationPusher($notification->id, $notification->received_user));
        }

        $challenge->delete();
        session()->flash('message', 'Challenge deleted successfully.');
        return redirect()->route('admin.panel');
    }

    public function showAnswerFaqForm($id)
    {
        $faq = Faq::findOrFail($id);
        return view('admin.answerFaq', compact('faq'));
    }

    public function showDeleteFaqForm($id)
    {
        $faq = Faq::findOrFail($id);
        return view('admin.deleteFaq', compact('faq'));
    }
}