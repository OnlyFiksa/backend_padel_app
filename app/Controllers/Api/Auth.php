<?php
namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;

class Auth extends ResourceController
{
    protected $format = 'json';

    public function register()
    {
        $rules = [
            'full_name' => 'required',
            'username'  => 'required|is_unique[users.username]',
            'email'     => 'required|valid_email|is_unique[users.email]',
            'password'  => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $userModel = new UserModel();
        $data = [
            'full_name' => $this->request->getVar('full_name'),
            'username'  => $this->request->getVar('username'),
            'email'     => $this->request->getVar('email'),
            // Hashing password standar keamanan CI4
            'password'  => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT), 
        ];

        $userModel->insert($data);

        return $this->respondCreated([
            'status'  => 201,
            'message' => 'Registrasi berhasil, silakan login.',
        ]);
    }

    public function login()
    {
        $userModel = new UserModel();
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        $user = $userModel->where('username', $username)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->failUnauthorized('Username atau Password salah');
        }

        // Karena belum pakai JWT (Token) untuk versi MVP, kita return data user
        // Data ini nanti disimpan di SharedPreferences Flutter
        return $this->respond([
            'status'  => 200,
            'message' => 'Login berhasil',
            'data'    => [
                'id'        => $user['id'],
                'full_name' => $user['full_name'],
                'username'  => $user['username'],
                'email'     => $user['email']
            ]
        ]);
    }
}