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
    public $tableName = 'salas_simplificadas';

    /**
     * Run the migrations.
     * @table salas_simplificadas
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('nome_sala');
            $table->string('disciplina_key', 31)->nullable()->default(null);
            $table->integer('periodo_letivo_key')->nullable()->default(null);
            $table->integer('turma_id');
            $table->string('turma_nome', 15);
            $table->decimal('carga_horaria_total_disciplina', 5, 2)->nullable()->default(null);
            $table->enum('avaliacao', ['nota', 'conceito'])->nullable()->default(null);
            $table->integer('sala_moodle_id')->nullable()->default(null);
            $table->string('link_moodle')->nullable()->default(null);

            $table->index(["professor_id"], 'professor_id');

            $table->index(["periodo_letivo_id"], 'periodo_letivo_id');

            $table->index(["curso_id"], 'curso_id');

            $table->index(["lote_id"], 'lote_id');

            $table->foreignId('professor_id')
                ->nullable()
                ->default(null)
                ->constrained('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreignId('periodo_letivo_id')
                ->constrained('periodo_letivos')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreignId('curso_id')
                ->nullable()
                ->default(null)
                ->constrained('cursos')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreignId('lote_id')
                ->constrained('lote_salas_simplificados')
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
