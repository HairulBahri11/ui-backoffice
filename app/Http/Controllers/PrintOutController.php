<?php

namespace App\Http\Controllers;

use App\Models\PrintOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PrintOutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (Auth::guard('teacher')->check()) {
            $teacherId = Auth::guard('teacher')->id();
            $printOut = PrintOut::with('teacher', 'price')->where('teacher_id', $teacherId)->orderBy('created_at', 'desc')->get();
        } else {
            $printOut = PrintOut::with('teacher', 'price',)->orderBy('created_at', 'desc')->get();
        }

        return view('print_out.index', compact('printOut'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $teacherId = Auth::guard('teacher')->id();

        if (!$teacherId) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        // Fetch unique schedules belonging strictly to the logged-in teacher
        $classes = DB::table('student')
            ->join('price', 'price.id', '=', 'student.priceid')
            ->join('day as day_one', 'day_one.id', '=', 'student.day1')
            ->join('day as day_two', 'day_two.id', '=', 'student.day2')
            ->where('student.status', 'ACTIVE')
            ->where('student.id_teacher', $teacherId)
            ->whereNotNull('student.course_time')
            ->where('student.course_time', '!=', '')
            ->whereNotNull('student.priceid')
            ->select(
                'student.priceid as class_id',
                'price.program as program_name',
                'student.course_time',
                'student.day1 as day1_id',
                'student.day2 as day2_id',
                'day_one.day as day1_name',
                'day_two.day as day2_name'
            )
            ->orderBy('class_id', 'asc')
            ->distinct()
            ->get();

        // dd($classes);

        return view('print_out.create', compact('classes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id'      => 'required',
            'course_time'   => 'required|string',
            'day1_id'       => 'required|integer',
            'day2_id'       => 'required|integer',
            'note'          => 'required|string',
            'document_file' => 'required|file|mimes:pdf,docx|max:5120', // Validate file types
        ]);

        try {
            $print = new PrintOut();
            $print->class_id    = (int)$request->class_id;
            $print->course_time = $request->course_time;
            $print->day1_id     = (int)$request->day1_id;
            $print->day2_id     = (int)$request->day2_id;
            $print->note        = $request->note;
            $print->teacher_id  = Auth::guard('teacher')->id();
            $print->created_at  = now();

            if ($request->hasFile('document_file')) {
                $file = $request->file('document_file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/print_files'), $filename);
                $print->file_link = 'uploads/print_files/' . $filename; // Saves down file access route
            }

            $print->save();

            return redirect('/print-out')->with('success', 'Print request created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to save data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $printOut = PrintOut::findOrFail($id);

            // Delete the associated file if it exists
            if ($printOut->file_link && file_exists(public_path($printOut->file_link))) {
                unlink(public_path($printOut->file_link));
            }

            $printOut->delete();

            return redirect('/print-out')->with('success', 'Print request deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete data: ' . $e->getMessage());
        }
    }
}
