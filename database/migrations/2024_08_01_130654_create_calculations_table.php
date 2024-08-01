<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalculationsTable extends Migration
{
    public function up()
    {
        Schema::create('calculations', function (Blueprint $table) {
            $table->id();
            $table->decimal('purchase_price', 8, 2);
            $table->decimal('logistics_cost', 8, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('tax_rate', 5, 2)->nullable();
            $table->decimal('selling_price', 8, 2)->nullable();
            $table->decimal('margin_percentage', 5, 2)->nullable();
            $table->decimal('category_commission_fbs', 5, 2);
            $table->decimal('category_commission_fbo', 5, 2);
            $table->decimal('height', 5, 2);
            $table->decimal('length', 5, 2);
            $table->decimal('depth', 5, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('calculations');
    }
}

