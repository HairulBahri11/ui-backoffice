<?php

namespace App\Http\Controllers\API;

use App\Models\Students;
use App\Models\Announces;
use App\Models\Advertises;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\AttendanceDetail;
use App\Http\Controllers\Controller;

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
                'error' => 'internal server error',
                'message' => $th,
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
                'error' => 'internal server error',
                'message' => $th,
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
                'error' => 'internal server error',
                'message' => $th,
            ], 403);
        }
    }

    public function countAttendence($studentId)
    {
        $student = Students::find($studentId);

        if (!$student) {
            return response()->json([
                'code' => '01',
                'message' => 'Siswa tidak ditemukan',
            ], 404);
        }

        $data = AttendanceDetail::join('attendances as atd', 'atd.id', 'attendance_details.attendance_id')
            ->where('atd.price_id', $student->priceid)
            ->where('attendance_details.student_id', $studentId)
            ->selectRaw('
                COUNT(*) as total_pertemuan,
                SUM(CASE WHEN is_absent = 0 THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN is_absent = 1 AND is_alpha = 1 THEN 1 ELSE 0 END) as total_alpha,
                SUM(CASE WHEN is_absent = 1 AND is_permission = 1 THEN 1 ELSE 0 END) as total_izin
            ')
            ->first();

        $totalPertemuan = (int) $data->total_pertemuan;
        $totalHadir = (int) $data->total_pertemuan - (int) $data->total_alpha - (int) $data->total_izin;

        $persentaseHadir = $totalPertemuan > 0 ? round(($totalHadir / $totalPertemuan) * 100, 2) : 0;

        return response()->json([
            'code' => '00',
            'payload' => [
                'total_pertemuan' => $totalPertemuan,
                'hadir' => $totalHadir,
                'alpha' => (int) $data->total_alpha,
                'izin'  => (int) $data->total_izin,
                'persentase_hadir' => $persentaseHadir . '%'
            ],
        ]);
    }
}
