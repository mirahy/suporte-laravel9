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
    public $tableName = 'macros_super_macros';

    /**
     * Run the migrations.
     * @table macros_super_macros
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->integer('ordem');
            $table->string('campo', 31);
            $table->string('operador', 15);
            $table->string('valor')->nullable()->default(null);

            $table->index(["macro_id"], 'macro_id');

            $table->index(["super_macro_id"], 'super_macro_id');

            $table->foreignId('macro_id')
                ->constrained('macros')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreignId('super_macro_id')
                ->constrained('super_macros')
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
