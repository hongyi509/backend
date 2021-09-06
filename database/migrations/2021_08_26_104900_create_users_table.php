<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique();
            $table->timestamp('last_seen_at')->useCurrent();
            $table->string('last_email', 250)->nullable();
            $table->string('last_version', 15)->nullable();
            $table->string('last_user_agent', 1000)->nullable();
            $table->unsignedBigInteger('passport_count')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}