<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['full_name', 'username', 'email', 'password', 'role'];
    protected $useTimestamps    = true; 
}