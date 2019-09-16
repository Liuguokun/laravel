<?php

namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessPodcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data=[])
    {
        //
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
//       $content = $this->user->name.'发表了新文章'.time()."\n";
//        $content =
//
//       file_put_contents('Notice.txt',$content,FILE_APPEND);


//        $str = $this->data['id'].$this->data['name']."\n";
        $str = $this->data;
        print_r($str);
        $this->delete();

    }
}
