<?php
namespace App\Controllers\Api;
use CodeIgniter\RESTful\ResourceController;

class Profile extends ResourceController
{
    // 1. FUNGSI STATISTIK (Menghitung Total Booking & Jam)
    public function stats($userId = null)
    {
        if (!$userId) return $this->fail('User ID dibutuhkan');

        $db = \Config\Database::connect();
        
        $bookings = $db->table('bookings')
                       ->where('user_id', $userId)
                       ->whereIn('status', ['Active', 'Completed'])
                       ->get()->getResultArray();

        $totalBookings = count($bookings);
        $totalMinutes = 0;

        foreach ($bookings as $b) {
            $totalMinutes += (int)$b['duration'];
        }

        $totalHours = round($totalMinutes / 60, 1);
        if (floor($totalHours) == $totalHours) {
            $totalHours = (int)$totalHours;
        }

        return $this->respond([
            'status' => 200,
            'data' => [
                'total_bookings' => (string)$totalBookings,
                'total_hours'    => (string)$totalHours
            ]
        ], 200);
    }

    // 2. FUNGSI UPDATE PROFILE (SUPER AMAN)
    public function updateProfile()
    {
        $db = \Config\Database::connect();
        $json = $this->request->getJSON();
        
        $userId   = $json->user_id ?? $this->request->getVar('user_id') ?? 1; 
        // Tangkap pakai berbagai kemungkinan nama variabel!
        $fullName = $json->full_name ?? $json->name ?? $this->request->getVar('full_name') ?? $this->request->getVar('name');
        $email    = $json->email ?? $this->request->getVar('email');

        $data = [];
        if ($fullName) $data['full_name'] = $fullName;
        if ($email) $data['email'] = $email;

        // Jangan eksekusi kalau datanya kosong (Ini yang bikin Error 500 tadi!)
        if (empty($data)) {
            return $this->fail('Data tidak boleh kosong atau format salah', 400);
        }

        $db->table('users')->where('id', $userId)->update($data);

        return $this->respond([
            'status' => 200, 
            'message' => 'Profile berhasil diupdate di Database!'
        ], 200);
    }

    // 3. FUNGSI UPDATE PASSWORD (SUPER AMAN & ENKRIPSI AKTIF! 🛡️)
    public function updatePassword()
    {
        $db = \Config\Database::connect();
        $json = $this->request->getJSON();
        
        $userId      = $json->user_id ?? $this->request->getVar('user_id') ?? 1;
        $newPassword = $json->new_password ?? $json->password ?? $this->request->getVar('new_password') ?? $this->request->getVar('password');

        if (!$newPassword) {
            return $this->fail('Password baru tidak boleh kosong', 400);
        }

        // 🔥 INI DIA FIX-NYA: ENKRIPSI PASSWORD SEBELUM DISIMPAN! 🔥
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Simpan password yang sudah diacak ke database
        $db->table('users')->where('id', $userId)->update([
            'password' => $hashedPassword
        ]);

        return $this->respond([
            'status' => 200, 
            'message' => 'Password berhasil diubah dan dienkripsi!'
        ], 200);
    }
}