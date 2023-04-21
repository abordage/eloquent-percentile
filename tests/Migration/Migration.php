<?php

declare(strict_types=1);

namespace Abordage\EloquentPercentile\Tests\Migration;

use Illuminate\Database\Migrations\Migration as BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Migration extends BaseMigration
{
    public function up(): void
    {
        Schema::dropIfExists('response_logs');
        Schema::dropIfExists('pages');

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
        });

        Schema::create('response_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained();
            $table->unsignedInteger('response_time')->nullable();
            $table->boolean('is_active')->default(true);
        });

        DB::table('pages')->insert([
            ['name' => 'page-1'],
        ]);
        DB::table('response_logs')->insert([
            ['page_id' => 1, 'response_time' => null, 'is_active' => true],
            ['page_id' => 1, 'response_time' => 2, 'is_active' => true],
            ['page_id' => 1, 'response_time' => 3, 'is_active' => true],
            ['page_id' => 1, 'response_time' => 7, 'is_active' => true],
            ['page_id' => 1, 'response_time' => 17, 'is_active' => true],
            ['page_id' => 1, 'response_time' => 45, 'is_active' => true],
            ['page_id' => 1, 'response_time' => 200, 'is_active' => true],
            ['page_id' => 1, 'response_time' => 203, 'is_active' => true],
            ['page_id' => 1, 'response_time' => 270, 'is_active' => true],
            ['page_id' => 1, 'response_time' => 360, 'is_active' => true],
            ['page_id' => 1, 'response_time' => 590, 'is_active' => true],
            ['page_id' => 1, 'response_time' => 605, 'is_active' => true],
            ['page_id' => 1, 'response_time' => null, 'is_active' => false],
            ['page_id' => 1, 'response_time' => 807, 'is_active' => true],
            ['page_id' => 1, 'response_time' => 976, 'is_active' => true],
            ['page_id' => 1, 'response_time' => 1001, 'is_active' => true],
            ['page_id' => 1, 'response_time' => 45677, 'is_active' => true],
            ['page_id' => 1, 'response_time' => 85677, 'is_active' => false],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('response_logs');
        Schema::dropIfExists('pages');
    }
}
