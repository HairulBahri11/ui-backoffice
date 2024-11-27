<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Advertises;
use App\Models\Announces;
use App\Models\Attendance;
use App\Models\AttendanceDetail;
use Illuminate\Http\Request;

class InfoController extends Controller
{
    public function getAdvertise()
    {
        try {
            $result = Advertises::orderBy('id', 'DESC')->take(5)->get();
            return response()->json([
                'code' => '00',
                'payload' => $result,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => '400',
                'error' => 'internal server error', 'message' => $th,
            ], 403);
        }
    }

    public function getAnnouncement()
    {
        try {
            $result = Announces::where('announce_for', 'staff')
                ->orderBy('id', 'desc')
                ->take(5)->get();
            return response()->json([
                'code' => '00',
                'payload' => $result,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => '400',
                'error' => 'internal server error', 'message' => $th,
            ], 403);
        }
    }

    public function getAgenda($studentId)
    {
        try {
            $result = AttendanceDetail::join('attendances', 'attendances.id', 'attendance_details.attendance_id')
                ->select('attendances.activity', 'attendances.date')
                ->where('attendance_details.student_id', $studentId)
                ->orderBy('attendance_details.id', 'DESC')
                ->take(5)->get();

            return response()->json([
                'code' => '00',
                'payload' => $result,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => '400',
                'error' => 'internal server error', 'message' => $th,
            ], 403);
        }
    }
}
