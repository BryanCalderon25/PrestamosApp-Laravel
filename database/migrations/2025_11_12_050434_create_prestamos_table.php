<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_cliente');
            $table->decimal('monto_solicitado', 15, 2);
            $table->integer('historial_crediticio');
            $table->decimal('ingresos_mensuales', 15, 2);
            $table->string('estado')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestamos');
    }
};
