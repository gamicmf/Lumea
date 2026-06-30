<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Notification;
use App\Models\Publication;
use App\Models\Challenge;
use App\Models\Group;
use App\Models\Comment;
use App\Models\User;
use App\Models\FollowRequest;
use App\Policies\NotificationsPolicy;
use App\Policies\PublicationPolicy;
use App\Policies\ChallengesPolicy;
use App\Policies\CommentPolicy;
use App\Policies\FollowRequestPolicy;
use App\Policies\ProfilePolicy;
use App\Policies\GroupPolicy;
use App\Models\Post;
use App\Policies\PostPolicy;
use App\Policies\StaticPagesPolicy;

use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Publication::class => PublicationPolicy::class,
        Challenge::class => ChallengesPolicy::class,
        Group::class => GroupPolicy::class,
        Comment::class => CommentPolicy::class,
        User::class => ProfilePolicy::class,
        FollowRequest::class => FollowRequestPolicy::class,
        Notification::class => NotificationsPolicy::class,
        Post::class => PostPolicy::class,
        User::class => UserPolicy::class,
        StaticPagesPolicy::class => StaticPagesPolicy::class,
        
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
