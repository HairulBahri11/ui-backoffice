<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Teacher;
use App\Models\Price;
use App\Models\Days;
use Illuminate\Support\Carbon;

class LessonPlan extends Model
{
    use HasFactory;

    protected $table = 'lesson_plan';

    protected $guarded = ['id'];

    // Senin awal "minggu perencanaan". Hari Minggu sudah dibuka untuk create dan dipakai
    // guru merencanakan minggu depan, jadi Minggu dihitung masuk minggu berikutnya
    // (bukan ekor minggu yang sedang berjalan seperti startOfWeek() Carbon).
    public static function planningWeekStart($date)
    {
        $date = Carbon::parse($date);

        return ($date->isSunday() ? $date->copy()->addDay() : $date->copy())->startOfWeek();
    }

    // Tanggal aktual kelas berlangsung (for_day 1-6 = Senin-Sabtu di minggu perencanaan data dibuat).
    // Data lama tanpa for_day: fallback ke tanggal dibuatnya data.
    public static function targetDate($createdAt, $forDay = null)
    {
        $createdDate = Carbon::parse($createdAt)->startOfDay();

        return $forDay
            ? self::planningWeekStart($createdDate)->addDays($forDay - 1)
            : $createdDate;
    }

    // Relasi ke Teacher
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id');
    }

    // Relasi ke Price (Class)
    public function price()
    {
        return $this->belongsTo(Price::class, 'class', 'id');
    }

    // Relasi ke day1
    public function day1()
    {
        return $this->belongsTo(Days::class, 'day1', 'id');
    }

    // Relasi ke day2
    public function day2()
    {
        return $this->belongsTo(Days::class, 'day2', 'id');
    }
}
