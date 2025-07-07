<?php

namespace App\Providers;

use App\Models\Students;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class BirthdayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        View::composer('template.navbar',  function ($view) {
            $teacherId = Auth::guard('teacher')->id();

            $query = Students::where('status', 'ACTIVE')
                ->whereNotNull('course_time')
                ->whereNotNull('priceid')
                ->with(['class', 'teacher'])
                ->join('day as day_one', 'day_one.id', '=', 'student.day1')
                ->join('day as day_two', 'day_two.id', '=', 'student.day2')
                ->select('student.*', 'day_one.day as day1_name', 'day_two.day as day2_name')
                ->where('student.status', 'ACTIVE')
                ->orderBy('student.name', 'asc');

            if ($teacherId) {
                $query->where('id_teacher', $teacherId);
            }

            $student_list_active = $query->get();

            // --- Logika Baru untuk Filtering Notifikasi ---
            $student_birthday_notification = [];

            $todayMonthDay = now()->format('m-d'); // Format bulan-tanggal hari ini
            $todayDayName = now()->format('l'); // Nama hari ini (Monday, Tuesday, dll.)
            $currentYear = now()->format('Y');

            // Ambil daftar student_id yang sudah memiliki point_category_id = 7 di tahun ini
            // Ini akan berisi student_id dari student yang sudah menerima poin ulang tahun tahun ini
            $students_with_points_this_year = DB::table('attendance_detail_points')
                ->join('attendance_details', 'attendance_details.id', '=', 'attendance_detail_points.attendance_detail_id')
                ->where('attendance_detail_points.point_category_id', 7)
                ->whereYear('attendance_detail_points.created_at', $currentYear)
                ->pluck('attendance_details.student_id')
                ->toArray();

            foreach ($student_list_active as $item) {
                $birthdayString = trim($item->birthday);

                try {
                    if (preg_match('/^\d{4} [A-Za-z]+ \d{1,2}$/', $birthdayString)) {
                        $birthdayDate = Carbon::createFromFormat('Y F d', $birthdayString);
                    } elseif (preg_match('/^[A-Za-z]+ \d{1,2}$/', $birthdayString)) {
                        $birthdayDate = Carbon::createFromFormat('F d', $birthdayString)->year(now()->year);
                    } else {
                        continue; // Lewati jika format salah
                    }
                } catch (\Exception $e) {
                    \Log::error("Error parsing birthday for student " . $item->id . ": " . $birthdayString . " - " . $e->getMessage());
                    continue;
                }

                // Cek apakah siswa ini sudah menerima poin ulang tahun tahun ini
                if (in_array($item->id, $students_with_points_this_year)) {
                    continue; // Jika sudah, lewati siswa ini
                }

                // Ambil bulan dan tanggal dari ulang tahun siswa
                $studentMonthDay = $birthdayDate->format('m-d');

                // Hitung batas akhir tampilan (7 hari setelah ulang tahun)
                // Pastikan tahun yang digunakan untuk perhitungan +7 hari adalah tahun yang sama
                // agar tidak melompat ke tahun depan jika ulang tahun di akhir Desember
                $birthday_plus_7_days_date = clone $birthdayDate; // Buat salinan agar tidak mengubah $birthdayDate asli
                $birthday_plus_7_days_date->addDays(7);
                $birthday_plus_7_days_month_day = $birthday_plus_7_days_date->format('m-d');

                // Cek apakah hari ini dalam rentang ulang tahun hingga 7 hari setelahnya
                // Perlu penanganan khusus untuk ulang tahun di akhir tahun yang rentangnya bisa melintasi tahun
                $is_within_7_days = false;
                if ($studentMonthDay <= $birthday_plus_7_days_month_day) {
                    // Kasus normal: rentang ulang tahun di bulan/tahun yang sama
                    $is_within_7_days = ($todayMonthDay >= $studentMonthDay && $todayMonthDay <= $birthday_plus_7_days_month_day);
                } else {
                    // Kasus ulang tahun di akhir Desember, rentangnya melintasi tahun
                    // Contoh: Ulang tahun 28 Des, rentang sampai 4 Jan tahun berikutnya
                    $is_within_7_days = ($todayMonthDay >= $studentMonthDay || $todayMonthDay <= $birthday_plus_7_days_month_day);
                }


                // Cek apakah hari ini cocok dengan day1 atau day2 siswa
                $day1Name = $item->day1_name ?? null;
                $day2Name = $item->day2_name ?? null;
                $is_matching_day = in_array($todayDayName, [$day1Name, $day2Name]);

                // Ambil data kelas dan guru
                $className = $item->class->program ?? null;
                $teacherName = $item->teacher->name ?? null;

                // Hitung umur siswa
                $age = $birthdayDate->diff(now())->y;

                // Cek apakah hari ini adalah ulang tahun siswa
                $isTodayBirthday = ($todayMonthDay == $studentMonthDay);

                // Hanya tambahkan siswa jika semua kondisi terpenuhi
                if ($is_within_7_days && $is_matching_day) {
                    $student_birthday_notification[] = [
                        'id' => $item->id,
                        'name' => $item->name,
                        'birthday' => $birthdayDate->format('Y-m-d'), // Format "YYYY-MM-DD"
                        'class' => $className,
                        'teacher' => $teacherName,
                        'age' => $age,
                        'is_today_birthday' => $isTodayBirthday,
                        'day1' => $day1Name,
                        'day2' => $day2Name,
                        'course_time' => $item->course_time

                    ];
                }
            }

            // Pass data ke view
            $view->with('student_birthday_notification', $student_birthday_notification);
        });

        View::composer('dashboard.index',  function ($view) {
            $teacherId = Auth::guard('teacher')->id();

            $query = Students::where('status', 'ACTIVE')
                ->whereNotNull('course_time')
                ->whereNotNull('priceid')
                ->with(['class', 'teacher'])
                ->join('day as day_one', 'day_one.id', '=', 'student.day1')
                ->join('day as day_two', 'day_two.id', '=', 'student.day2')
                ->select('student.*', 'day_one.day as day1_name', 'day_two.day as day2_name')
                ->where('student.status', 'ACTIVE')
                ->orderBy('student.name', 'asc');

            if ($teacherId) {
                $query->where('id_teacher', $teacherId);
            }

            $student_list_active = $query->get();

            // --- Logika Baru untuk Filtering Notifikasi ---
            $student_birthday_notification = [];

            $todayMonthDay = now()->format('m-d'); // Format bulan-tanggal hari ini
            $todayDayName = now()->format('l'); // Nama hari ini (Monday, Tuesday, dll.)
            $currentYear = now()->format('Y');

            // Ambil daftar student_id yang sudah memiliki point_category_id = 7 di tahun ini
            // Ini akan berisi student_id dari student yang sudah menerima poin ulang tahun tahun ini
            $students_with_points_this_year = DB::table('attendance_detail_points')
                ->join('attendance_details', 'attendance_details.id', '=', 'attendance_detail_points.attendance_detail_id')
                ->where('attendance_detail_points.point_category_id', 7)
                ->whereYear('attendance_detail_points.created_at', $currentYear)
                ->pluck('attendance_details.student_id')
                ->toArray();

            foreach ($student_list_active as $item) {
                $birthdayString = trim($item->birthday);

                try {
                    if (preg_match('/^\d{4} [A-Za-z]+ \d{1,2}$/', $birthdayString)) {
                        $birthdayDate = Carbon::createFromFormat('Y F d', $birthdayString);
                    } elseif (preg_match('/^[A-Za-z]+ \d{1,2}$/', $birthdayString)) {
                        $birthdayDate = Carbon::createFromFormat('F d', $birthdayString)->year(now()->year);
                    } else {
                        continue; // Lewati jika format salah
                    }
                } catch (\Exception $e) {
                    \Log::error("Error parsing birthday for student " . $item->id . ": " . $birthdayString . " - " . $e->getMessage());
                    continue;
                }

                // Cek apakah siswa ini sudah menerima poin ulang tahun tahun ini
                if (in_array($item->id, $students_with_points_this_year)) {
                    continue; // Jika sudah, lewati siswa ini
                }

                // Ambil bulan dan tanggal dari ulang tahun siswa
                $studentMonthDay = $birthdayDate->format('m-d');

                // Hitung batas akhir tampilan (7 hari setelah ulang tahun)
                // Pastikan tahun yang digunakan untuk perhitungan +7 hari adalah tahun yang sama
                // agar tidak melompat ke tahun depan jika ulang tahun di akhir Desember
                $birthday_plus_7_days_date = clone $birthdayDate; // Buat salinan agar tidak mengubah $birthdayDate asli
                $birthday_plus_7_days_date->addDays(7);
                $birthday_plus_7_days_month_day = $birthday_plus_7_days_date->format('m-d');

                // Cek apakah hari ini dalam rentang ulang tahun hingga 7 hari setelahnya
                // Perlu penanganan khusus untuk ulang tahun di akhir tahun yang rentangnya bisa melintasi tahun
                $is_within_7_days = false;
                if ($studentMonthDay <= $birthday_plus_7_days_month_day) {
                    // Kasus normal: rentang ulang tahun di bulan/tahun yang sama
                    $is_within_7_days = ($todayMonthDay >= $studentMonthDay && $todayMonthDay <= $birthday_plus_7_days_month_day);
                } else {
                    // Kasus ulang tahun di akhir Desember, rentangnya melintasi tahun
                    // Contoh: Ulang tahun 28 Des, rentang sampai 4 Jan tahun berikutnya
                    $is_within_7_days = ($todayMonthDay >= $studentMonthDay || $todayMonthDay <= $birthday_plus_7_days_month_day);
                }


                // Cek apakah hari ini cocok dengan day1 atau day2 siswa
                $day1Name = $item->day1_name ?? null;
                $day2Name = $item->day2_name ?? null;
                $is_matching_day = in_array($todayDayName, [$day1Name, $day2Name]);

                // Ambil data kelas dan guru
                $className = $item->class->program ?? null;
                $teacherName = $item->teacher->name ?? null;

                // Hitung umur siswa
                $age = $birthdayDate->diff(now())->y;

                // Cek apakah hari ini adalah ulang tahun siswa
                $isTodayBirthday = ($todayMonthDay == $studentMonthDay);

                // Hanya tambahkan siswa jika semua kondisi terpenuhi
                if ($is_within_7_days && $is_matching_day) {
                    $student_birthday_notification[] = [
                        'id' => $item->id,
                        'name' => $item->name,
                        'birthday' => $birthdayDate->format('Y-m-d'), // Format "YYYY-MM-DD"
                        'class' => $className,
                        'teacher' => $teacherName,
                        'age' => $age,
                        'is_today_birthday' => $isTodayBirthday,
                        'day1' => $day1Name,
                        'day2' => $day2Name,
                        'course_time' => $item->course_time

                    ];
                }
            }

            // Pass data ke view
            $view->with('student_birthday_notification', $student_birthday_notification);
        });
    }
}
