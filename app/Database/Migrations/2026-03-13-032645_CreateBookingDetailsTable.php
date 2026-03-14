<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBookingDetailsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
    'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
    'booking_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
    'court_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
    'schedule_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('booking_id', 'bookings', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('court_id', 'courts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('schedule_id', 'schedules', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('booking_details');
    }

    public function down()
    {
        $this->forge->dropTable('booking_details');
    }
}
