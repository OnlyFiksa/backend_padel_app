<?php
namespace App\Models;
use CodeIgniter\Model;

class VenueModel extends Model
{
    protected $table         = 'venues';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['location_id', 'name', 'address', 'description', 'image_url'];
}