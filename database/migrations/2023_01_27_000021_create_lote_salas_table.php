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
    public $tableName = 'lote_salas';

    /**
     * Run the migrations.
     * @table lote_salas
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('descricao', 63);
            $table->tinyInteger('is_salas_criadas');
            $table->tinyInteger('is_estudantes_inseridos');

            $table->unique(["descricao"], 'descricao');

            $table->index(["periodo_letivo_id"], 'periodo_letivo_id');

            $table->index(["faculdade_id"], 'faculdade_id');

            $table->index(["curso_id"], 'curso_id');

            $table->foreignId('periodo_letivo_id')
                ->constrained('periodo_letivos')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreignId('faculdade_id')
                ->constrained('faculdades')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreignId('curso_id')
                ->nullable()
                ->default(null)
                ->constrained('cursos')
                ->onDelete('restrict')
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
