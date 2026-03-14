<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class Explore extends ResourceController
{
    public function getVenues()
    {
        $db = \Config\Database::connect();
        
        // 1. Ambil daftar kota dari tabel locations
        $locationsQuery = $db->table('locations')->select('city_name')->get()->getResultArray();
        $categories = ['Semua']; // 'Semua' jadi index ke-0
        foreach ($locationsQuery as $loc) {
            $categories[] = $loc['city_name'];
        }

        // 2. Ambil semua venues
        $venuesQuery = $db->table('venues v')
            ->select('v.id, v.name, v.image_url, l.city_name as location')
            ->join('locations l', 'l.id = v.location_id', 'left')
            ->select('(SELECT MIN(price) FROM schedules s JOIN courts c ON c.id = s.court_id WHERE c.venue_id = v.id) as starting_price')
            ->get()
            ->getResultArray();

        $venuesData = [];
        foreach ($venuesQuery as $venue) {
            $priceText = $venue['starting_price'] ? 'Rp ' . number_format($venue['starting_price']/1000, 0, ',', '.') . 'k' : 'Rp -';
            $imageUrl = $venue['image_url'] ? base_url('uploads/venues/' . $venue['image_url']) : '';
            
            $venuesData[] = [
                'id'        => $venue['id'],
                'name'      => $venue['name'],
                'location'  => $venue['location'] ?? 'Jakarta',
                'distance'  => rand(1, 8) . '.' . rand(0, 9) . ' km',
                'price'     => $priceText,
                'rating'    => '4.' . rand(5, 9),
                'isPopular' => (rand(0, 1) == 1),
                'image'     => $imageUrl
            ];
        }

        return $this->respond([
            'status' => 200,
            'message' => 'Berhasil mengambil data katalog lapangan',
            'data' => [
                'categories' => $categories, // <-- Kirim daftar lokasi ke Flutter
                'venues' => $venuesData      // <-- Kirim daftar lapangan
            ]
        ], 200);
    }
}