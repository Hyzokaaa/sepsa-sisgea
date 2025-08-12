<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Provincias
        Schema::create('provincias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
        });

        // Empresas
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('siglas', 20)->nullable();
            $table->string('direccion')->nullable();
            $table->boolean('activo')->default(true);
            $table->text('descripcion')->nullable();
            $table->timestamps();
            $table->unique(['nombre']);
        });

        // Unidades de medida
        Schema::create('unidades_medida', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('abreviatura', 15)->unique();
            $table->timestamps();
        });

        // UEBs (Unidades Empresariales de Base)
        Schema::create('uebs', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('provincia_id')->nullable()->constrained('provincias')->nullOnDelete();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->unique(['empresa_id', 'nombre']);
        });

        // Clientes
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('direccion')->nullable();
            $table->string('siglas', 20)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->unique(['nombre']);
        });

        // Items (entidad base para productos y grupos)
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->foreignId('unidad_medida_id')->nullable()->constrained('unidades_medida')->nullOnDelete();
            $table->enum('tipo', ['producto', 'grupo']);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->index('tipo');
        });

        // Grupos (especialización de items)
        Schema::create('grupos', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')->primary();
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
            $table->unsignedBigInteger('grupo_padre_id')->nullable();
            $table->foreign('grupo_padre_id')->references('item_id')->on('grupos')->nullOnDelete();
            $table->index('grupo_padre_id');
        });

        // Productos (especialización de items)
        Schema::create('productos', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')->primary();
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
            $table->string('codigo', 50)->unique();
            $table->string('imagen')->nullable();
            $table->unsignedBigInteger('grupo_id')->nullable();
            $table->foreign('grupo_id')->references('item_id')->on('grupos')->nullOnDelete();
            $table->index('grupo_id');
        });

        // Periodos de planificación / demanda
        Schema::create('periodos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->timestamps();
            $table->index(['fecha_inicio', 'fecha_fin']);
        });

        // Planificaciones (por UEB, periodo y item - puede ser producto o grupo) con valores plan y pronostico
        Schema::create('planificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ueb_id')->constrained('uebs')->cascadeOnDelete();
            $table->foreignId('periodo_id')->constrained('periodos')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->decimal('plan', 15, 3)->default(0); // valor planificado
            $table->decimal('pronostico', 15, 3)->default(0); // valor pronosticado
            $table->string('estado', 30)->default('borrador');
            $table->timestamps();
            $table->unique(['ueb_id', 'periodo_id', 'item_id']);
        });

        // Demandas (por cliente, periodo e item - producto o grupo)
        Schema::create('demandas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('periodo_id')->constrained('periodos')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->decimal('cantidad', 15, 3)->default(0);
            $table->timestamps();
            $table->unique(['cliente_id', 'periodo_id', 'item_id']);
        });

        // Inventarios (stock por UEB y producto)
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ueb_id')->constrained('uebs')->cascadeOnDelete();
            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('item_id')->on('productos')->cascadeOnDelete();
            $table->decimal('cantidad', 15, 3)->default(0);
            $table->decimal('costo_unitario', 15, 4)->nullable();
            $table->timestamps();
            $table->unique(['ueb_id', 'producto_id']);
        });

        // Recetas (para elaboración de un producto final)
        Schema::create('recetas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('item_id')->on('productos')->cascadeOnDelete();
            $table->string('nombre')->nullable();
            $table->text('descripcion')->nullable();
            $table->timestamps();
            $table->unique(['producto_id']);
        });

        // Detalle de recetas (componentes: item puede ser producto o grupo)
        Schema::create('receta_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receta_id')->constrained('recetas')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->decimal('cantidad', 15, 4)->default(0);
            $table->timestamps();
            $table->unique(['receta_id', 'item_id']);
        });

        // Operaciones (tabla base CTI)
        Schema::create('operaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ueb_id')->constrained('uebs')->cascadeOnDelete(); // origen
            $table->enum('tipo', ['traslado', 'venta', 'produccion', 'elaboracion']);
            $table->dateTime('fecha')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->index(['tipo', 'fecha']);
        });

        // Traslados (especialización)
        Schema::create('traslados', function (Blueprint $table) {
            $table->unsignedBigInteger('operacion_id')->primary();
            $table->foreign('operacion_id')->references('id')->on('operaciones')->cascadeOnDelete();
            $table->foreignId('destino_ueb_id')->constrained('uebs')->cascadeOnDelete();
        });

        // Ventas (especialización)
        Schema::create('ventas', function (Blueprint $table) {
            $table->unsignedBigInteger('operacion_id')->primary();
            $table->foreign('operacion_id')->references('id')->on('operaciones')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
        });

        // Producciones (especialización)
        Schema::create('producciones', function (Blueprint $table) {
            $table->unsignedBigInteger('operacion_id')->primary();
            $table->foreign('operacion_id')->references('id')->on('operaciones')->cascadeOnDelete();
            // Campos futuros específicos de producción pueden añadirse aquí
        });

        // Elaboraciones (especialización con receta)
        Schema::create('elaboraciones', function (Blueprint $table) {
            $table->unsignedBigInteger('operacion_id')->primary();
            $table->foreign('operacion_id')->references('id')->on('operaciones')->cascadeOnDelete();
            $table->foreignId('receta_id')->constrained('recetas')->cascadeOnDelete();
        });

        // Detalle de operaciones (items afectados con cantidades +/-)
        Schema::create('operacion_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operacion_id')->constrained('operaciones')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->decimal('cantidad', 15, 3);
            $table->string('rol', 30)->nullable(); // ej: origen/destino/consumo/producido
            $table->timestamps();
            $table->index(['operacion_id', 'item_id']);
        });

        // Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->timestamps();
            $table->unique(['nombre']);
        });

        // Accesos (permisos de alto nivel)
        Schema::create('accesos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->text('descripcion')->nullable();
            $table->timestamps();
            $table->unique(['nombre']);
        });

        // Pivot rol-acceso
        Schema::create('acceso_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('acceso_id')->constrained('accesos')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['role_id', 'acceso_id']);
        });

        // Pivot role-user (users table ya existe por Laravel)
        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['role_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('acceso_role');
        Schema::dropIfExists('accesos');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('operacion_items');
        Schema::dropIfExists('elaboraciones');
        Schema::dropIfExists('producciones');
        Schema::dropIfExists('ventas');
        Schema::dropIfExists('traslados');
        Schema::dropIfExists('operaciones');
        Schema::dropIfExists('receta_items');
        Schema::dropIfExists('recetas');
        Schema::dropIfExists('inventarios');
        Schema::dropIfExists('demandas');
        Schema::dropIfExists('planificaciones');
        Schema::dropIfExists('periodos');
        Schema::dropIfExists('productos');
        Schema::dropIfExists('grupos');
        Schema::dropIfExists('items');
        Schema::dropIfExists('clientes');
        Schema::dropIfExists('uebs');
        Schema::dropIfExists('unidades_medida');
        Schema::dropIfExists('empresas');
        Schema::dropIfExists('provincias');
    }
};
