<?php

namespace App\Http\Controllers;

use App\Models\ParentStudents;
use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use DB;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Response;

class ParentStudentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Models\ParentStudents  $parentStudents
     * @return \Illuminate\Http\Response
     */
    public function show(ParentStudents $parentStudents)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ParentStudents  $parentStudents
     * @return \Illuminate\Http\Response
     */
    public function edit(ParentStudents $parentStudents)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ParentStudents  $parentStudents
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ParentStudents $parentStudents)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ParentStudents  $parentStudents
     * @return \Illuminate\Http\Response
     */
    public function destroy(ParentStudents $parentStudents)
    {
        //
    }

    public function student()
    {
        
        $generator = new \Picqer\Barcode\BarcodeGeneratorJPG();
        $student = Students::where('status', 'ACTIVE')->whereNull('barcode')->get();
        // dd($student);
        foreach ($student as $key => $value) {
            $getStudent = Students::find($value->id);
            $getStudent->barcode = $value->id . '.jpg';
            $getStudent->save();

            //File::put('storage/barcode/' . $value->id . '.jpg', $generator->getBarcode($value->id, $generator::TYPE_CODE_128));
            File::put(public_path('barcode/' . $value->id . '.jpg'), $generator->getBarcode($value->id, $generator::TYPE_CODE_128));
        }
        return redirect('/barcode-student')->with('status', 'Berhasil mengupdate barcode');
    }

    public function barcode()
    {
        $data = Students::where('status', 'ACTIVE')
                ->whereNotNull('priceid')
                ->get();

        return view('parents.barcode', compact('data'));
    }

    public function download($id)
    {
        //$filepath = 'storage/barcode/' . $id . '.jpg';
        $filepath = public_path('barcode/' . $id . '.jpg');
        return Response::download($filepath);
    }

    public function print($id)
    {
        // $student = Students::join('price', 'price.id', 'student.priceid')->find($id);
        $student = Students::find($id);

        return view('parents.print', compact('id', 'student'));
    }

    public function printAll()
    {
        $student = Students::leftJoin('price', 'price.id', 'student.priceid')->select('student.id as id', 'student.name as name', 'price.program as program')->where('status', 'ACTIVE')->limit(50)->get();
        // return $student;
        return view('parents.print-all', compact('student'));
    }
}
