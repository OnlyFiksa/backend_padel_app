<?php
namespace App\Models;
use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table         = 'bookings';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['user_id', 'order_id', 'booking_date', 'total_amount', 'status', 'payment_method'];
    protected $useTimestamps = true;
}