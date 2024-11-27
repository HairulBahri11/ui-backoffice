<?php

namespace App\Http\Controllers;

use App\Models\Advertises;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdvertisesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Advertises::all();
        return view('advertise.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Add Advertise';
        $data = (object)[
            'id' => 0,
            'title' => '',
            'banner' => '',
            'description' => '',
            'type' => 'create',
        ];
        return view('advertise.form', compact('data', 'title'));
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
            'title' => 'required',
            'description' => 'required',
            'banner' => 'required',
        ]);
        $fileType = $request->file('banner')->extension();
        $name = Str::random(8) . '.' . $fileType;

        $input['title'] = $request['title'];
        $input['description'] = $request['description'];
        $input['banner'] = Storage::putFileAs('adds', $request->file('banner'), $name);;

        try {
            Advertises::create($input);
            return redirect('/advertise')->with('status', 'Berhasil menambah data');
        } catch (\Throwable $th) {
            return $th;
            return back('/advertise/create')->with('status', 'Ggagal menambah data');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Advertises  $advertises
     * @return \Illuminate\Http\Response
     */
    public function show(Advertises $advertises)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Advertises  $advertises
     * @return \Illuminate\Http\Response
     */
    public function edit(Advertises $advertise)
    {
        $item = Advertises::where('id', $advertise->id)->first();
        $title = 'Edit Advertise';
        $data = (object)[
            'id' => $advertise->id,
            'title' => $item->title,
            'banner' => $item->banner,
            'description' => $item->description,
            'type' => 'update',
        ];
        return view('advertise.form', compact('data', 'title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Advertises  $advertises
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Advertises $advertise)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);
        $input['title'] = $request['title'];
        $input['description'] = $request['description'];
        if ($request->banner) {
            $fileType = $request->file('banner')->extension();
            $name = Str::random(8) . '.' . $fileType;
            $input['banner'] = Storage::putFileAs('adds', $request->file('banner'), $name);
        }

        try {
            Advertises::where('id', $advertise->id)->update($input);
            return redirect('/advertise')->with('status', 'Berhasil mengubah data');
        } catch (\Throwable $th) {
            return $th;
            return back('/advertise/create')->with('status', 'Ggagal mengubah data');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Advertises  $advertises
     * @return \Illuminate\Http\Response
     */
    public function destroy(Advertises $advertise)
    {

        try {
            Advertises::where('id', $advertise->id)->delete();
            return redirect('/advertise')->with('status', 'Berhasil menghapus data');
        } catch (\Throwable $th) {
            return $th;
            return redirect('/advertise')->with('error', 'Gagal menghapus data');
        }
    }
}
