<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Provinces
        Schema::create('provincias', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('sigla', 10)->nullable();
            $table->timestamps();
        });

        // Companies
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provincia_id')->nullable()->constrained('provincias')->nullOnDelete();
            $table->string('name')->unique();
            $table->string('siglas', 20)->nullable();
            $table->string('direccion')->nullable();
            $table->boolean('activo')->default(true);
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        // Business units (UEBs)
        Schema::create('uebs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('provincia_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('siglas', 20)->nullable();
            $table->string('direccion')->nullable();
            $table->boolean('activo')->default(true);
            $table->text('descripcion')->nullable();
            $table->timestamps();
            $table->unique(['empresa_id', 'name']);
        });

        // Clients
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('siglas', 20)->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['name']);
        });

        // unidad_Medidas
        Schema::create('unidad_medidas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('siglas', 10);
            $table->timestamps();
        });

        // Groups
        Schema::create('grupo_productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_medidas_id')->constrained()->nullOnDelete();
            $table->foreignId('padre_id')->nullable()->constrained('grupo_productos')->nullOnDelete();
            $table->string('name');
            $table->string('codigo');
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Products
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_productos_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('unidad_medidas_id')->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('codigo', 50)->unique();
            $table->string('imagen')->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->index(['grupo_productos_id', 'codigo']);
        });

        // periodos
        Schema::create('periodos', function (Blueprint $table) {
            $table->id();
            $table->string('ejercicio');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->index(['fecha_inicio', 'fecha_fin']);
        });

        // Planning (by business unit, period and item - can be product or group) with plan and forecast values
        Schema::create('planificacions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periodo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ueb_id')->constrained()->cascadeOnDelete();
            $table->text('observacion')->nullable();
            $table->timestamps();
            $table->unique(['periodo_id', 'ueb_id']);
        });

        // Planning details
        Schema::create('planificacion_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planificacion_id')->constrained()->cascadeOnDelete();
            $table->morphs('item'); // Para relacionar con productos o grupos
            $table->decimal('plan', 15, 2)->default(0);
            $table->decimal('pronostico', 15, 2)->default(0);
            $table->text('observacion')->nullable();
            $table->timestamps();
        });

        // Demands (by client, period and item - product or group)
        Schema::create('demandas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ueb_id')->constrained()->cascadeOnDelete();
            $table->foreignId('periodo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained()->cascadeOnDelete();
            $table->text('observacion')->nullable();
            $table->timestamps();
            $table->unique(['ueb_id', 'periodo_id', 'cliente_id']);
        });

        // Demand details
        Schema::create('demanda_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demanda_id')->constrained()->cascadeOnDelete();
            $table->morphs('item'); // Para relacionar con productos o grupos
            $table->decimal('demanda', 15, 2)->default(0);
            $table->text('observacion')->nullable();
            $table->timestamps();
        });

        // Almacenes (Warehouses)
        Schema::create('almacens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ueb_id')->constrained()->cascadeOnDelete();
            $table->string('nombre');
            $table->text('observacion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Inventories
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('almacen_id')->constrained()->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained()->cascadeOnDelete();
            $table->decimal('cantidad', 15, 2)->default(0);
            $table->decimal('precio_costo', 15, 2)->default(0);
            $table->decimal('precio_venta', 15, 2)->default(0);
            $table->timestamps();
            $table->unique(['almacen_id', 'producto_id']);
        });

        // Product ingredients (for products that are made from other products)
        Schema::create('producto_ingredientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingrediente_id')->constrained('productos')->cascadeOnDelete();
            $table->decimal('cantidad', 15, 2)->default(0);
            $table->timestamps();
        });

        // Operations (movements in inventory)
        Schema::create('operacions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->date('fecha');
            $table->enum('tipo_operacion', ['venta', 'produccion', 'elaboracion', 'traslado', 'compra']);
            $table->foreignId('almacen_id')->constrained()->cascadeOnDelete();
            $table->string('origen_destino_tipo')->nullable(); // cliente, proveedor, otro almacén
            $table->unsignedBigInteger('origen_destino_id')->nullable(); // ID del cliente, proveedor o almacén
            $table->text('observacion')->nullable();
            $table->float('importe')->default(0);
            $table->boolean('cerrado')->default(false);
            $table->timestamps();
        });

        // Operation details
        Schema::create('operacion_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operacion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained()->cascadeOnDelete();
            $table->decimal('cantidad', 15, 2)->default(0);
            $table->decimal('precio_costo', 15, 2)->default(0);
            $table->decimal('precio_venta', 15, 2)->default(0);
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operacion_detalles');
        Schema::dropIfExists('operaciones');
        Schema::dropIfExists('producto_ingredientes');
        Schema::dropIfExists('inventarios');
        Schema::dropIfExists('almacens');
        Schema::dropIfExists('demanda_detalles');
        Schema::dropIfExists('demandas');
        Schema::dropIfExists('planificacion_detalles');
        Schema::dropIfExists('planificacions');
        Schema::dropIfExists('periodos');
        Schema::dropIfExists('productos');
        Schema::dropIfExists('grupo_productos');
        Schema::dropIfExists('unidad_medidas');
        Schema::dropIfExists('clientes');
        Schema::dropIfExists('uebs');
        Schema::dropIfExists('empresas');
        Schema::dropIfExists('provincias');
    }
};
