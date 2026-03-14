<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateVenuesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'location_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => '255'],
            'address'     => ['type' => 'TEXT'],
            'description' => ['type' => 'TEXT', 'null' => true],
            'image_url'   => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('location_id', 'locations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('venues');
    }

    public function down()
    {
        $this->forge->dropTable('venues');
    }
}