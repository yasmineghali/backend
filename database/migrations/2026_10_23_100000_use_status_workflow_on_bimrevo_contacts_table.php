<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Same workflow as GeoRevo contacts: status (new / in_progress / handled), internal notes, handled date.
    // Replaces the read/unread flag.
    public function up(): void
    {
        Schema::table('bimrevo_contacts', function (Blueprint $table) {
            $table->dropIndex(['read_at']);
        });

        Schema::table('bimrevo_contacts', function (Blueprint $table) {
            $table->dropColumn('read_at');
            $table->string('status', 20)->default('new')->index()->after('source_url');
            $table->text('admin_notes')->nullable()->after('status');
            $table->timestamp('handled_at')->nullable()->after('user_agent');
        });
    }

    public function down(): void
    {
        Schema::table('bimrevo_contacts', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('bimrevo_contacts', function (Blueprint $table) {
            $table->dropColumn(['status', 'admin_notes', 'handled_at']);
            $table->timestamp('read_at')->nullable()->index();
        });
    }
};
