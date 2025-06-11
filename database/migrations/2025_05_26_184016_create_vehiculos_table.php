<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('matricula')->unique();
            $table->string('modelo');
            $table->text('descripcion')->nullable();
            $table->integer('capacidad_personas');
            $table->integer('numero_camas');
            $table->decimal('precio_dia', 8, 2);
            $table->boolean('disponible')->default(true);
            $table->string('imagen')->nullable();
            $table->json('caracteristicas')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vehiculos');
    }
};
