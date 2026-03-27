<?php
namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\BookingModel;

class Booking extends ResourceController
{
    public function create()
    {
        $db = \Config\Database::connect();
        $bookingModel = new BookingModel();
        
        $json = $this->request->getJSON();
        $userId      = $json->user_id ?? $this->request->getVar('user_id');
        $courtId     = $json->court_id ?? $this->request->getVar('court_id') ?? 1;
        $bookingDate = $json->booking_date ?? $this->request->getVar('booking_date');
        $startTime   = $json->start_time ?? $this->request->getVar('start_time');
        $duration    = $json->duration ?? $this->request->getVar('duration');
        $totalAmount = $json->total_amount ?? $this->request->getVar('total_amount');

        // Validasi Anti-Double Booking yang Super Akurat
        $startHour = (int)substr($startTime, 0, 2);
        $endHour = $startHour + ($duration / 60);

        $existingBookings = $db->table('bookings')
            ->where('booking_date', $bookingDate)
            ->where('court_id', $courtId)
            ->whereIn('status', ['Active', 'Completed'])
            ->get()->getResultArray();

        foreach ($existingBookings as $b) {
            $bStart = (int)substr($b['start_time'], 0, 2);
            $bEnd = $bStart + ($b['duration'] / 60);
            
            // Logika Tabrakan
            if (max($startHour, $bStart) < min($endHour, $bEnd)) {
                return $this->fail('Maaf, jam dalam durasi ini sudah dipesan orang lain.');
            }
        }

        $orderId = 'PDL-' . date('Ymd', strtotime($bookingDate)) . '-' . rand(1000, 9999);

        // Langsung Insert 1 Baris Saja! (Nggak perlu tabel details)
        $bookingModel->insert([
            'user_id' => $userId, 'court_id' => $courtId, 'order_id' => $orderId,
            'booking_date' => $bookingDate, 'start_time' => $startTime,
            'duration' => $duration, 'total_amount' => $totalAmount,
            'status' => 'Active', 'payment_method' => 'QRIS'
        ]);

        // BUG PROMO SOLVED: Tandai user ini sudah pernah booking (Hanguskan promonya!) 🔥
        $db->table('users')->where('id', $userId)->update(['has_used_promo' => 1]);

        return $this->respondCreated([
            'status' => 201, 'message' => 'Pesanan berhasil!', 'data' => ['order_id' => $orderId]
        ]);
    }

    public function getUserBookings($userId = null)
    {
        $db = \Config\Database::connect();
        $bookings = $db->table('bookings b')
                       ->select('b.*, c.court_number, v.name as venue_name')
                       ->join('courts c', 'c.id = b.court_id', 'left')
                       ->join('venues v', 'v.id = c.venue_id', 'left')
                       ->where('b.user_id', $userId)
                       ->orderBy('b.booking_date', 'DESC')
                       ->get()->getResultArray();

        return $this->respond(['status' => 200, 'data' => $bookings]);
    }

    public function cancelBooking($bookingId = null)
    {
        $bookingModel = new BookingModel();
        $bookingModel->update($bookingId, ['status' => 'Cancelled']);
        return $this->respond(['status' => 200, 'message' => 'Pesanan dibatalkan.']);
    }
}