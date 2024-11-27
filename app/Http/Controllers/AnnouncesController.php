<?php

namespace App\Http\Controllers;

use App\Models\Announces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\File;

class AnnouncesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Announces::all();
        return view('announcement.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Add Announcement';
        $data = (object)[
            'id' => 0,
            'banner' => '',
            'description' => '',
            'announce_for' => 'Staff',
            'type' => 'create',
        ];
        return view('announcement.form', compact('data', 'title'));
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
            'description' => 'required',
            'banner' => 'required',
            'announce_for'=> 'required'
        ]);
        $fileType = $request->file('banner')->extension();
        $name = Str::random(8) . '.' . $fileType;

        $input['description'] = $request['description'];
        $input['announce_for'] = $request['announce_for'];
        
        //$input['banner'] = Storage::putFileAs('announce', $request->file('banner'), $name);;

        $input['banner'] = $request->file('banner')->storeAs('announce', $name); 


        try {
            Announces::create($input);
            return redirect('/announces')->with('status', 'Berhasil menambah data');
        } catch (\Throwable $th) {
            return $th;
            return back('/announces/create')->with('status', 'Ggagal menambah data');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Announces  $announces
     * @return \Illuminate\Http\Response
     */
    public function show(Announces $announces)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Announces  $announces
     * @return \Illuminate\Http\Response
     */
    public function edit(Announces $announce)
    {
        $item = Announces::where('id', $announce->id)->first();
        $title = 'Edit Announcement';
        $data = (object)[
            'id' => $announce->id,
            'title' => $item->title,
            'banner' => $item->banner,
            'description' => $item->description,
            'announce_for' => $item->announce_for,
            'type' => 'update',
        ];
        return view('announcement.form', compact('data', 'title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Announces  $announces
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Announces $announce)
    {
        $request->validate([
            'description' => 'required',
        ]);
        $input['description'] = $request['description'];
        $input['announce_for'] = $request['announce_for'];
        if ($request->banner) {
            $fileType = $request->file('banner')->extension();
            $name = Str::random(8) . '.' . $fileType;
            //$input['banner'] = Storage::putFileAs('announce', $request->file('banner'), $name);
            
            $input['banner'] = $request->file('banner')->storeAs('announce', $name); 
        }

        try {
            Announces::where('id', $announce->id)->update($input);
            return redirect('/announces')->with('status', 'Berhasil mengubah data');
        } catch (\Throwable $th) {
            return $th;
            return back('/announces/create')->with('status', 'Ggagal mengubah data');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Announces  $announces
     * @return \Illuminate\Http\Response
     */
    public function destroy(Announces $announce)
    {
        try {
            Announces::where('id', $announce->id)->delete();
            return redirect('/announces')->with('status', 'Berhasil menghapus data');
        } catch (\Throwable $th) {
            return $th;
            return redirect('/announces')->with('error', 'Gagal menghapus data');
        }
    }
}
