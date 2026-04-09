<?php

namespace App\Jobs;

use App\Mail\PrihlaskaPotvrzeniMail;
use App\Models\Prihlaska;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPrihlaskaEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public function __construct(
        public readonly Prihlaska $prihlaska,
        public readonly string $mode = 'created',
        public readonly bool $notifyAdmin = true,
    )
    {
    }

    public function handle(): void
    {
        $prihlaska = $this->prihlaska->load([
            'udalost',
            'osoba',
            'kun',
            'kunTandem',
            'polozky.moznost',
            'ustajeniChoices.ustajeni',
            'user',
        ]);

        if (! $prihlaska->user?->email) {
            return;
        }

        Mail::to($prihlaska->user->email)->send(new PrihlaskaPotvrzeniMail($prihlaska, $this->mode));

        if ($this->notifyAdmin) {
            SendPrihlaskaNotifikaceEmail::dispatch($prihlaska);
        }
    }
}
