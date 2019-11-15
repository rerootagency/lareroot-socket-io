<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetupLarerootSocketIoTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mongodb')->create('user_endpoints', function (Blueprint $table) {
            $table->unique('user_id');
            $table->unique('endpoint');
        });

        Schema::connection('mongodb')->create('channels', function (Blueprint $table) {
            $table->unique('channel');
            $table->index(['key', 'channel']);
            $table->index(['type', 'channel']);
        });

        Schema::connection('mongodb')->create('channel_endpoints', function (Blueprint $table) {
            $table->index(['channel', 'endpoint']);
        });

        Schema::connection('mongodb')->create('acknowledgements', function (Blueprint $table) {
            $table->index(['message_id', 'endpoint', 'time']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mongodb')->drop('acknowledgements');
        Schema::connection('mongodb')->drop('channel_endpoints');
        Schema::connection('mongodb')->drop('channels');
        Schema::connection('mongodb')->drop('user_endpoints');
    }
}
