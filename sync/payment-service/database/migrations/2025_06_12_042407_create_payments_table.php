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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');  // Tidak perlu foreign key constraint
            $table->unsignedBigInteger('user_id');   // Tidak perlu foreign key constraint
            $table->decimal('amount', 10, 2);        // Jumlah yang dibayar
            $table->string('payment_method');        // Metode pembayaran
            $table->string('payment_status')->default('pending');  // Status pembayaran
            $table->timestamp('payment_date')->useCurrent();  // Tanggal pembayaran
            $table->timestamps();
        });
    }



    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
