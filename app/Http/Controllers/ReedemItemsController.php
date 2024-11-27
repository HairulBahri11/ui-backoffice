<?php

namespace App\Http\Controllers;

use App\Models\ReedemItems;
use Illuminate\Http\Request;

class ReedemItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = ReedemItems::all();
        return view('reedemItems.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Add Reedem Items';
        $data = (object)[
            'id' => 0,
            'item' => '',
            'point' => '',
            'type' => 'create',
        ];
        return view('reedemItems.form', compact('data', 'title'));
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
            'item' => 'required',
            'point' => 'required',
        ]);
       

        $input['item'] = $request['item'];
        $input['point'] = $request['point'];

        try {
            ReedemItems::create($input);
            return redirect('/reedemItems')->with('status', 'Berhasil menambah data');
        } catch (\Throwable $th) {
            return $th;
            return back('/reedemItems/create')->with('status', 'Ggagal menambah data');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ReedemItems  $reedemItem
     * @return \Illuminate\Http\Response
     */
    public function show(ReedemItems $reedemItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ReedemItems  $reedemItem
     * @return \Illuminate\Http\Response
     */
    public function edit(ReedemItems $reedemItem)
    {
        $title = 'Edit Reedem Items';
        $data = (object)[
            'id' => $reedemItem->id,
            'item' =>  $reedemItem->item,
            'point' =>  $reedemItem->point,
            'type' => 'edit',
        ];
        return view('reedemItems.form', compact('data', 'title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ReedemItems  $reedemItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ReedemItems $reedemItem)
    {
        $request->validate([
            'item' => 'required',
            'point' => 'required',
        ]);
        $input['item'] = $request['item'];
        $input['point'] = $request['point'];
       

        try {
            ReedemItems::where('id', $reedemItem->id)->update($input);
            return redirect('/reedemItems')->with('status', 'Berhasil mengubah data');
        } catch (\Throwable $th) {
            return $th;
            return back('/reedemItems/create')->with('status', 'Ggagal mengubah data');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ReedemItems  $reedemItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(ReedemItems $reedemItem)
    {
        try {
            ReedemItems::where('id', $reedemItem->id)->delete();
            return redirect('/reedemItems')->with('status', 'Berhasil menghapus data');
        } catch (\Throwable $th) {
            return $th;
            return redirect('/reedemItems')->with('error', 'Gagal menghapus data');
        }
    }
}
