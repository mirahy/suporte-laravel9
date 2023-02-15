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
    public $tableName = 'salas';

    /**
     * Run the migrations.
     * @table salas
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('email', 63);
            $table->string('nome_sala');
            $table->string('modalidade', 31)->nullable()->default(null);
            $table->string('objetivo_sala', 31)->nullable()->default(null);
            $table->string('senha_aluno')->nullable()->default(null);
            $table->text('observacao')->nullable()->default(null);
            $table->text('estudantes')->nullable()->default(null);
            $table->string('mensagem')->nullable()->default(null);
            $table->decimal('carga_horaria_total_disciplina', 5, 2)->nullable()->default(null);
            $table->string('turma_nome', 15)->nullable()->default(null);
            $table->enum('avaliacao', ['nota', 'conceito'])->nullable()->default(null);
            $table->integer('turma_id')->nullable()->default(null);
            $table->integer('periodo_letivo_key')->nullable()->default(null);
            $table->string('disciplina_key', 31)->nullable()->default(null);
            $table->integer('sala_moodle_id')->nullable()->default(null);

            $table->index(["status_id"], 'status_id');

            $table->index(["solicitante_id"], 'solicitante_id');

            $table->index(["curso_id"], 'curso_id');

            $table->index(["periodo_letivo_id"], 'periodo_letivo_id');

            $table->index(["macro_id"], 'macro_id');

            $table->index(["lote_salas_id"], 'lote_salas_id');

            $table->foreignId('status_id')
                ->default('1')
                ->constrained('status')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreignId('solicitante_id')
                ->constrained('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreignId('curso_id')
                ->constrained('cursos')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreignId('periodo_letivo_id')
                ->constrained('periodo_letivos')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreignId('macro_id')
                ->constrained('macros')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreignId('lote_salas_id')
                ->nullable()
                ->default(null)
                ->constrained('lote_salas')
                ->onDelete('set null')
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
