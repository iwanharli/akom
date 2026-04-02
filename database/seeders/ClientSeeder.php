<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = [
            [
                'name' => 'Direktur Utama',
                'institution' => 'PT Pertamina (Persero)',
                'email' => 'dirut@pertamina.com',
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Direktur Keuangan',
                'institution' => 'PT Perusahaan Listrik Negara (PLN)',
                'email' => 'keuangan@pln.co.id',
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'VP Corporate Strategy',
                'institution' => 'PT Telkom Indonesia (Persero) Tbk',
                'email' => 'strategy@telkom.co.id',
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chief Economist',
                'institution' => 'PT Bank Mandiri (Persero) Tbk',
                'email' => 'economist@bankmandiri.co.id',
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Direktur Operasional',
                'institution' => 'PT Garuda Indonesia (Persero) Tbk',
                'email' => 'ops@garuda-indonesia.com',
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        \Illuminate\Support\Facades\DB::table('t_clients')->insert($clients);
    }
}
