<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            if (! Schema::hasColumn('vendors', 'bank_name')) {
                $table->string('bank_name', 80)->nullable()->after('phone');
            }
            if (! Schema::hasColumn('vendors', 'bank_account_number')) {
                $table->string('bank_account_number', 60)->nullable()->after('bank_name');
            }
            if (! Schema::hasColumn('vendors', 'bank_account_holder')) {
                $table->string('bank_account_holder', 120)->nullable()->after('bank_account_number');
            }
        });

        Schema::table('members', function (Blueprint $table) {
            if (! Schema::hasColumn('members', 'bank_name')) {
                $table->string('bank_name', 80)->nullable()->after('daily_fee');
            }
            if (! Schema::hasColumn('members', 'bank_account_number')) {
                $table->string('bank_account_number', 60)->nullable()->after('bank_name');
            }
            if (! Schema::hasColumn('members', 'bank_account_holder')) {
                $table->string('bank_account_holder', 120)->nullable()->after('bank_account_number');
            }
        });

        Schema::table('schedules', function (Blueprint $table) {
            if (! Schema::hasColumn('schedules', 'payout_proof_path')) {
                $table->string('payout_proof_path')->nullable()->after('payment_notes');
            }
        });

        Schema::table('event_members', function (Blueprint $table) {
            if (! Schema::hasColumn('event_members', 'payout_proof_path')) {
                $table->string('payout_proof_path')->nullable()->after('payment_status');
            }
            if (! Schema::hasColumn('event_members', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payout_proof_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('event_members', function (Blueprint $table) {
            foreach (['paid_at', 'payout_proof_path'] as $col) {
                if (Schema::hasColumn('event_members', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('schedules', function (Blueprint $table) {
            if (Schema::hasColumn('schedules', 'payout_proof_path')) {
                $table->dropColumn('payout_proof_path');
            }
        });

        Schema::table('members', function (Blueprint $table) {
            foreach (['bank_account_holder', 'bank_account_number', 'bank_name'] as $col) {
                if (Schema::hasColumn('members', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('vendors', function (Blueprint $table) {
            foreach (['bank_account_holder', 'bank_account_number', 'bank_name'] as $col) {
                if (Schema::hasColumn('vendors', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

