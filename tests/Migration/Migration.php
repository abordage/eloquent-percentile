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
        });

        DB::table('pages')->insert([
            ['name' => 'page-1'],
        ]);
        DB::table('response_logs')->insert([
            ['page_id' => 1, 'response_time' => null],
            ['page_id' => 1, 'response_time' => 2],
            ['page_id' => 1, 'response_time' => 3],
            ['page_id' => 1, 'response_time' => 7],
            ['page_id' => 1, 'response_time' => 17],
            ['page_id' => 1, 'response_time' => 45],
            ['page_id' => 1, 'response_time' => 200],
            ['page_id' => 1, 'response_time' => 203],
            ['page_id' => 1, 'response_time' => 270],
            ['page_id' => 1, 'response_time' => 360],
            ['page_id' => 1, 'response_time' => 590],
            ['page_id' => 1, 'response_time' => 605],
            ['page_id' => 1, 'response_time' => null],
            ['page_id' => 1, 'response_time' => 807],
            ['page_id' => 1, 'response_time' => 976],
            ['page_id' => 1, 'response_time' => 1001],
            ['page_id' => 1, 'response_time' => 45677],
            ['page_id' => 1, 'response_time' => 85677],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('response_logs');
        Schema::dropIfExists('pages');
    }
}
