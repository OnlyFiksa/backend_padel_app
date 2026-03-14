<?php
namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\BookingModel;
use App\Models\BookingDetailModel;
use App\Models\ScheduleModel;

class Booking extends ResourceController
{
    protected $format = 'json';

    // 1. Membuat Pesanan Baru
    public function create()
    {
        $db = \Config\Database::connect();
        $bookingModel = new BookingModel();
        $detailModel = new BookingDetailModel();
        $scheduleModel = new ScheduleModel();

        $userId     = $this->request->getVar('user_id');
        $courtId    = $this->request->getVar('court_id');
        $scheduleId = $this->request->getVar('schedule_id');
        $bookingDate = $this->request->getVar('booking_date'); // Format: YYYY-MM-DD

        // VALIDASI 1: Cek Double Booking
        // Cek apakah di tanggal dan jam tersebut lapangan sudah di-booking orang lain
        $isBooked = $db->table('booking_details')
                       ->join('bookings', 'bookings.id = booking_details.booking_id')
                       ->where('bookings.booking_date', $bookingDate)
                       ->where('booking_details.court_id', $courtId)
                       ->where('booking_details.schedule_id', $scheduleId)
                       ->where('bookings.status', 'Active')
                       ->countAllResults();

        if ($isBooked > 0) {
            return $this->fail('Maaf, jadwal ini sudah dipesan orang lain. Silakan pilih jam lain.');
        }

        // Ambil harga dari tabel schedules
        $schedule = $scheduleModel->find($scheduleId);
        $totalAmount = $schedule['price'];

        // Generate Order ID (Contoh: PDL-20260315-RANDOM)
        $orderId = 'PDL-' . date('Ymd', strtotime($bookingDate)) . '-' . rand(1000, 9999);

        // Mulai Transaksi Database (Mencegah data setengah masuk kalau error)
        $db->transStart();

        // Insert ke tabel bookings (Header)
        $bookingData = [
            'user_id'        => $userId,
            'order_id'       => $orderId,
            'booking_date'   => $bookingDate,
            'total_amount'   => $totalAmount,
            'status'         => 'Active',
            'payment_method' => 'Pay at Venue'
        ];
        $bookingModel->insert($bookingData);
        $newBookingId = $bookingModel->getInsertID();

        // Insert ke tabel booking_details (Isi Pesanan)
        $detailData = [
            'booking_id'  => $newBookingId,
            'court_id'    => $courtId,
            'schedule_id' => $scheduleId
        ];
        $detailModel->insert($detailData);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->failServerError('Terjadi kesalahan saat memproses pesanan.');
        }

        return $this->respondCreated([
            'status'  => 201,
            'message' => 'Pesanan berhasil dibuat! Bayar di lokasi.',
            'data'    => ['order_id' => $orderId]
        ]);
    }

    // 2. Menampilkan Riwayat Pesanan User (Untuk Bottom Navbar)
    public function getUserBookings($userId = null)
    {
        $db = \Config\Database::connect();
        
        // Mengambil data pesanan beserta detail GOR-nya
        $bookings = $db->table('bookings')
                       ->select('bookings.*, courts.court_number, venues.name as venue_name, schedules.start_time, schedules.end_time')
                       ->join('booking_details', 'booking_details.booking_id = bookings.id')
                       ->join('courts', 'courts.id = booking_details.court_id')
                       ->join('venues', 'venues.id = courts.venue_id')
                       ->join('schedules', 'schedules.id = booking_details.schedule_id')
                       ->where('bookings.user_id', $userId)
                       ->orderBy('bookings.booking_date', 'DESC')
                       ->get()
                       ->getResultArray();

        return $this->respond(['status' => 200, 'data' => $bookings]);
    }

    // 3. Batalkan Pesanan (Dengan Validasi H-1)
    public function cancelBooking($bookingId = null)
    {
        $bookingModel = new BookingModel();
        $booking = $bookingModel->find($bookingId);

        if (!$booking) {
            return $this->failNotFound('Pesanan tidak ditemukan.');
        }

        // VALIDASI 2: Cek Aturan H-1
        $bookingDate = strtotime($booking['booking_date']);
        $today = strtotime(date('Y-m-d')); // Waktu hari ini tanpa jam

        // Jika hari ini sudah sama dengan atau melewati tanggal booking
        if ($today >= $bookingDate) {
            return $this->fail('Pesanan tidak dapat dibatalkan. Pembatalan maksimal dilakukan H-1.');
        }

        // Jika lolos validasi, ubah status jadi Cancelled
        $bookingModel->update($bookingId, ['status' => 'Cancelled']);

        return $this->respond([
            'status'  => 200,
            'message' => 'Pesanan berhasil dibatalkan.'
        ]);
    }
}