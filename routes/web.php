<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StaticPagesController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\CommentLikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowRequestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GoogleController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// Home
Route::redirect('/', '/login');

Route::controller(GoogleController::class)->group(function () {
    Route::get('auth/google', 'redirect')->name('google-auth');
    Route::get('auth/google/call-back', 'callbackGoogle')->name('google-call-back');
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::middleware([ 'check.blocked'])->group(function () {
        //profile
        Route::get('profile/{id}', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('profile/{id}/edit', [ProfileController::class, 'edit'])->name('profile.mudar');
        Route::put('profile/{id}', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/profile/username/{username}', [ProfileController::class, 'showProfileByUsername'])->name('profile.showByUsername');
        Route::delete('profile/{id}', [UserController::class, 'deleteUser'])->name('user.delete');
        Route::post('/notifications/{id}/view', [NotificationController::class, 'markAsViewed'])->name('notifications.view');
        //follow
        Route::post('profile/{id}/follow', [ProfileController::class, 'follow'])->name('profile.follow');
        Route::delete('profile/{user_following}/unfollow/{user_followed}', [ProfileController::class, 'unfollow'])->name('profile.unfollow');
        Route::get('profile/{id}/followers', [ProfileController::class, 'followers'])->name('profile.followers');
        Route::get('profile/{id}/following', [ProfileController::class, 'following'])->name('profile.following');
        Route::post('/follow/accept/{id_followed}/{id_follower}/{notification_id}', [ProfileController::class, 'acceptFollow'])->name('follow.accept');
        Route::post('/follow/reject/{id_followed}/{id_follower}/{notification_id}', [ProfileController::class, 'rejectFollow'])->name('follow.reject');
        Route::get('profile/notifications', [ProfileController::class, 'notifications'])->name('profile.notifications');
        Route::delete('/follow_requests/{id_followed}/{id_follower}', [FollowRequestController::class, 'destroy'])->name('follow_requests.destroy');
        Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('register', [RegisterController::class, 'register']);
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login']);
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        //publications
        Route::get('publications', [PublicationController::class, 'index'])->name('publications.index');
        Route::get('publications/create/{challenge_id}', [PublicationController::class, 'create'])->name('publications.create');
        Route::post('publications/{challenge_id}', [PublicationController::class, 'store'])->name('publications.store');
        Route::get('publications/show/{id}', [PublicationController::class, 'show'])->name('publications.show');



        Route::get('publications/{id}/edit', [PublicationController::class, 'edit'])->name('publications.edit');
        Route::put('publications/{id}', [PublicationController::class, 'update'])->name('publications.update');
        Route::delete('publications/{id}', [PublicationController::class, 'delete'])->name('publications.destroy');
        Route::get('/users/redirect', [UserController::class, 'searchUser'])->name('users.redirect');

        Route::post('/publications/{id}/rate', [PublicationController::class, 'rate'])->name('publications.rate');
        Route::post('publications/{id}/comment', [PublicationController::class, 'commentPublication'])->name('publications.comment');
        Route::post('comments/{id}/reply', [PublicationController::class, 'replyComment'])->name('comments.reply');


        //groups


        Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
        Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
        Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
        Route::get('/groups/show/{id}', [GroupController::class, 'show'])->name('groups.show');
        Route::get('/groups/{id}/edit', [GroupController::class, 'edit'])->name('groups.edit');
        Route::put('/groups/{id}', [GroupController::class, 'update'])->name('groups.update');
        Route::post('/groups/{id}/upload-image', [GroupController::class, 'uploadImage'])->name('groups.uploadImage');
        Route::post('/groups/{id}/remove-member/{userId}', [GroupController::class, 'removeMember'])->name('groups.removeMember');
        Route::post('/groups/{id}/add', [GroupController::class, 'addMember'])->name('groups.addMember');
        Route::post('/groups/{id}/join', [GroupController::class, 'joinGroup'])->name('groups.join');



        Route::post('/groups/{id}/request-join', [GroupController::class, 'requestJoin'])->name('groups.requestJoin');
        Route::get('/groups/{id}/requests', [GroupController::class, 'requests'])->name('groups.requests');
        Route::delete('/groups/{groupId}/remove-request/{userId}', [GroupController::class, 'removeRequest'])->name('groups.removeRequest');
        Route::post('/groups/{groupId}/accept-request/{userId}', [GroupController::class, 'acceptRequest'])->name('groups.acceptRequest');
        Route::get('/groups/{id}/messages', [GroupController::class, 'messages'])->name('groups.messages');
        Route::post('/groups/{id}/send-message', [GroupController::class, 'sendMessage'])->name('groups.sendMessage');
        Route::delete('/messages/{id}', [GroupController::class, 'deleteMessage'])->name('groups.deleteMessage');
        Route::get('groups/{group_id}/create-challenge', [ChallengeController::class, 'createGroupChallenge'])->name('groups.createChallenge');
        Route::post('groups/{group_id}/store-challenge', [ChallengeController::class, 'storeGroupChallenge'])->name('groups.storeChallenge');
        Route::post('/groups/{id}/decline', [NotificationController::class, 'rejectInvite'])->name('groups.rejectInvite');
        Route::delete('/groups/{id}/leave', [GroupController::class, 'leaveGroup'])->name('groups.leave');
        Route::delete('/groups/{id}', [GroupController::class, 'destroy'])->name('groups.destroy');
        Route::post('/groups/{groupId}/invite', [GroupController::class, 'inviteMember'])->name('groups.inviteMember');

        Route::post('/groups/{id_group}/accept', [GroupController::class, 'acceptInvite'])->name('groups.acceptInvite');
        Route::delete('/groups/{groupId}/removeInvite/{userId}', [GroupController::class, 'removeInvite'])->name('groups.removeInvite');
        Route::get('/groups/search', [GroupController::class, 'search'])->name('groups.search');

        //reports
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::delete('reports/{id}', [ReportController::class, 'destroy'])->name('reports.destroy');
        Route::get('/report/{type}/{otherId}', [ReportController::class, 'create'])->name('report.create');
        Route::post('/report', [ReportController::class, 'store'])->name('report.store');
        
        //admin
        Route::get('/admin/panel', [AdminController::class, 'showAdminPanel'])->name('admin.panel');

        // Admin Delete User Route
        Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');

        // Admin Block User Route
        Route::post('/admin/users/{id}/block', [AdminController::class, 'blockUser'])->name('admin.blockUser');

        // Admin Unblock User Route
        Route::post('/admin/users/{id}/unblock', [AdminController::class, 'unblockUser'])->name('admin.unblockUser');

        // Admin Promote User Route
        Route::post('/admin/users/{id}/promote', [AdminController::class, 'promoteUser'])->name('admin.promoteUser');

        // Admin View FAQ Answer Route
        Route::get('/admin/faqs/{id}/view', [AdminController::class, 'viewFaq'])->name('admin.viewFaq');
        // Admin Delete FAQ Route
        Route::delete('/admin/faqs/{id}', [AdminController::class, 'deleteFaq'])->name('admin.deleteFaq');

        // Admin Answer FAQ Route
        Route::post('/admin/faqs/{id}/answer', [AdminController::class, 'answerFaq'])->name('admin.answerFaq');

        // Admin Show Answer FAQ Form Route
        Route::get('/admin/faqs/{id}/answer', [AdminController::class, 'showAnswerFaqForm'])->name('admin.showAnswerFaqForm');

        // Admin Delete Group Route
        Route::delete('/admin/groups/{id}', [AdminController::class, 'deleteGroup'])->name('admin.deleteGroup');

        // Admin Delete Challenge Route
        Route::delete('/admin/challenges/{id}', [AdminController::class, 'deleteChallenge'])->name('admin.deleteChallenge');

        //users
        Route::get('/users/search', [UserController::class, 'search'])->name('users.search');
        Route::get('/users/autocomplete', [UserController::class, 'autocomplete'])->name('users.autocomplete');


        //challenges
        Route::get('challenges', [ChallengeController::class, 'index'])->name('challenges.index');
        Route::get('challenges/create', [ChallengeController::class, 'create'])->name('challenges.create');
        Route::post('challenges', [ChallengeController::class, 'store'])->name('challenges.store');
        Route::get('challenges/participating', [ChallengeController::class, 'participating'])->name('challenges.participating');
        Route::get('challenges/show/{id}', [ChallengeController::class, 'show'])->name('challenges.show');
        Route::get('/challenges/search', [ChallengeController::class, 'search'])->name('challenges.search');
        Route::post('/challenges/{id}/joinchallenge', [ChallengeController::class, 'joinChallenge'])->name('challenges.joinchallenge');

       
        // Password Reset Routes
        Route::get('password/reset', [PasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('password/email', [PasswordController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('password/token', [PasswordController::class, 'showTokenForm'])->name('password.token');
        Route::post('password/verify-token', [PasswordController::class, 'verifyToken'])->name('password.verifyToken');
        Route::get('password/reset/{token}', [PasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('password/reset', [PasswordController::class, 'reset'])->name('password.update');
        Route::get('challenges/{id}/edit', [ChallengeController::class, 'edit'])->name('challenges.edit');
        Route::put('challenges/{id}', [ChallengeController::class, 'update'])->name('challenges.update');
        Route::delete('challenges/{id}', [ChallengeController::class, 'delete'])->name('challenges.destroy');




        Route::post('/publications/comments/{comment}/like', [CommentController::class, 'like'])->name('comments.like');
        Route::delete('/comments/{comment}/like', [CommentLikeController::class, 'unlike'])->name('comments.unlike');
        Route::middleware('auth')->group(function () {
        Route::post('/comments/{comment}/edit', [CommentController::class, 'edit'])->name('comments.edit');
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
        });
});

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/faq', function () {
    return view('faq');
})->name('faq');

Route::get('/faq', [StaticPagesController::class, 'faq'])->name('faq');
Route::post('/faq/send-question', [StaticPagesController::class, 'sendQuestion'])->name('faq.sendQuestion');
Route::get('/about', [StaticPagesController::class, 'about'])->name('about');

Route::get('/blocked', function () {
                        return view('blocked');
                        })->name('blocked.page');

