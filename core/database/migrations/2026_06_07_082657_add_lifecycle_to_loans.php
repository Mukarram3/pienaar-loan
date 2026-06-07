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
        Schema::table('loans', function (Blueprint $table) {
            $table->tinyInteger('lifecycle_stage')->default(1)->after('status')
                ->comment('1=active, 2=redemption_offered, 3=redemption_accepted, 4=settled, 5=closed, 6=security_released');
            $table->tinyInteger('arrears_state')->default(1)->after('lifecycle_stage')
                ->comment('1=active, 2=arrears, 3=defaulted, 4=legal_collections');
            $table->unsignedBigInteger('active_quote_id')->nullable()->after('arrears_state');
            $table->timestamp('quote_accepted_at')->nullable()->after('active_quote_id');
            $table->unsignedBigInteger('quote_accepted_by')->nullable()->after('quote_accepted_at');
            $table->timestamp('settled_at')->nullable()->after('quote_accepted_by');
            $table->unsignedBigInteger('settled_by')->nullable()->after('settled_at');
            $table->timestamp('closed_at')->nullable()->after('settled_by');
            $table->unsignedBigInteger('closed_by')->nullable()->after('closed_at');
            $table->timestamp('security_released_at')->nullable()->after('closed_by');
            $table->unsignedBigInteger('security_released_by')->nullable()->after('security_released_at');
            $table->text('security_release_notes')->nullable()->after('security_released_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            //
        });
    }
};
