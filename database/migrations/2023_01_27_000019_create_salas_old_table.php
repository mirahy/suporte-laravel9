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
    public $tableName = 'salas_old';

    /**
     * Run the migrations.
     * @table salas_old
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('nome_professor');
            $table->string('email', 63);
            $table->string('faculdade');
            $table->string('curso');
            $table->string('nome_sala');
            $table->string('modalidade', 31)->nullable()->default(null);
            $table->string('objetivo_sala', 31)->nullable()->default(null);
            $table->string('senha_aluno')->nullable()->default(null);
            $table->string('senha_professor');
            $table->text('observacao')->nullable()->default(null);
            $table->string('mensagem')->nullable()->default(null);

            $table->index(["status_id"], 'status_id');

            $table->index(["macro_id"], 'macro_id');

            $table->foreignId('status_id')
                ->default('1')
                ->constrained('status')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreignId('macro_id')
                ->constrained('macros')
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
