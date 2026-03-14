<?php
namespace App\Models;
use CodeIgniter\Model;

class ScheduleModel extends Model
{
    protected $table         = 'schedules';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['court_id', 'day_of_week', 'start_time', 'end_time', 'price'];
}