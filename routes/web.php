<?php

use App\Events\TaskCreated;
use App\User;
use App\Jobs\ReconcileAccount;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    $pipeline = app(Pipeline::class);
    //we have a data
    $pipeline->send('hello freaking world')
        //we send the data thorough the series of pipes like middleware
        //each of those pipes have te opportunity to modify that data or through exception, they
        //can do whatever they want as long as they pass the result to the next layer of the onion or to the next pipe.
        ->through([
            //you can pass a pipe class(class that has handle method)
            //or even a closure
            function ($string, $next) {
                $string = ucwords($string);

                return $next($string);
            },
            function ($string, $next) {
                //case insensitive ireplace
                $string = str_ireplace('freaking', '', $string);

                return $next($string);
            },

            //ReconcileAccount::class,
        ])
        //then we dump the result in a browser
        ->then(function ($string) {
            dump($string);
        });

    return 'Done';
});

// Example 1:
Route::get('/redis', function () {
    dispatch(function () {
        logger('Hello there');
    })->delay(now()->addMinutes(2));
    return 'Finished';
});

//Use case: user is registered, you want to send an email x time after its registered,
//update an account for user when it signs up,
//schedule an email after register etc.

// In x-tralife we used it after the garmin api hit the endpoint to parse the data to a table

// Example 2:
Route::get('/account', function () {
    $user = App\User::first();

    dispatch(new ReconcileAccount($user));

    //ReconcileAccount::dispatch($user)->onQueue('high');

    return 'Finished';
});

Route::get('/source', function () {

    $user = User::first();

    ReconcileAccount::dispatch($user);
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// You must restart the queue to see the code changes: on deploy is really important to add
//php artisan queue:restart or close it out and re-run it

//queue:work runs in memory

//Alternatively, you may run the queue:listen command. When using the queue:listen command,
//you don't have to manually restart the worker when you want to reload your updated code or reset
//the application state; however, this command is significantly less efficient than the queue:work
// command

// pa queue:failed  - lists the failed jobs
//pa queue:retry id of the failed queue

// you can specify any number of queues and then you can run any number of
//queue workers:
//->onQueue('high');

//php artisan queue:work --queue="high,default"

Route::get('websockets', function () {
    return view('home');
});

Route::get('tasks', function () {
    return \App\Task::latest()->pluck('body');
});

Route::post('tasks', function () {
    $task = \App\Task::forceCreate(request(['body']));

    event(
        (new TaskCreated($task))->dontBroadcastToCurrentUser()
    );
});

Route::get('/projects/{project}', function (\App\Project $project) {
    $project->load('tasks');

    return view('home', compact('project'));
});

// API

Route::get('/api/projects/{project}', function (\App\Project $project) {
    return $project->tasks->pluck('body');
});

Route::post('/api/projects/{project}/tasks', function (\App\Project $project) {
    $task = $project->tasks()->create(request(['body']));

    event(new TaskCreated($task));

    return $task;
});



//Route::get('update', function () {
//    \App\Events\OrderStatusUpdated::dispatch();
//    This is the same with:
//    event(new \App\Events\OrderStatusUpdated());
//});
