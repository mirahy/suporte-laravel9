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
    public $tableName = 'cursos';

    /**
     * Run the migrations.
     * @table cursos
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('nome', 127);
            $table->integer('auto_increment_ref')->nullable()->default(null);
            $table->string('curso_key', 15)->nullable()->default(null);
            $table->tinyInteger('ativo')->default('1');

            $table->index(["faculdade_id"], 'faculdade_id');


            $table->foreignId('faculdade_id')
                ->constrained('faculdades')
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
