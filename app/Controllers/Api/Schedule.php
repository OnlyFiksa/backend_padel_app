<?php
namespace App\Controllers\Api;
use CodeIgniter\RESTful\ResourceController;

class Schedule extends ResourceController
{
    public function available()
    {
        $db = \Config\Database::connect();
        $courtId = $this->request->getVar('court_id') ?? 1;
        $date = $this->request->getVar('date');

        if (!$date) return $this->fail('Tanggal wajib dikirim');

        // Langsung ambil dari tabel bookings!
        $bookings = $db->table('bookings')
                       ->where('court_id', $courtId)
                       ->where('booking_date', $date)
                       ->whereIn('status', ['Active', 'Completed'])
                       ->get()->getResultArray();

        $bookedTimes = [];
        foreach ($bookings as $b) {
            $startHour = (int)substr($b['start_time'], 0, 2);
            $slots = (int)$b['duration'] / 60; // 60 menit = 1 slot, 120 menit = 2 slot
            
            for ($i = 0; $i < $slots; $i++) {
                $bookedTimes[] = sprintf('%02d:00:00', $startHour + $i);
            }
        }

        $schedules = [];
        for ($h = 8; $h <= 23; $h++) {
            $timeString = sprintf('%02d:00:00', $h);
            $isAvailable = !in_array($timeString, $bookedTimes);

            // Mesin Waktu: Jam yang udah lewat hari ini = false
            if ($date == date('Y-m-d') && $h <= (int)date('H')) {
                $isAvailable = false;
            }

            $schedules[] = [
                'start_time'   => $timeString,
                'is_available' => $isAvailable,
            ];
        }

        return $this->respond(['status' => 200, 'data' => $schedules]);
    }
}