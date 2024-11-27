<?php

namespace App\Http\Controllers;

use App\Models\TestItems;
use App\Models\Tests;
use Illuminate\Http\Request;

class TestItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Tests::all();
        return view('testItems.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Add Tests Master';
        $data = (object)[
            'id' => 0,
            'name' => '',
            'type' => 'create',
        ];
        return view('testItems.form', compact('data', 'title'));
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
            'name' => 'required',
        ]);
       

        $input['name'] = $request['name'];

        try {
            Tests::create($input);
            return redirect('/tests')->with('status', 'Berhasil menambah data');
        } catch (\Throwable $th) {
            return $th;
            return back('/tests/create')->with('status', 'Ggagal menambah data');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TestItems  $test
     * @return \Illuminate\Http\Response
     */
    public function show(TestItems $test)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TestItems  $test
     * @return \Illuminate\Http\Response
     */
    public function edit(TestItems $test)
    {
        $title = 'Edit Tests Master';
        $data = (object)[
            'id' => $test->id,
            'name' =>  $test->name,
            'type' => 'edit',
        ];
        return view('testItems.form', compact('data', 'title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TestItems  $test
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TestItems $test)
    {
        
        $request->validate([
            'name' => 'required',
            
        ]);
        $input['name'] = $request['name'];
       

        try {
            Tests::where('id', $test->id)->update($input);
            return redirect('/tests')->with('status', 'Berhasil mengubah data');
        } catch (\Throwable $th) {
            return $th;
            return back('/tests/create')->with('status', 'Ggagal mengubah data');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TestItems  $test
     * @return \Illuminate\Http\Response
     */
    public function destroy(TestItems $test)
    {
        try {
            Tests::where('id', $test->id)->delete();
            return redirect('/tests')->with('status', 'Berhasil menghapus data');
        } catch (\Throwable $th) {
            return $th;
            return redirect('/tests')->with('error', 'Gagal menghapus data');
        }
    }
}
