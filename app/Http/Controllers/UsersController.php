<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Price;
use App\Models\Staff;
use App\Models\Parents;
use App\Models\Teacher;
use App\Models\Students;
use App\Models\Announces;
use App\Models\Attendance;
use Facade\FlareClient\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $student = Students::where('status', 'ACTIVE')->where('course_time', '!=', null)->where('priceid', '!=', null)->count();

        // dd($student);
        $parent = Parents::count();
        $teacher = Teacher::count();

        $arr = [];
        if (Auth::guard('teacher')->check()) {

            $today = Carbon::today()->toDateString();

            $test = DB::table('order_reviews as or2')
                ->select('or2.test_id', 'a.price_id', 'ad.student_id', 'or2.id_teacher', 'or2.class', 'or2.review_test', 's.name', 'p.program', 'p.id', 'day1.day as day1', 'day2.day as day2', 'a.course_time', 'or2.due_date', 'or2.type')
                ->join('attendances as a', 'a.id', '=', 'or2.id_attendance')
                ->join('attendance_details as ad', 'ad.attendance_id', '=', 'a.id')
                ->join('student as s', 's.id', '=', 'ad.student_id')
                ->join('price as p', 'p.id', '=', 'a.price_id')
                ->join('day as day1', 'day1.id', '=', 'a.day1')
                ->join('day as day2', 'day2.id', '=', 'a.day2')
                ->where('or2.id_teacher', Auth::guard('teacher')->id())
                ->whereRaw('DATE_ADD(or2.due_date, INTERVAL 2 WEEK) <= ?', [$today])
                ->where('or2.is_done', '0')
                ->where('or2.type', 'test')
                ->where('s.status', 'ACTIVE')
                ->orderBy('p.id', 'ASC')
                // ->groupBy('ad.student_id', 'a.price_id', 'or2.test_id', 'or2.id_teacher')
                ->get();

            // dd($test);


            foreach ($test as $item) {
                $test1 = DB::table('student_scores as ss')
                    ->where('student_id', $item->student_id)
                    ->where('price_id', $item->price_id)
                    ->where('test_id', $item->test_id)

                    ->whereNotNull('ss.id') // or any other column that you want to check for null
                    ->first();
                if (!$test1) {
                    array_push($arr, $item);
                }
            }
            // dd($arr);

            $announces = Announces::where('announce_for', 'Teacher')->orderBy('id', 'DESC')->first();
        } else
            $announces = Announces::where('announce_for', 'Staff')->orderBy('id', 'DESC')->first();


        $data = (object)([
            'student' => $student,
            'parent' => $parent,
            'teacher' => $teacher,
            'announces' => $announces,
        ]);


        // Cek student_birthday
        $teacherId = Auth::guard('teacher')->id(); // Ambil ID guru jika login

        // Ambil siswa aktif
        $query = Students::where('status', 'ACTIVE')
            ->whereNotNull('course_time')
            ->whereNotNull('priceid')
            ->with(['class', 'teacher'])
            ->join('day as day_one', 'day_one.id', '=', 'student.day1')
            ->join('day as day_two', 'day_two.id', '=', 'student.day2')
            ->select('student.*', 'day_one.day as day1_name', 'day_two.day as day2_name')
            ->orderBy('student.name', 'asc');


        // Jika login sebagai teacher, filter berdasarkan id_teacher
        if ($teacherId) {
            $query->where('id_teacher', $teacherId);
        }

        $student_list_active = $query->get();

        $student_birthday = [];

        foreach ($student_list_active as $item) {
            $birthdayString = trim($item->birthday); // Hapus spasi ekstra

            // Pastikan format tanggal valid sebelum diproses
            if (preg_match('/^\d{4} [A-Za-z]+ \d{1,2}$/', $birthdayString)) {
                // Format "2019 November 24" → "2019-11-24"
                $date = Carbon::createFromFormat('Y F d', $birthdayString);
            } elseif (preg_match('/^[A-Za-z]+ \d{1,2}$/', $birthdayString)) {
                // Format "November 24" → "2025-11-24" (asumsi tahun ini)
                $birthdayString .= ' ' . now()->year;
                $date = Carbon::createFromFormat('F d Y', $birthdayString);
            } else {
                continue; // Lewati jika format salah
            }

            // Hitung umur hanya jika tahun ada
            $age = $date->year != now()->year ? now()->diffInYears($date) : null;

            // Cek apakah ulang tahun bulan ini
            $isThisMonthBirthday = ($date->format('m') == now()->format('m'));

            // Cek apakah ulang tahun hari ini
            $isTodayBirthday = ($date->format('m-d') == now()->format('m-d'));

            // Pastikan data `class` dan `teacher` tersedia sebelum mengaksesnya
            $className = $item->class->program ?? 'Unknown';
            $teacherName = $item->teacher->name ?? 'Unknown';
            $day1 = $item->day1_name ?? 'Unknown';
            $day2 = $item->day2_name ?? 'Unknown';

            // Tambahkan hanya siswa yang ulang tahun bulan ini
            if ($isThisMonthBirthday) {
                $student_birthday[] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'birthday' => $date->format('Y-m-d'), // Format "YYYY-MM-DD"
                    'class' => $className,
                    'teacher' => $teacherName,
                    'age' => $age,
                    'is_today_birthday' => $isTodayBirthday,
                    'day1' => $day1,
                    'day2' => $day2,
                    'course_time' => $item->course_time
                ];
            }
        }


        // dd($student_birthday);

        return view('dashboard.index', compact('data', 'arr', 'parent', 'student_birthday'));
    }

    public function viewLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        // Check if the email exists in staff or teacher table
        $staff = DB::table('staff')->where('username', $request->email)->first();
        $teacher = DB::table('teacher')->where('username', $request->email)->first();

        // If neither staff nor teacher exists, redirect back to login page
        if (!$staff && !$teacher) {
            return redirect()->intended('/')->withErrors(['email' => 'Invalid credentials']);
        }

        // If the account is inactive, redirect with message
        if ($teacher && $teacher->status === 'nonactive') {
            return redirect()->intended('/')->with('message', 'Your account is inactive');
        }

        // Attempt login based on user type (teacher or staff)
        try {
            if ($request->type === 'teacher' && $teacher) {
                if (Auth::guard('teacher')->attempt(['username' => $request->email, 'password' => $request->password])) {
                    return redirect()->intended('/dashboard');
                }
            } elseif ($request->type === 'staff' && $staff) {
                if (Auth::guard('staff')->attempt(['username' => $request->email, 'password' => $request->password])) {
                    return redirect()->intended('/dashboard')->with('message', 'Need to follow up');
                }
            }

            // If authentication failed, redirect back to login
            return redirect()->intended('/')->withErrors(['email' => 'Invalid credentials or incorrect password']);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return redirect()->intended('/')->withErrors(['error' => 'An error occurred during login.']);
        }
    }


    public function logout()
    {
        if (Auth::guard('teacher')->check()) {
            Auth::guard('teacher')->logout();
            return redirect('/');
        } else if (Auth::guard('staff')->check()) {
            Auth::guard('staff')->logout();
            return redirect('/');
        }
    }

    public function profile()
    {
        try {
            $data = [];
            if (Auth::guard('teacher')->check()) {
                $data = Teacher::where('id', Auth::guard('teacher')->user()->id)->first();
            } else if (Auth::guard('staff')->check()) {
                $data = Staff::where('id', Auth::guard('staff')->user()->id)->first();
            }

            // return $data;
            return view('user', compact('data'));
        } catch (\Throwable $th) {
            //throw $th;
        }
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $user)
    {
        // return $request;
        $this->validate($request, [
            'username' => 'required',
            'name' => 'required'
        ]);
        try {
            $input = ([
                'name' => $request->name,
                'username' => $request->username,
            ]);
            if ($request->password) {

                $input['password'] = Hash::make($request->password);
            }
            if (Auth::guard('teacher')->check()) {
                Teacher::where('id', $user)->update($input);
                return redirect('/user')->with('status', 'Success update profile');
            } else {
                Staff::where('id', $user)->update($input);
                return redirect('/user')->with('status', 'Success update profile');
            }
        } catch (\Throwable $th) {
            return $th;
        }
        return $request;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    public function print()
    {
        return view('report.print');
    }

    function parseBirthday($birthday)
    {
        // Coba parsing format "Y F d" (contoh: 2017 October 15)
        try {
            return Carbon::createFromFormat('Y F d', $birthday);
        } catch (\Exception $e) {
            // Jika gagal, coba parsing format "F Y" (contoh: Agustus 2025)
            return false;
        }
    }
}
