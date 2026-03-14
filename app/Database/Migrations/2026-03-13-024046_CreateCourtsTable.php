<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateCourtsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'venue_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'court_number' => ['type' => 'VARCHAR', 'constraint' => '50'],
            'is_active'    => ['type' => 'BOOLEAN', 'default' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('venue_id', 'venues', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('courts');
    }

    public function down()
    {
        $this->forge->dropTable('courts');
    }
}