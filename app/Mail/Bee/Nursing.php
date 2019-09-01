<?php

namespace App\Mail\Bee;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Nursing extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('8000@made-in-china.com')
            ->subject('恭喜您的宝贝出生啦！（内含护理假、生育礼金、护理假津贴多项福利）')
            ->view('emails.bee.nursing');
    }
}
