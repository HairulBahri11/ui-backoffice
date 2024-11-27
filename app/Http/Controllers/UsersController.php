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


        return view('dashboard.index', compact('data', 'arr', 'parent'));
    }

    public function viewLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        // return $request;
        // return bcrypt('teacher');
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);
        // return Auth::guard('staff')->attempt(['username' => $request->email, 'password' => $request->password]);
        try {
            if ($request->type == 'teacher') {
                if (Auth::guard('teacher')->attempt(['username' => $request->email, 'password' => $request->password])) {
                    // if successful, then redirect to their intended location
                    return redirect()->intended('/dashboard');
                } else {
                    return redirect()->intended('/');
                }
            } else {
                if (Auth::guard('staff')->attempt(['username' => $request->email, 'password' => $request->password])) {
                    // if successful, then redirect to their intended location
                    return redirect('/dashboard')->with('message', 'Need to follow up');
                } else {
                    return redirect()->intended('/');
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $th;
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
            return redirect('/user')->with('error', 'Success update profile');
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
    
    public function tes()
    {
        return 'Hey';
    }
    
    
}
