<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{

	/**
	 * Run the migrations.
	 */
	public function up()
	: void{
		Schema::create('activity_day', function (Blueprint $table){
			$table->id();
			$table->integer('user_id');
			$table->timestamp('date');
			$table->unsignedInteger('count')
			      ->default(0); // Using for heat counting, most activity of a day
			$table->timestamps();
			$table->unique(['user_id', 'date']);
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down()
	: void{
		Schema::dropIfExists('activity_day');
	}
};
