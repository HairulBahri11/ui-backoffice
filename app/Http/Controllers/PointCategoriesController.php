<?php

namespace App\Http\Controllers;

use App\Models\PointCategories;
use Illuminate\Http\Request;

class PointCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = PointCategories::all();
        return view('pointCategories.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Add Point Categories';
        $data = (object)[
            'id' => 0,
            'name' => '',
            'point' => '',
            'type' => 'create',
        ];
        return view('pointCategories.form', compact('data', 'title'));
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
            'point' => 'required|numeric',
        ]);
       

        $input['name'] = $request['name'];
        $input['point'] = $request['point'];

        try {
            PointCategories::create($input);
            return redirect('/pointCategories')->with('status', 'Berhasil menambah data');
        } catch (\Throwable $th) {
            return $th;
            return back('/pointCategories/create')->with('status', 'Ggagal menambah data');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PointCategories  $pointCategory
     * @return \Illuminate\Http\Response
     */
    public function show(PointCategories $pointCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PointCategories  $pointCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(PointCategories $pointCategory)
    {
        $title = 'Edit Point Category';
        $data = (object)[
            'id' => $pointCategory->id,
            'name' =>  $pointCategory->name,
            'point' =>  $pointCategory->point,
            'type' => 'edit',
        ];
        return view('pointCategories.form', compact('data', 'title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PointCategories  $pointCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PointCategories $pointCategory)
    {
        $request->validate([
            'name' => 'required',
            'point' => 'required|numeric',
        ]);
        $input['name'] = $request['name'];
        $input['point'] = $request['point'];
       

        try {
            PointCategories::where('id', $pointCategory->id)->update($input);
            return redirect('/pointCategories')->with('status', 'Berhasil mengubah data');
        } catch (\Throwable $th) {
            return $th;
            return back('/pointCategories/create')->with('status', 'Ggagal mengubah data');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PointCategories  $pointCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(PointCategories $pointCategory)
    {
        try {
            PointCategories::where('id', $pointCategory->id)->delete();
            return redirect('/pointCategories')->with('status', 'Berhasil menghapus data');
        } catch (\Throwable $th) {
            return $th;
            return redirect('/pointCategories')->with('error', 'Gagal menghapus data');
        }
    }
}
