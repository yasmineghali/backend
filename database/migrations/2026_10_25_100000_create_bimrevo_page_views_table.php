<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bimrevo_page_views', function (Blueprint $table) {
            $table->id();
            // Daily-rotating keyed hash: cannot be reversed to an IP and cannot link visits across days.
            $table->char('visitor_hash', 32);
            $table->string('path', 200);
            $table->string('referrer_host', 100)->nullable();
            $table->string('device', 10);
            $table->timestamp('viewed_at')->index();

            $table->index(['viewed_at', 'visitor_hash']);
            $table->index(['path', 'viewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bimrevo_page_views');
    }
};
