<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherNotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $data = \App\Models\TeacherReminder::all();
        // return view('teacher-notes.index', compact('data'));

        if(auth()->guard('staff')->user()->id == 7 || auth()->guard('staff')->user()->id == 10){
            $data = \App\Models\TeacherReminder::with(['teacher', 'staff'])->where('type_announce', 'note')->get();
        } else {
            $data = \App\Models\TeacherReminder::with(['teacher', 'staff'])
                ->where('staff_id', auth()->guard('staff')->user()->id)
                ->where('type_announce', 'note')
                ->get();
        }
        return view('teacher-notes.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Add Teacher Notes';
        $teacher = Teacher::where('status', 'active')->get();
        $data = (object)[
            'id' => 0,
            'teacher_id' => '',
            'staff_id' => '',
            'description' => '',
            'status' => '',
            'category' => '',
            'type' => 'create',
        ];
        return view('teacher-notes.form', compact('data', 'title', 'teacher'));
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
            'teacher_id' => 'required',
            'description' => 'required|string',
            'category' => 'required|string',
        ]);

        // dd($request->all());

        $reminder = new \App\Models\TeacherReminder();
        $reminder->teacher_id = $request->teacher_id;
        $reminder->staff_id = auth()->guard('staff')->user()->id; // Assuming staff is logged in
        $reminder->description = $request->description;
        $reminder->status = 'pending'; // Default status
        $reminder->category = $request->category;
        $reminder->created_at = now();
        $reminder->type_announce = 'note';
        $reminder->save();

        return redirect()->route('teacher-notes.index')->with('success', 'Teacher Notes created successfully.');
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
        $title = 'Edit Teacher Notes';
        $teacher = Teacher::where('status', 'active')->get();
        $data = \App\Models\TeacherReminder::findOrFail($id);
        $data->type = 'edit';
        return view('teacher-notes.form', compact('data', 'title', 'teacher'));
        
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
        $request->validate([
            'teacher_id' => 'required',
            'description' => 'required|string',
            'category' => 'required|string',
            // 'status' => 'required|string',
        ]);

        $reminder = \App\Models\TeacherReminder::findOrFail($id);
        $reminder->teacher_id = $request->teacher_id;
        $reminder->description = $request->description;
        // $reminder->status = $request->status; // Update status
        $reminder->category = $request->category;
        $reminder->save();

        return redirect()->route('teacher-notes.index')->with('success', 'Teacher Notes updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $reminder = \App\Models\TeacherReminder::findOrFail($id);
        $reminder->delete();

        return redirect()->route('teacher-notes.index')->with('success', 'Teacher Notes deleted successfully.');
    }

    public function updateStatus($id, Request $request)
{
    $reminder = \App\Models\TeacherReminder::findOrFail($id);
    
    // Safety check: Ensure the request is trying to set the status to 'Completed'
    if ($request->input('status') !== 'Completed') {
        return back()->with('error', 'Invalid status update attempt.');
    }

    $reminder->status = $request->input('status'); // Sets status to 'Completed'
    $reminder->save();

    // Redirect back to the previous page with a success message
    return back()->with('status', 'Notes successfully marked as completed!');
}
}
