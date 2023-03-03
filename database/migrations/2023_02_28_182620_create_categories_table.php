<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('color');
            $table->timestamps();
        });

        $data = [
            ['name' => 'Hrana i Piće', 'color' => '#b45954', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Kupovina', 'color' => '#109561', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Kućanstvo', 'color' => '#3a3dd6', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Prijevoz', 'color' => '#6f7f16', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Automobil', 'color' => '#760ecd', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Život i zabava', 'color' => '#03d9cb', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Ulaganja', 'color' => '#e735cc', 'created_at' => date('Y-m-d H:i:s')]
        ];

        DB::table('categories')->insert($data);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
