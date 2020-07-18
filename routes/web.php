<?php

use App\Jobs\ReconcileAccount;
use Illuminate\Pipeline\Pipeline;
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
                $string = str_ireplace('freaking', '', $string);

                return $next($string);
            },

            ReconcileAccount::class,
        ])
        //then we dump the result in a browser
        ->then(function ($string) {
            dump($string);
        });

    return 'Done';
});

// Example 1:
//dispatch(function () {
//    logger('Hello there');
//})->delay(now()->addMinutes(1));
//return 'Finished';

// Example 2:
//$user = App\User::first();
//
////dispatch(new ReconcileAccount($user));
//
//ReconcileAccount::dispatch($user)->onQueue('high');
//
//return 'Finished';
