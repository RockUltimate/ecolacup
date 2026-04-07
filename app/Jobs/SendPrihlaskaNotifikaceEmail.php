<?php

namespace App\Jobs;

use App\Mail\PrihlaskaNotifikaceMail;
use App\Models\Prihlaska;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPrihlaskaNotifikaceEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public function __construct(public readonly Prihlaska $prihlaska)
    {
    }

    public function handle(): void
    {
        $prihlaska = $this->prihlaska->load([
            'udalost',
            'osoba',
            'kun',
            'kunTandem',
            'polozky',
            'ustajeniChoices.ustajeni',
            'user',
        ]);

        Mail::to('ecola@ecolakone.cz')->send(new PrihlaskaNotifikaceMail($prihlaska));
    }
}
