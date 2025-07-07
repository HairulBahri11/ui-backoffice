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
        // Define the logic for fetching birthday notifications in a single, reusable method
        $getBirthdayNotifications = function () {
            $teacherId = Auth::guard('teacher')->id();

            $query = Students::where('status', 'ACTIVE')
                ->whereNotNull('course_time')
                ->whereNotNull('priceid')
                ->with(['class', 'teacher'])
                ->join('day as day_one', 'day_one.id', '=', 'student.day1')
                ->join('day as day_two', 'day_two.id', '=', 'student.day2')
                ->select('student.*', 'day_one.day as day1_name', 'day_two.day as day2_name')
                ->orderBy('student.name', 'asc');

            if ($teacherId) {
                $query->where('id_teacher', $teacherId);
            }

            $studentListActive = $query->get();

            $studentBirthdayNotification = [];

            $todayMonthDay = now()->format('m-d');
            $todayDayName = now()->format('l');
            $currentYear = now()->format('Y');

            $studentsWithPointsThisYear = DB::table('attendance_detail_points')
                ->join('attendance_details', 'attendance_details.id', '=', 'attendance_detail_points.attendance_detail_id')
                ->where('attendance_detail_points.point_category_id', 7)
                ->whereYear('attendance_detail_points.created_at', $currentYear)
                ->pluck('attendance_details.student_id')
                ->toArray();

            foreach ($studentListActive as $item) {
                $birthdayString = trim($item->birthday);

                try {
                    if (preg_match('/^\d{4} [A-Za-z]+ \d{1,2}$/', $birthdayString)) {
                        $birthdayDate = Carbon::createFromFormat('Y F d', $birthdayString);
                    } elseif (preg_match('/^[A-Za-z]+ \d{1,2}$/', $birthdayString)) {
                        $birthdayDate = Carbon::createFromFormat('F d', $birthdayString)->year(now()->year);
                    } else {
                        continue; // Skip if format is incorrect
                    }
                } catch (\Exception $e) {
                    \Log::error("Error parsing birthday for student " . $item->id . ": " . $birthdayString . " - " . $e->getMessage());
                    continue;
                }

                // Check if the student has already received birthday points this year
                if (in_array($item->id, $studentsWithPointsThisYear)) {
                    continue; // If so, skip this student
                }

                $studentMonthDay = $birthdayDate->format('m-d');

                $birthdayPlus7DaysDate = clone $birthdayDate;
                $birthdayPlus7DaysDate->addDays(7);
                $birthdayPlus7DaysMonthDay = $birthdayPlus7DaysDate->format('m-d');

                $isWithin7Days = false;
                if ($studentMonthDay <= $birthdayPlus7DaysMonthDay) {
                    // Normal case: birthday range within the same month/year
                    $isWithin7Days = ($todayMonthDay >= $studentMonthDay && $todayMonthDay <= $birthdayPlus7DaysMonthDay);
                } else {
                    // Birthday at year-end, range crosses into the next year
                    $isWithin7Days = ($todayMonthDay >= $studentMonthDay || $todayMonthDay <= $birthdayPlus7DaysMonthDay);
                }

                $day1Name = $item->day1_name ?? null;
                $day2Name = $item->day2_name ?? null;
                $isMatchingDay = in_array($todayDayName, [$day1Name, $day2Name]);

                $className = $item->class->program ?? null;
                $teacherName = $item->teacher->name ?? null;

                $age = $birthdayDate->diff(now())->y;

                $isTodayBirthday = ($todayMonthDay == $studentMonthDay);

                if ($isWithin7Days && $isMatchingDay) {
                    $studentBirthdayNotification[] = [
                        'id' => $item->id,
                        'name' => $item->name,
                        'birthday' => $birthdayDate->format('Y-m-d'),
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
            return $studentBirthdayNotification;
        };

        // Use the reusable logic for 'template.navbar' view
        View::composer('template.navbar', function ($view) use ($getBirthdayNotifications) {
            $view->with('student_birthday_notification', $getBirthdayNotifications());
        });

        // Use the reusable logic for 'dashboard.index' view
        View::composer('dashboard.index', function ($view) use ($getBirthdayNotifications) {
            $view->with('student_birthday_notification', $getBirthdayNotifications());
        });

        View::composer('attendance.form', function ($view) use ($getBirthdayNotifications) {
            $view->with('student_birthday_notification', $getBirthdayNotifications());
        });
    }
}
