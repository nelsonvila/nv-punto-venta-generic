<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('ventas')) {

            exit(0);

        } else {

            Schema::create('ventas', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('idcliente')->unsigned()->nullable();
                $table->integer('idusuario')->unsigned();
                $table->string('num_venta', 10);
                $table->dateTime('fecha_venta');
                $table->decimal('total', 11, 2);
                $table->string('estado', 20);
                $table->timestamps();
            });

        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ventas');
    }
}
