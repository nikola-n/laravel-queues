<?php

namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReconcileAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \App\User
     */
    protected $user;

    /**
     * Create a new job instance.
     *
     * @param \App\User $user
     */
    public function __construct(User $user)
    {
        //
        $this->user = $user;
    }

    public function handle($string, $next)
    {
        return $next('Something Else');
    }
    ///**
    // * Execute the job.
    // *
    // * @param \Illuminate\Filesystem\Filesystem $file
    // *
    // * @return void
    // */
    //public function handle(Filesystem $file)
    //{
    //    $file->put(public_path('testing.txt'), 'Recounciling: ' . $this->user->name);
    //
    //    logger('Reconcile User ' . $this->user->name);
    //}

    //you can override the tags from horizon

    public function tags()
    {
        return ['accounts'];
    }
}
