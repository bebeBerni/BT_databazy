<?php

namespace App\Providers;

use App\Models\Note;
use App\Models\Task;
use App\Policies\NotePolicy;
use App\Policies\TaskPolicy;
use Illuminate\Auth\Access\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Note::class => NotePolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

        Gate::define('archiveOldDrafts', [NotePolicy::class, 'archiveOldDrafts']);
        Gate::define('viewUserNotesWithCategories', [NotePolicy::class, 'viewUserNotesWithCategories']);
    }

}
