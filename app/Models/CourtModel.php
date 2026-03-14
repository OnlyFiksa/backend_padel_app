<?php
namespace App\Models;
use CodeIgniter\Model;

class CourtModel extends Model
{
    protected $table         = 'courts';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['venue_id', 'court_number', 'is_active'];
}