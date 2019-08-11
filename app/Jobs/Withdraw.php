<?php

namespace App\Jobs;

use App\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Withdraw implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $groups = Group::all();
        foreach ($groups as $group) {
            $collection = $group->collection->list;
            if (!is_null($collection)) {
                $collection = json_decode($collection);
                foreach ($collection as $key => $value) {
                    if ($key = 0) {
                        $collection[(count($collection) - 1)] = $collection[0];
                    } else {
                        $collection[$key - 1] = $value;
                    }
                }
            }
        }
    }
}
