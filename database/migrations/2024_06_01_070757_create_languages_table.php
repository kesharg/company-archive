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
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("code")->unique();
            $table->tinyInteger("is_active")->default(1)->comment("1=Active,0=Inactive");
            $table->foreignId('created_by_id')->nullable()->constrained("users");
            $table->foreignId('updated_by_id')->nullable()->constrained("users");
            $table->foreignId('deleted_by_id')->nullable()->constrained("users");
            $table->datetimes();
            $table->softDeletes();
        });

        \Illuminate\Support\Facades\DB::table("languages")->insert([
            "name" => "English",
            "code" => "en",
        ]);

        \Illuminate\Support\Facades\DB::table("languages")->insert([
            "name" => "English UK",
            "code" => "enUK",
        ]);

        \Illuminate\Support\Facades\DB::table("languages")->insert([
            "name" => "Bangla",
            "code" => "bn",
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
