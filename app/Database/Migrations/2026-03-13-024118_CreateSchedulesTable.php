<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSchedulesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
    'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
    'court_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
    'day_of_week'  => ['type' => 'INT', 'constraint' => 1], // 1=Isnin, 7=Ahad
    'start_time'   => ['type' => 'TIME'],
    'end_time'     => ['type' => 'TIME'],
    'price'        => ['type' => 'DECIMAL', 'constraint' => '10,2'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('court_id', 'courts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('schedules');
    }

    public function down()
    {
        $this->forge->dropTable('schedules');
    }
}
