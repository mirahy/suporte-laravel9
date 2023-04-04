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
    public $tableName = 'reservas';

    /**
     * Run the migrations.
     * @table reservas
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('title');
            $table->timestamp('start');
            $table->timestamp('end')->nullable()->default(null);
            $table->tinyInteger('allDay');
            $table->tinyInteger('maisDay');
            $table->char('backgroundColor', 7)->nullable()->default(null);
            $table->integer('ref_id')->nullable()->default(null);
            $table->integer('usuario_id');
            $table->text('observacao')->nullable()->default(null);
            $table->text('justificativa')->nullable()->default(null);

            $table->index(["ref_id"], 'ref_id');

            $table->index(["recurso_id"], 'recurso_id');

            $table->index(["status_id"], 'status_id');
            

            $table->foreignId('recurso_id')
                ->constrained('recursos')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreignId('status_id')
                ->constrained('status')
                ->default('1')
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
