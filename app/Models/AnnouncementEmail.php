<?php

namespace App\Models;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AnnouncementEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $announcement;
    public $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($announcement,$user)
    {
        $this->announcement = $announcement;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.announcement',["user"=>$this->user,"announcement"=>$this->announcement]);
    }
}
