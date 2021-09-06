<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSysFailedJobsTable extends Migration
{
    public function up()
    {
        Schema::create('sys_jobs_failed', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 100)->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sys_jobs_failed');
    }
}
