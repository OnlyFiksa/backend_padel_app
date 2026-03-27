<?php
namespace App\Models;
use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table      = 'bookings';
    protected $primaryKey = 'id';
    
    // Satpam baru: Izinkan semua kolom penting masuk!
    protected $allowedFields = [
        'user_id', 'court_id', 'order_id', 'booking_date', 
        'start_time', 'duration', 'total_amount', 'status', 'payment_method'
    ];

    protected $useTimestamps = true;
}