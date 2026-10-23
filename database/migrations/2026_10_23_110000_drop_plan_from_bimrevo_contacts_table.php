<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // The contact form has no plan field; plans belong to the future pricing/payment flow.
    public function up(): void
    {
        Schema::table('bimrevo_contacts', function (Blueprint $table) {
            $table->dropColumn('plan');
        });
    }

    public function down(): void
    {
        Schema::table('bimrevo_contacts', function (Blueprint $table) {
            $table->string('plan', 50)->nullable()->after('company');
        });
    }
};
