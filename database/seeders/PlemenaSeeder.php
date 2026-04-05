<?php

namespace Database\Seeders;

use App\Models\Pleme;
use Illuminate\Database\Seeder;

class PlemenaSeeder extends Seeder
{
    public function run(): void
    {
        $breeds = [
            ['kod' => 'ACHT', 'nazev' => 'Achaltekinský kůň'],
            ['kod' => 'AMHA', 'nazev' => 'Americký minihorse'],
            ['kod' => 'APH', 'nazev' => 'Americký Paint Horse'],
            ['kod' => 'A1/1', 'nazev' => 'Anglický plnokrevník'],
            ['kod' => 'APPA', 'nazev' => 'Appaloosa'],
            ['kod' => 'APPpo', 'nazev' => 'Appaloosa Pony'],
            ['kod' => 'OR1/1', 'nazev' => 'Arabský plnokrevník'],
            ['kod' => 'BP', 'nazev' => 'Barok Pinto'],
            ['kod' => 'b.PP', 'nazev' => 'Bez plemenné příslušnosti'],
            ['kod' => 'CMB', 'nazev' => 'Českomoravský belgik'],
            ['kod' => 'CNOR', 'nazev' => 'Český norik'],
            ['kod' => 'CSP', 'nazev' => 'Český sportovní pony'],
            ['kod' => 'CSPMK', 'nazev' => 'Český sportovní pony - malý kůň'],
            ['kod' => 'CT', 'nazev' => 'Český teplokrevník'],
            ['kod' => 'FAL', 'nazev' => 'Falabella'],
            ['kod' => 'FEELL', 'nazev' => 'Fell Pony'],
            ['kod' => 'FJ', 'nazev' => 'Fjordský kůň'],
            ['kod' => 'FK', 'nazev' => 'Fríský kůň'],
            ['kod' => 'HAFL', 'nazev' => 'Hafling'],
            ['kod' => 'HANN', 'nazev' => 'Hanoverský kůň'],
            ['kod' => 'KWPN', 'nazev' => 'Holandský teplokrevník'],
            ['kod' => 'HOLST', 'nazev' => 'Holštýnský kůň'],
            ['kod' => 'HUCUL', 'nazev' => 'Huculský kůň'],
            ['kod' => 'ICOB', 'nazev' => 'Irský Cob/Tinker'],
            ['kod' => 'KMSH', 'nazev' => 'Kentucky horský sedlový kůň'],
            ['kod' => 'KLUS', 'nazev' => 'Klusák'],
            ['kod' => 'KNAPS', 'nazev' => 'Knabstrupper'],
            ['kod' => 'LIP', 'nazev' => 'Lipický kůň'],
            ['kod' => 'LUSIT', 'nazev' => 'Lusitano'],
            ['kod' => 'Mezek', 'nazev' => 'Mezek'],
            ['kod' => 'MiniA', 'nazev' => 'Miniappaloosa'],
            ['kod' => 'MMD', 'nazev' => 'Miniaturní středozemní osel'],
            ['kod' => 'MiniH', 'nazev' => 'MiniHorse'],
            ['kod' => 'MT', 'nazev' => 'Moravský teplokrevník'],
            ['kod' => 'Mula', 'nazev' => 'Mula'],
            ['kod' => 'NS', 'nazev' => 'Norik slezský'],
            ['kod' => 'Osel', 'nazev' => 'Osel'],
            ['kod' => 'CP', 'nazev' => 'Polský ušlechtilý polokrevník'],
            ['kod' => 'PRESP', 'nazev' => 'Pura Raza Española'],
            ['kod' => 'QH', 'nazev' => 'Quarter Horse'],
            ['kod' => 'ShA', 'nazev' => 'Shagya Arab'],
            ['kod' => 'SHP', 'nazev' => 'Shetlandský pony'],
            ['kod' => 'SHPMH', 'nazev' => 'Shetlandský pony – sekce minihorse'],
            ['kod' => 'CS', 'nazev' => 'Slovenský teplokrevník česká PK'],
            ['kod' => 'ST', 'nazev' => 'Slovenský teplokrevník slovenská PK'],
            ['kod' => 'STKL', 'nazev' => 'Starokladrubský kůň'],
            ['kod' => 'TRAK', 'nazev' => 'Trakénský kůň'],
            ['kod' => 'Pony', 'nazev' => 'Typ pony'],
            ['kod' => 'WB', 'nazev' => 'Typ teplokrevný'],
            ['kod' => 'WCobD', 'nazev' => 'Welsh Cob sekce D'],
            ['kod' => 'WPB', 'nazev' => 'Welsh part bred'],
            ['kod' => 'WP', 'nazev' => 'Welsh pony'],
            ['kod' => 'WPC', 'nazev' => 'Welsh pony typu Cob sekce C'],
        ];

        foreach ($breeds as $index => $breed) {
            Pleme::query()->updateOrCreate(
                ['kod' => $breed['kod']],
                [
                    'nazev' => $breed['nazev'],
                    'poradi' => $index + 1,
                ]
            );
        }
    }
}
