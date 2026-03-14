<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username'   => 'opik',
                'email'      => 'opik@padelpro.com',
                // Ingat, di database password wajib di-hash!
                'password'   => password_hash('password123', PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username'   => 'player1',
                'email'      => 'player1@padelpro.com',
                'password'   => password_hash('12345678', PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Masukkan data ke tabel 'users' (sesuaikan nama tabelmu jika berbeda)
        $this->db->table('users')->insertBatch($data);
    }
}