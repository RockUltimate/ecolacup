<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'jmeno' => 'Admin',
                'prijmeni' => 'Správce',
                'email' => 'admin@koneakce.cz',
                'password' => 'admin123',
                'is_admin' => true,
            ],
            [
                'jmeno' => 'Pavla',
                'prijmeni' => 'Cihlová',
                'email' => 'pavla@example.cz',
                'datum_narozeni' => '1961-05-30',
                'pohlavi' => 'F',
                'telefon' => '602012020',
                'password' => 'heslo123',
            ],
            [
                'jmeno' => 'Lucie',
                'prijmeni' => 'Jerhótová',
                'email' => 'lucie@example.cz',
                'datum_narozeni' => '1985-03-15',
                'pohlavi' => 'F',
                'telefon' => '777234567',
                'password' => 'heslo123',
            ],
            [
                'jmeno' => 'Markéta',
                'prijmeni' => 'Plekánková',
                'email' => 'marketa@example.cz',
                'datum_narozeni' => '1990-07-22',
                'pohlavi' => 'F',
                'telefon' => '603345678',
                'password' => 'heslo123',
            ],
            [
                'jmeno' => 'Michael',
                'prijmeni' => 'Kabenderle',
                'email' => 'michael@example.cz',
                'datum_narozeni' => '1978-11-08',
                'pohlavi' => 'M',
                'telefon' => '728456789',
                'password' => 'heslo123',
            ],
        ];

        foreach ($users as $user) {
            $password = $user['password'];
            unset($user['password']);

            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => trim($user['jmeno'].' '.$user['prijmeni']),
                    'jmeno' => $user['jmeno'],
                    'prijmeni' => $user['prijmeni'],
                    'datum_narozeni' => $user['datum_narozeni'] ?? null,
                    'pohlavi' => $user['pohlavi'] ?? null,
                    'telefon' => $user['telefon'] ?? null,
                    'password' => Hash::make($password),
                    'is_admin' => (bool) ($user['is_admin'] ?? false),
                    'gdpr_souhlas' => true,
                    'gdpr_souhlas_at' => now(),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
