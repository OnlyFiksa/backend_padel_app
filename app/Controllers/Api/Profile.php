<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class Profile extends ResourceController
{
    // API untuk Update Profile
    public function updateProfile()
    {
        $db = \Config\Database::connect();
        
        // Asumsi: Karena belum pakai token dinamis, kita pakai user ID 1 dulu
        $userId = $this->request->getPost('user_id') ?? 1;
        
        $data = [
            'full_name' => $this->request->getPost('full_name'),
            'email'     => $this->request->getPost('email'),
            'updated_at'=> date('Y-m-d H:i:s')
        ];

        $db->table('users')->where('id', $userId)->update($data);

        return $this->respond([
            'status' => 200,
            'message' => 'Profil berhasil diperbarui di database!'
        ], 200);
    }

    // API untuk Update Password
    public function updatePassword()
    {
        $db = \Config\Database::connect();
        $userId = $this->request->getPost('user_id') ?? 1;
        
        $newPassword = $this->request->getPost('new_password');
        
        // Menggunakan password_hash agar aman dan terbaca saat login
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $db->table('users')->where('id', $userId)->update([
            'password' => $hashedPassword,
            'updated_at'=> date('Y-m-d H:i:s')
        ]);

        return $this->respond([
            'status' => 200,
            'message' => 'Password berhasil diubah dan dienkripsi!'
        ], 200);
    }
}