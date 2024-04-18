<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('with_draw_invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->decimal('amount', 28,2);
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('with_draw_invoices');
    }
};
