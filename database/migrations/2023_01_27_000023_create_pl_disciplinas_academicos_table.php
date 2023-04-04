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
    public $tableName = 'pl_disciplinas_academicos';

    /**
     * Run the migrations.
     * @table pl_disciplinas_academicos
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('disciplina');
            $table->text('estudantes')->nullable()->default(null);
            $table->bigInteger('disciplina_key')->nullable()->default(null);

            $table->index(["curso_id"], 'curso_id');

            $table->index(["periodo_letivo_id"], 'periodo_letivo_id');

            $table->foreignId('curso_id')
                ->constrained('cursos')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreignId('periodo_letivo_id')
                ->constrained('periodo_letivos')
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
