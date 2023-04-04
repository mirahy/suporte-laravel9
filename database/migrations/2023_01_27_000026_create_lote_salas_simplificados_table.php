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
    public $tableName = 'lote_salas_simplificados';

    /**
     * Run the migrations.
     * @table lote_salas_simplificados
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('descricao', 63);
            $table->integer('sala_provao_id')->nullable()->default(null);
            $table->string('sufixo', 63)->nullable()->default(null);

            $table->unique(["descricao"], 'descricao');

            $table->index(["servidor_moodle_id"], 'servidor_moodle_id');

            $table->index(["super_macro_id"], 'super_macro_id');

            $table->index(["grupo_id"], 'grupo_id');

            $table->foreignId('servidor_moodle_id')
                ->constrained('servidores_moodle')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreignId('super_macro_id')
                ->nullable()
                ->default(null)
                ->constrained('super_macros')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreignId('grupo_id')
                ->constrained('grupo_lotes_simplificados')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->nullableTimestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
};
