<?php
namespace App\Models;
use CodeIgniter\Model;

class BookingDetailModel extends Model
{
    protected $table         = 'booking_details';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['booking_id', 'court_id', 'schedule_id'];
}