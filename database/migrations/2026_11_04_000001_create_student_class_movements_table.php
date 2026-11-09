<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentClassMovementsTable extends Migration
{
    /**
     * Riwayat perpindahan kelas student (Jump Class & Transfer Class).
     * Kolom from_* = kondisi kelas sebelum pindah, to_* = sesudah pindah.
     * Tipe kolom mengikuti tabel `student` (priceid, day1, day2, course_time, id_teacher).
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_class_movements', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id');
            $table->string('type', 20)->comment('jump_class / transfer_class / update_class');

            $table->integer('from_price_id')->nullable();
            $table->integer('from_day1')->nullable();
            $table->integer('from_day2')->nullable();
            $table->string('from_course_time', 10)->nullable();
            $table->integer('from_teacher_id')->nullable();

            $table->integer('to_price_id')->nullable();
            $table->integer('to_day1')->nullable();
            $table->integer('to_day2')->nullable();
            $table->string('to_course_time', 10)->nullable();
            $table->integer('to_teacher_id')->nullable();

            $table->string('user_name')->nullable()->comment('Staff yang memproses');
            $table->timestamps();

            $table->index(['student_id', 'created_at']);
        });
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_class_movements');
    }
}
