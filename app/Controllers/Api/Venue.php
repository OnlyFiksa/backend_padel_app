<?php
namespace App\Controllers\Api;
use CodeIgniter\RESTful\ResourceController;

class Venue extends ResourceController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // Ambil data venues dari database (sekarang udah ada 2 venue: Jaksel & Jaktim)
        $venuesQuery = $db->table('venues')->get()->getResultArray();

        $venuesList = [];
        $locations = ['Semua']; // Kategori default

        foreach ($venuesQuery as $v) {
            // Logika gambar pintar (seperti di Dashboard)
            $img = $v['image_url'];
            if ($img && strpos($img, 'http') === 0) {
                $imageUrl = $img;
            } else {
                $imageUrl = $img ? base_url('uploads/venues/' . $img) : '';
            }

            // Kumpulkan daftar lokasi unik (biar bisa jadi filter)
            $locText = trim(explode(',', $v['location'])[1] ?? $v['location']); // Ambil teks kotanya
            if (!in_array($locText, $locations)) {
                $locations[] = $locText;
            }

            $venuesList[] = [
                'id'       => (int)$v['id'],
                'name'     => $v['name'],
                'location' => $locText, // Lokasi singkat untuk filter
                'full_location' => $v['location'],
                'rating'   => $v['rating'] ?? '4.9',
                'distance' => rand(1, 5) . '.' . rand(0, 9) . ' km', // Simulasi jarak
                'price'    => 'Rp ' . number_format($v['price_per_hour']/1000, 0, ',', '.') . 'k',
                'raw_price'=> $v['price_per_hour'],
                'image'    => $imageUrl,
                'isPopular'=> rand(0,1) == 1 // Random Popular tag
            ];
        }

        return $this->respond([
            'status' => 200,
            'message' => 'Success',
            'data' => [
                'categories' => $locations, // Akan ngirim: ["Semua", "Jakarta Selatan", "Jakarta Timur"]
                'venues' => $venuesList
            ]
        ], 200);
    }
}