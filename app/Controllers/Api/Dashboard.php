<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class Dashboard extends ResourceController
{
    public function index()
    {
        // Panggil library Database CI4
        $db = \Config\Database::connect();

        // 1. AMBIL DATA USER
        // Sementara kita hardcode cari user dengan ID 1. 
        // Nanti kalau fitur Login/Token udah sempurna, angka 1 ini diganti jadi ID user yang lagi login.
        $userId = $this->request->getVar('user_id') ?? 1;
        
        $user = $db->table('users')->select('full_name')->where('id', $userId)->get()->getRow();
        // Kita pecah namanya biar ngambil nama panggilan aja (kata pertama)
        $userName = $user ? explode(' ', trim($user->full_name))[0] : 'Pemain';

        // 2. AMBIL UPCOMING MATCHES (Jadwal Terdekat)
        $upcomingQuery = $db->table('bookings b')
            ->select('v.name as title, b.booking_date as date, s.start_time as time')
            ->join('booking_details bd', 'bd.booking_id = b.id')
            ->join('schedules s', 's.id = bd.schedule_id')
            ->join('courts c', 'c.id = bd.court_id')
            ->join('venues v', 'v.id = c.venue_id')
            ->where('b.user_id', $userId)
            ->where('b.status', 'Active') // Ambil yang statusnya masih aktif
            ->where('b.booking_date >=', date('Y-m-d')) // Ambil hari ini atau ke depan
            ->orderBy('b.booking_date', 'ASC')
            ->orderBy('s.start_time', 'ASC')
            ->limit(1)
            ->get()
            ->getResultArray();

        $upcomingMatches = [];
        foreach ($upcomingQuery as $match) {
            $upcomingMatches[] = [
                'title' => $match['title'],
                'type'  => 'MATCH', // Karena di DB ga ada kolom tipe, kita set statis
                'date'  => date('d M Y', strtotime($match['date'])),
                'time'  => date('H:i', strtotime($match['time'])) . ' WIB'
            ];
        }

        // 3. AMBIL FEATURED VENUES (Rekomendasi Lapangan)
        // 3. AMBIL FEATURED VENUES (Rekomendasi Lapangan)
        $venuesQuery = $db->table('venues v')
            ->select('v.id, v.name, v.image_url') // <-- TAMBAHKAN v.image_url DI SINI
            ->select('(SELECT MIN(price) FROM schedules s JOIN courts c ON c.id = s.court_id WHERE c.venue_id = v.id) as starting_price')
            ->limit(5)
            ->get()
            ->getResultArray();

        $featuredVenues = [];
        foreach ($venuesQuery as $venue) {
            $priceText = $venue['starting_price'] ? 'Rp ' . number_format($venue['starting_price']/1000, 0, ',', '.') . 'k/hr' : 'Rp -/hr';
            
            // Bikin Full URL untuk gambar (base_url akan ambil IP dari file .env kamu)
            $imageUrl = $venue['image_url'] ? base_url('uploads/venues/' . $venue['image_url']) : '';

            $featuredVenues[] = [
                'name'     => $venue['name'],
                'rating'   => '4.' . rand(5, 9),
                'distance' => rand(1, 5) . '.' . rand(0, 9) . ' km away',
                'price'    => $priceText,
                'image'    => $imageUrl // <-- KIRIM URL GAMBAR KE FLUTTER
            ];
        }

        // 4. BUNGKUS DAN KIRIM KE FLUTTER
        $data = [
            'status' => 200,
            'message' => 'Berhasil mengambil data dashboard',
            'data' => [
                'user_name' => $userName,
                'upcoming_matches' => $upcomingMatches,
                'featured_venues' => $featuredVenues
            ]
        ];

        return $this->respond($data, 200);
    }
}