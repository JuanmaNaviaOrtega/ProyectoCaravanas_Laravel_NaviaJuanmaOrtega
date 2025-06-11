<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vehiculos', function (Blueprint $table) {
            // Añadir columna si no existe
            if (!Schema::hasColumn('vehiculos', 'capacidad_personas')) {
                $table->integer('capacidad_personas')->after('descripcion')->default(2);
            }
            
            if (!Schema::hasColumn('vehiculos', 'numero_camas')) {
                $table->integer('numero_camas')->after('capacidad_personas')->default(1);
            }
            
            if (!Schema::hasColumn('vehiculos', 'imagen')) {
                $table->string('imagen')->after('precio_dia')->nullable();
            }
            
            if (!Schema::hasColumn('vehiculos', 'caracteristicas')) {
                $table->json('caracteristicas')->after('imagen')->nullable();
            }
            
            if (!Schema::hasColumn('vehiculos', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down()
    {
        Schema::table('vehiculos', function (Blueprint $table) {
            $table->dropColumn([
                'capacidad_personas',
                'numero_camas',
                'imagen',
                'caracteristicas',
                'deleted_at'
            ]);
        });
    }
};
