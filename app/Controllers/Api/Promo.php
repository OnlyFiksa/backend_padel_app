<?php
namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class Promo extends ResourceController
{
    protected $format = 'json';

    public function apply()
    {
        $db = \Config\Database::connect();
        
        // Tangkap data dari Flutter
        $userId = $this->request->getVar('user_id');
        $promoCode = $this->request->getVar('promo_code');

        if (!$userId || !$promoCode) {
            return $this->respond([
                'status'  => 400,
                'message' => 'User ID dan Kode Promo wajib diisi!'
            ], 400);
        }

        // LOGIKA PROMO: PENGGUNA BARU (KODE: NEW20)
        if (strtoupper($promoCode) === 'NEW20') {
            
            // Cek ke database: Apakah user ini sudah pernah booking?
            $bookingCount = $db->table('bookings')
                               ->where('user_id', $userId)
                               ->countAllResults();

            if ($bookingCount > 0) {
                // Kalau udah pernah booking, DITOLAK!
                return $this->respond([
                    'status'  => 400,
                    'message' => 'Yah, promo ini khusus untuk transaksi pertamamu!'
                ], 400);
            }

            // Kalau belum pernah booking, BERHASIL! Kasih diskon 20%
            return $this->respond([
                'status'  => 200,
                'message' => 'Hore! Promo Pengguna Baru 20% berhasil digunakan!',
                'data'    => [
                    'promo_code' => 'NEW20',
                    'discount_percentage' => 20
                ]
            ], 200);
        }

        // Kalau kode promonya ngasal
        return $this->respond([
            'status'  => 400,
            'message' => 'Kode promo tidak ditemukan atau sudah kadaluarsa.'
        ], 400);
    }
}