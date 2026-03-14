<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBookingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
    'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
    'user_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
    'order_id'       => ['type' => 'VARCHAR', 'constraint' => '50', 'unique' => true],
    'booking_date'   => ['type' => 'DATE'],
    'total_amount'   => ['type' => 'DECIMAL', 'constraint' => '10,2'],
    'status'         => ['type' => 'ENUM', 'constraint' => ['Active', 'Completed', 'Cancelled'], 'default' => 'Active'],
    'payment_method' => ['type' => 'VARCHAR', 'constraint' => '50', 'default' => 'Pay at Venue'],
    'created_at'     => ['type' => 'DATETIME', 'null' => true],
    'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('bookings');
    }

    public function down()
    {
        $this->forge->dropTable('bookings');
    }
}
