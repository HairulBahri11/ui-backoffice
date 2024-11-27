<?php

namespace App\Http\Controllers;

use App\Models\OrderReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewTestPaperController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::guard('teacher')->check() == true) {
            $data = OrderReview::with('teacher')->where('id_teacher', Auth::guard('teacher')->user()->id)->where('is_done', '0')->orderBy('id', 'DESC')->get();
        } else {
            $data = OrderReview::with('teacher')->where('is_done', '0');
            if ($request->from && $request->to) {
                $data = $data->whereBetween('due_date', [$request->from, $request->to]);
            }
            $data = $data->orderBy('id', 'DESC')->get();
        }
        return view('order-review.index', compact('data'));
    }

    public function done($id)
    {
        DB::beginTransaction();
        try {
            $model = OrderReview::find($id);
            $model->is_done = true;
            $model->save();
            DB::commit();
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
        return redirect('/review')->with('status', 'Berhasil mengupdate');
    }
    public function comment($id, Request $request)
    {
        DB::beginTransaction();
        try {
            $model = OrderReview::find($id);
            $model->comment = $request->comment;
            $model->save();
            DB::commit();
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
        return redirect('/review')->with('status', 'Berhasil mengupdate');
    }

    public function destroy($id)
    {
        try {
            OrderReview::findOrFail($id)->delete();
            return redirect('/review')->with('status', 'Berhasil menghapus data');
        } catch (\Throwable $th) {
            return $th;
            return redirect('/review')->with('error', 'Gagal menghapus data');
        }
    }
}
