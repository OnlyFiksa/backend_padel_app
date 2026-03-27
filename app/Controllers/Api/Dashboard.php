<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class Dashboard extends ResourceController
{
    public function index()
    {
        // Panggil library Database CI4
        $db = \Config\Database::connect();

        $userId = $this->request->getVar('user_id') ?? 1;
        
        // 1. AMBIL DATA USER (TERMASUK STATUS PROMO!) 🔥
        $user = $db->table('users')->select('full_name, has_used_promo')->where('id', $userId)->get()->getRow();
        
        $userName = $user ? explode(' ', trim($user->full_name))[0] : 'Pemain';
        $hasUsedPromo = $user ? (bool)$user->has_used_promo : false; // Ambil dari database!

        // 2. AMBIL UPCOMING MATCHES
        $upcomingQuery = $db->table('bookings b')
            ->select('v.name as title, b.booking_date as date, b.start_time as time')
            ->join('courts c', 'c.id = b.court_id')
            ->join('venues v', 'v.id = c.venue_id')
            ->where('b.user_id', $userId)
            ->where('b.status', 'Active') 
            ->where('b.booking_date >=', date('Y-m-d')) 
            ->orderBy('b.booking_date', 'ASC')
            ->orderBy('b.start_time', 'ASC')
            ->limit(1)
            ->get()
            ->getResultArray();

        $upcomingMatches = [];
        foreach ($upcomingQuery as $match) {
            $upcomingMatches[] = [
                'title' => $match['title'],
                'type'  => 'MATCH', 
                'date'  => date('d M Y', strtotime($match['date'])),
                'time'  => date('H:i', strtotime($match['time'])) . ' WIB'
            ];
        }

        // 3. AMBIL FEATURED VENUES (Query Jauh Lebih Clean!)
        $venuesQuery = $db->table('venues')->get()->getResultArray();

        $featuredVenues = [];
        foreach ($venuesQuery as $venue) {
            $priceText = $venue['price_per_hour'] ? 'Rp ' . number_format($venue['price_per_hour']/1000, 0, ',', '.') . 'k/hr' : 'Rp -/hr';
            
            // LOGIKA GAMBAR PINTAR: Bisa baca link internet maupun lokal bawaan Taufik!
            $img = $venue['image_url'];
            if ($img && strpos($img, 'http') === 0) {
                $imageUrl = $img; // Kalau link dari unsplash, langsung pakai
            } else {
                // Kalau gambar lokal, tambahin base_url
                $imageUrl = $img ? base_url('uploads/venues/' . $img) : '';
            }

            $featuredVenues[] = [
                'name'     => $venue['name'],
                'rating'   => $venue['rating'] ?? '4.9',
                'distance' => rand(1, 5) . '.' . rand(0, 9) . ' km away', // Statis dulu
                'price'    => $priceText,
                'image'    => $imageUrl // <-- GAMBAR RESMI KEMBALI!
            ];
        }

        // 4. BUNGKUS DAN KIRIM KE FLUTTER
        $data = [
            'status' => 200,
            'message' => 'Berhasil mengambil data dashboard',
            'data' => [
                'user_name' => $userName,
                'has_used_promo' => $hasUsedPromo, // <--- KUNCI PENGAMAN PROMO!
                'upcoming_matches' => $upcomingMatches,
                'featured_venues' => $featuredVenues
            ]
        ];

        return $this->respond($data, 200);
    }
}