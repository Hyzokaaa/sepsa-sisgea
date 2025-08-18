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
        // Provinces
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Companies
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('acronym', 20)->nullable();
            $table->string('address')->nullable();
            $table->boolean('active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['name']);
        });

        // Measurement units
        Schema::create('measurement_units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('abbreviation', 15)->unique();
            $table->timestamps();
        });

        // Business units (UEBs)
        Schema::create('business_units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('province_id')->nullable()->constrained('provinces')->nullOnDelete();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['company_id', 'name']);
        });

        // Clients
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('acronym', 20)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['name']);
        });

        // Items (base entity for products and groups)
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('measurement_unit_id')->nullable()->constrained('measurement_units')->nullOnDelete();
            $table->enum('type', ['product', 'group']);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index('type');
        });

        // Groups (item specialization)
        Schema::create('groups', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')->primary();
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
            $table->unsignedBigInteger('parent_group_id')->nullable();
            $table->index('parent_group_id');
        });
        
        // Add self-referencing foreign key after table creation
        Schema::table('groups', function (Blueprint $table) {
            $table->foreign('parent_group_id')->references('item_id')->on('groups')->nullOnDelete();
        });

        // Products (item specialization)
        Schema::create('products', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')->primary();
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
            $table->string('code', 50)->unique();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();
            $table->foreign('group_id')->references('item_id')->on('groups')->nullOnDelete();
            $table->index('group_id');
        });

        // Planning/demand periods
        Schema::create('periods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
            $table->index(['start_date', 'end_date']);
        });

        // Planning (by business unit, period and item - can be product or group) with plan and forecast values
        Schema::create('plannings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_unit_id')->constrained('business_units')->cascadeOnDelete();
            $table->foreignId('period_id')->constrained('periods')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->decimal('plan', 15, 3)->default(0); // planned value
            $table->decimal('forecast', 15, 3)->default(0); // forecasted value
            $table->string('status', 30)->default('draft');
            $table->timestamps();
            $table->unique(['business_unit_id', 'period_id', 'item_id']);
        });

        // Demands (by client, period and item - product or group)
        Schema::create('demands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('period_id')->constrained('periods')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->decimal('quantity', 15, 3)->default(0);
            $table->timestamps();
            $table->unique(['client_id', 'period_id', 'item_id']);
        });

        // Inventories (stock by business unit and product)
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_unit_id')->constrained('business_units')->cascadeOnDelete();
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('item_id')->on('products')->cascadeOnDelete();
            $table->decimal('quantity', 15, 3)->default(0);
            $table->decimal('unit_cost', 15, 4)->nullable();
            $table->timestamps();
            $table->unique(['business_unit_id', 'product_id']);
        });

        // Recipes (for manufacturing a final product)
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('item_id')->on('products')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['product_id']);
        });

        // Recipe detail (components: item can be product or group)
        Schema::create('recipe_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('recipes')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->decimal('quantity', 15, 4)->default(0);
            $table->timestamps();
            $table->unique(['recipe_id', 'item_id']);
        });

        // Operations (CTI base table)
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_unit_id')->constrained('business_units')->cascadeOnDelete(); // origin
            $table->enum('type', ['transfer', 'sale', 'production', 'manufacture']);
            $table->dateTime('date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['type', 'date']);
        });

        // Transfers (specialization)
        Schema::create('transfers', function (Blueprint $table) {
            $table->unsignedBigInteger('operation_id')->primary();
            $table->foreign('operation_id')->references('id')->on('operations')->cascadeOnDelete();
            $table->foreignId('destination_business_unit_id')->constrained('business_units')->cascadeOnDelete();
        });

        // Sales (specialization)
        Schema::create('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('operation_id')->primary();
            $table->foreign('operation_id')->references('id')->on('operations')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
        });

        // Productions (specialization)
        Schema::create('productions', function (Blueprint $table) {
            $table->unsignedBigInteger('operation_id')->primary();
            $table->foreign('operation_id')->references('id')->on('operations')->cascadeOnDelete();
            // Future production-specific fields can be added here
        });

        // Manufactures (specialization with recipe)
        Schema::create('manufactures', function (Blueprint $table) {
            $table->unsignedBigInteger('operation_id')->primary();
            $table->foreign('operation_id')->references('id')->on('operations')->cascadeOnDelete();
            $table->foreignId('recipe_id')->constrained('recipes')->cascadeOnDelete();
        });

        // Operation detail (items affected with quantities +/-)
        Schema::create('operation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_id')->constrained('operations')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->decimal('quantity', 15, 3);
            $table->string('role', 30)->nullable(); // e.g.: origin/destination/consumption/produced
            $table->timestamps();
            $table->index(['operation_id', 'item_id']);
        });

        // Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
            $table->unique(['name']);
        });

        // Access permissions (high level)
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['name']);
        });

        // Pivot role-permission
        Schema::create('permission_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['role_id', 'permission_id']);
        });

        // Pivot role-user (users table already exists by Laravel)
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
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('operation_items');
        Schema::dropIfExists('manufactures');
        Schema::dropIfExists('productions');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('transfers');
        Schema::dropIfExists('operations');
        Schema::dropIfExists('recipe_items');
        Schema::dropIfExists('recipes');
        Schema::dropIfExists('inventories');
        Schema::dropIfExists('demands');
        Schema::dropIfExists('plannings');
        Schema::dropIfExists('periods');
        Schema::dropIfExists('products');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('items');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('business_units');
        Schema::dropIfExists('measurement_units');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('provinces');
    }
};
