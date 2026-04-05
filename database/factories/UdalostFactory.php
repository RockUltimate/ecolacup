<?php

namespace Database\Factories;

use App\Models\Udalost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Udalost>
 */
class UdalostFactory extends Factory
{
    protected $model = Udalost::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('+1 week', '+2 months');
        $endDate = (clone $startDate)->modify('+1 day');
        $deadline = (clone $startDate)->modify('-7 days');

        return [
            'nazev' => fake()->sentence(3),
            'misto' => fake()->city(),
            'datum_zacatek' => $startDate->format('Y-m-d'),
            'datum_konec' => $endDate->format('Y-m-d'),
            'uzavierka_prihlasek' => $deadline->format('Y-m-d'),
            'kapacita' => fake()->numberBetween(20, 120),
            'propozice_pdf' => null,
            'aktivni' => true,
            'popis' => fake()->paragraph(),
        ];
    }
}
