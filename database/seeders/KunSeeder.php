<?php

namespace Database\Seeders;

use App\Models\Kun;
use App\Models\Pleme;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class KunSeeder extends Seeder
{
    public function run(): void
    {
        $plemena = Pleme::query()->pluck('nazev', 'kod');

        $kone = [
            [
                'user_email' => 'pavla@example.cz',
                'jmeno' => 'Chance',
                'plemeno_kod' => 'WPB',
                'rok_narozeni' => 2012,
                'pohlavi' => 'k',
                'staj' => 'Écola Equestrian',
                'cislo_prukazu' => '71047',
                'cislo_hospodarstvi' => 'CZ31129487',
                'majitel_jmeno_adresa' => 'Pavla Cihlová, České Budějovice',
            ],
            [
                'user_email' => 'pavla@example.cz',
                'jmeno' => 'Écola',
                'plemeno_kod' => 'A1/1',
                'rok_narozeni' => 1997,
                'pohlavi' => 'k',
                'staj' => 'Écola Equestrian',
                'cislo_prukazu' => '43092',
                'cislo_hospodarstvi' => 'CZ31129487',
                'majitel_jmeno_adresa' => 'Pavla Cihlová, České Budějovice',
            ],
            [
                'user_email' => 'pavla@example.cz',
                'jmeno' => 'Escape Donna',
                'plemeno_kod' => 'A1/1',
                'rok_narozeni' => 2009,
                'pohlavi' => 'k',
                'staj' => 'Écola Equestrian',
                'cislo_prukazu' => '58211',
                'majitel_jmeno_adresa' => 'Pavla Cihlová, České Budějovice',
            ],
            [
                'user_email' => 'pavla@example.cz',
                'jmeno' => 'Galf',
                'plemeno_kod' => 'WB',
                'rok_narozeni' => 2019,
                'pohlavi' => 'v',
                'staj' => 'Écola Equestrian',
                'majitel_jmeno_adresa' => 'Pavla Cihlová, České Budějovice',
            ],
            [
                'user_email' => 'pavla@example.cz',
                'jmeno' => 'Hugo Lucky Star',
                'plemeno_kod' => 'SHP',
                'rok_narozeni' => 2011,
                'pohlavi' => 'h',
                'staj' => 'Stálek Úslné',
                'majitel_jmeno_adresa' => 'Pavla Cihlová, České Budějovice',
            ],
            [
                'user_email' => 'pavla@example.cz',
                'jmeno' => 'Merlik Of Kadov',
                'plemeno_kod' => 'SHP',
                'rok_narozeni' => 2011,
                'pohlavi' => 'h',
                'staj' => 'Stálek Úslné',
                'majitel_jmeno_adresa' => 'Pavla Cihlová, České Budějovice',
            ],
            [
                'user_email' => 'pavla@example.cz',
                'jmeno' => 'On Star',
                'plemeno_kod' => 'APPA',
                'rok_narozeni' => 2006,
                'pohlavi' => 'k',
                'staj' => 'Écola Equestrian',
                'majitel_jmeno_adresa' => 'Pavla Cihlová, České Budějovice',
            ],
            [
                'user_email' => 'marketa@example.cz',
                'jmeno' => 'Accademia\'s Midnight Expresso',
                'plemeno_kod' => 'APPA',
                'rok_narozeni' => 2008,
                'pohlavi' => 'h',
                'staj' => 'FARMA BOJOU',
                'majitel_jmeno_adresa' => 'Markéta Plekánková, Brno',
            ],
            [
                'user_email' => 'michael@example.cz',
                'jmeno' => 'Atlantis Viking',
                'plemeno_kod' => 'APPA',
                'rok_narozeni' => 2014,
                'pohlavi' => 'h',
                'staj' => 'X-Trail Schachenhausen',
                'majitel_jmeno_adresa' => 'Michael Kabenderle, Schachenhausen',
            ],
            [
                'user_email' => 'michael@example.cz',
                'jmeno' => 'Cuckoo Moonshine',
                'plemeno_kod' => 'APPA',
                'rok_narozeni' => 2015,
                'pohlavi' => 'k',
                'staj' => 'X-Trail Schachenhausen',
                'majitel_jmeno_adresa' => 'Michael Kabenderle, Schachenhausen',
            ],
            [
                'user_email' => 'lucie@example.cz',
                'jmeno' => 'Joseph Small',
                'plemeno_kod' => 'WPB',
                'rok_narozeni' => 2017,
                'pohlavi' => 'v',
                'staj' => 'Grubac',
                'majitel_jmeno_adresa' => 'Lucie Jerhótová, Plzeň',
            ],
            [
                'user_email' => 'pavla@example.cz',
                'jmeno' => 'Star Queen Ann',
                'plemeno_kod' => 'APPA',
                'rok_narozeni' => 2022,
                'pohlavi' => 'k',
                'staj' => 'Écola Equestrian',
                'majitel_jmeno_adresa' => 'Pavla Cihlová, České Budějovice',
            ],
        ];

        foreach ($kone as $kun) {
            $user = User::query()->where('email', $kun['user_email'])->first();
            if (! $user) {
                throw new RuntimeException('User for KunSeeder not found: '.$kun['user_email']);
            }

            $plemenoKod = $kun['plemeno_kod'] ?? null;

            Kun::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'jmeno' => $kun['jmeno'],
                ],
                [
                    'plemeno_kod' => $plemenoKod,
                    'plemeno_nazev' => $plemenoKod ? ($plemena[$plemenoKod] ?? null) : null,
                    'plemeno_vlastni' => $kun['plemeno_vlastni'] ?? null,
                    'rok_narozeni' => $kun['rok_narozeni'],
                    'staj' => $kun['staj'],
                    'pohlavi' => $kun['pohlavi'],
                    'cislo_prukazu' => $kun['cislo_prukazu'] ?? null,
                    'cislo_hospodarstvi' => $kun['cislo_hospodarstvi'] ?? null,
                    'majitel_jmeno_adresa' => $kun['majitel_jmeno_adresa'] ?? null,
                ]
            );
        }
    }
}
