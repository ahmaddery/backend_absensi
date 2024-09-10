<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMidtransColumnsToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_id')->nullable();
            $table->timestamp('purchase_date')->nullable();
            $table->timestamp('transaction_time')->nullable();
            $table->string('transaction_status')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('status_message')->nullable();
            $table->string('status_code')->nullable();
            $table->string('signature_key')->nullable();
            $table->timestamp('settlement_time')->nullable();
            $table->string('payment_type')->nullable();
            $table->decimal('gross_amount', 12, 2)->nullable();
            $table->string('fraud_status')->nullable();
            $table->string('currency')->nullable();
            $table->string('merchant_id')->nullable();
            $table->decimal('discount', 12, 2)->nullable(); // Add this line for the discount column
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'order_id',
                'purchase_date',
                'transaction_time',
                'transaction_status',
                'transaction_id',
                'status_message',
                'status_code',
                'signature_key',
                'settlement_time',
                'payment_type',
                'gross_amount',
                'fraud_status',
                'currency',
                'merchant_id',
                'discount', // Make sure to drop the column in the down method
            ]);
        });
    }
}
