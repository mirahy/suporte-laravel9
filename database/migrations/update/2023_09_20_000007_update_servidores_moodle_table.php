<?php

namespace Database\Migrations;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'servidores_moodle';

    /**
     * Run the migrations.
     * @table servidores_moodle
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->string('nome_banco', 50)->nullable()->after('url');
            $table->string('ip_banco', 50)->nullable()->after('nome_banco');
            $table->string('ip_server', 50)->nullable(false)->after('ip_banco');
            $table->string('prefixo', 10)->nullable()->after('ip_server');
            $table->enum('status', ['ON', 'OFF'])->nullable(false)->default('OFF')->after('prefixo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salas', function (Blueprint $table) {
            $table->dropColumn('nome_banco');
            $table->dropColumn('ip_banco');
            $table->dropColumn('ip_server');
            $table->dropColumn('prefixo');
            $table->dropColumn('status');
        });
    }
};
