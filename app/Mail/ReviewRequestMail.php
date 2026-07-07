<?php
// app/Mail/ReviewRequestMail.php

namespace App\Mail;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReviewRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Movie $movie,
        public ?string $watchedDate,
        public string $reviewUrl,
    ) {
    }

    public function build(): self
    {
        return $this->subject("How was \"{$this->movie->title}\"? Write a review!")
            ->view('emails.review-request');
    }
}