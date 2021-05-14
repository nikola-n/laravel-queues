<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('tasks.{project}', function ($user, \App\Project $project) {
    // this works for private channel
    //return $project->participants->contains($user);

    // for presence channel you need to return an information about the present user
    if ($project->participants->contains($user)) {
        return ['name' => $user->name];
    }

});
