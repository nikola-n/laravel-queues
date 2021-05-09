<?php

namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
        $this->user = $user;
    }

    ///**
    // * @param $string
    // * @param $next
    // * This is used as a pipeline, middleware
    // * @return mixed
    // */
    //public function handle($string, $next)
    //{
        //return $next('Something Else');
    //}


    /**
     * Execute the job.
     *
     * @param \Illuminate\Filesystem\Filesystem $file
     *
     * @return void
     */
    public function handle(Filesystem $file)
    {
        $file->put(public_path('testing.txt'), 'Reconciling: ' . $this->user->name);

        logger('Reconcile User ' . $this->user->name);
    }

    //you can override the tags from horizon
    public function tags()
    {
        return ['accounts'];
    }
}
