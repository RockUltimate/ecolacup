<?php

namespace Database\Seeders;

use App\Models\Osoba;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class OsobaSeeder extends Seeder
{
    public function run(): void
    {
        $osoby = [
            ['user_email' => 'pavla@example.cz', 'jmeno' => 'Pavla', 'prijmeni' => 'Cihlová', 'datum_narozeni' => '1961-05-30', 'staj' => 'Écola Equestrian'],
            ['user_email' => 'pavla@example.cz', 'jmeno' => 'Kristýna', 'prijmeni' => 'Kubů', 'datum_narozeni' => '2006-03-21', 'staj' => 'Stálek Úslné'],
            ['user_email' => 'lucie@example.cz', 'jmeno' => 'Lucie', 'prijmeni' => 'Jerhótová', 'datum_narozeni' => '2004-06-10', 'staj' => 'Écola Equestrian'],
            ['user_email' => 'lucie@example.cz', 'jmeno' => 'Melanie', 'prijmeni' => 'Kirchner', 'datum_narozeni' => '1983-11-20', 'staj' => 'Écola Equestrian'],
            ['user_email' => 'marketa@example.cz', 'jmeno' => 'Markéta', 'prijmeni' => 'Plekánková', 'datum_narozeni' => '1990-07-22', 'staj' => 'Academia BOJOU'],
            ['user_email' => 'marketa@example.cz', 'jmeno' => 'Gabriela', 'prijmeni' => 'Plekánková', 'datum_narozeni' => '2012-02-14', 'staj' => 'Academia BOJOU'],
            ['user_email' => 'michael@example.cz', 'jmeno' => 'Michael', 'prijmeni' => 'Kabenderle', 'datum_narozeni' => '1978-11-08', 'staj' => 'X-Trail Schachenhausen'],
            ['user_email' => 'michael@example.cz', 'jmeno' => 'Simon', 'prijmeni' => 'Meier', 'datum_narozeni' => '1982-04-05', 'staj' => 'X-Trail Schachenhausen'],
            ['user_email' => 'pavla@example.cz', 'jmeno' => 'Paulína', 'prijmeni' => 'Jindřichová', 'datum_narozeni' => '1995-09-17', 'staj' => 'Stáj Úslné'],
            ['user_email' => 'lucie@example.cz', 'jmeno' => 'Alena', 'prijmeni' => 'Novotná', 'datum_narozeni' => '1975-06-03', 'staj' => 'Stáj Koníčkov'],
        ];

        foreach ($osoby as $osoba) {
            $user = User::query()->where('email', $osoba['user_email'])->first();
            if (! $user) {
                throw new RuntimeException('User for OsobaSeeder not found: '.$osoba['user_email']);
            }

            Osoba::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'jmeno' => $osoba['jmeno'],
                    'prijmeni' => $osoba['prijmeni'],
                    'datum_narozeni' => $osoba['datum_narozeni'],
                ],
                [
                    'staj' => $osoba['staj'],
                    'gdpr_souhlas' => true,
                    'gdpr_odvolano' => false,
                    'gdpr_souhlas_at' => now(),
                ]
            );
        }
    }
}
