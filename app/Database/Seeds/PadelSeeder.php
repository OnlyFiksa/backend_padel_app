<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PadelSeeder extends Seeder
{
    public function run()
    {
        // 1. Matikan Foreign Key Check sementara biar gampang kalau mau re-seed (timpa data)
        $this->db->disableForeignKeyChecks();

        // Kosongkan tabel sebelum diisi ulang
        $this->db->table('schedules')->truncate();
        $this->db->table('courts')->truncate();
        $this->db->table('venues')->truncate();
        $this->db->table('locations')->truncate();

        // 2. SEEDER LOCATIONS
        $locations = [
            ['id' => 1, 'city_name' => 'Jakarta Selatan'],
            ['id' => 2, 'city_name' => 'Jakarta Pusat'],
        ];
        $this->db->table('locations')->insertBatch($locations);

        // 3. SEEDER VENUES
        $venues = [
            [
                'id'          => 1,
                'location_id' => 1,
                'name'        => 'Padel Hub Senayan',
                'address'     => 'Jl. Gelora Bung Karno No.1, Senayan, Jakarta Selatan',
                'description' => 'Lapangan padel premium outdoor dengan fasilitas lengkap di tengah kota.',
                'image_url'   => 'default_venue_1.jpg'
            ],
            [
                'id'          => 2,
                'location_id' => 2,
                'name'        => 'Sky Terrace Padel',
                'address'     => 'Rooftop Grand Indonesia, Jl. M.H. Thamrin, Jakarta Pusat',
                'description' => 'Sensasi bermain padel di atas gedung dengan pemandangan malam kota Jakarta yang indah.',
                'image_url'   => 'default_venue_2.jpg'
            ]
        ];
        $this->db->table('venues')->insertBatch($venues);

        // 4. SEEDER COURTS
        $courts = [
            // Lapangan di Padel Hub Senayan
            ['id' => 1, 'venue_id' => 1, 'court_number' => 'Court A (Outdoor)', 'is_active' => 1],
            ['id' => 2, 'venue_id' => 1, 'court_number' => 'Court B (Outdoor)', 'is_active' => 1],
            
            // Lapangan di Sky Terrace Padel
            ['id' => 3, 'venue_id' => 2, 'court_number' => 'Court 1 (Rooftop)', 'is_active' => 1],
        ];
        $this->db->table('courts')->insertBatch($courts);

        // 5. SEEDER SCHEDULES
        // Asumsi day_of_week: 1 = Senin, 7 = Minggu
        $schedules = [
            // Jadwal Senayan Court A (Harga siang lebih murah, malam lebih mahal)
            ['court_id' => 1, 'day_of_week' => 1, 'start_time' => '16:00:00', 'end_time' => '17:00:00', 'price' => 150000],
            ['court_id' => 1, 'day_of_week' => 1, 'start_time' => '18:00:00', 'end_time' => '19:00:00', 'price' => 200000],
            ['court_id' => 1, 'day_of_week' => 6, 'start_time' => '08:00:00', 'end_time' => '09:00:00', 'price' => 250000], // Weekend

            // Jadwal Senayan Court B
            ['court_id' => 2, 'day_of_week' => 1, 'start_time' => '18:00:00', 'end_time' => '19:00:00', 'price' => 200000],

            // Jadwal Sky Terrace Court 1 (Harga lebih premium)
            ['court_id' => 3, 'day_of_week' => 1, 'start_time' => '19:00:00', 'end_time' => '20:00:00', 'price' => 300000],
            ['court_id' => 3, 'day_of_week' => 5, 'start_time' => '20:00:00', 'end_time' => '21:00:00', 'price' => 350000],
        ];
        $this->db->table('schedules')->insertBatch($schedules);

        // Nyalakan kembali Foreign Key Check
        $this->db->enableForeignKeyChecks();

        echo "Seeder Locations, Venues, Courts, dan Schedules berhasil dijalankan! \n";
    }
}