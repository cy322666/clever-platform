<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alfacrm_transactions', function (Blueprint $table) {

            $table->renameColumn('account_id', 'webhook_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alfacrm_fields', function (Blueprint $table) {

            $table->renameColumn('webhook_id', 'account_id');
        });
    }
};
