<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Parents;
use App\Models\ParentStudents;
use App\Models\PaymentBillDetail;
use App\Models\StudentScore;
use Carbon\Carbon;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UsersController extends Controller
{

    public function getOtp(Request $request)
    {
        try {
            $phone = "";
            $parent_name = $request->name;

            if (substr($request->phone, 0, 2) == '08') {
                $phone = str_replace(substr($request->phone, 0, 2), '62', $request->phone);
            } else if (substr($request->phone, 0, 3) == '+62') {
                $phone = str_replace(substr($request->phone, 0, 3), '62', $request->phone);
            } else {
                $phone = $request->phone;
            }

            $data = Parents::where('no_hp', $phone)->first();
            if ($parent_name) {
                if ($data) {
                    $otp = substr(str_shuffle("0123456789"), 0, 4);

                    $generate = Parents::where('no_hp', $phone)->update(['name' => $parent_name, 'otp' => $otp, 'password' => bcrypt($otp)]);
                    $message = 'Your verification code is: ' . $otp;

                    // $sendOTP =  Helper::sendMessage($phone, $message);
                    $students = ParentStudents::join('student', 'parent_students.student_id', 'student.id')->where('parent_id', $data->id)->first();
                    $data['default_student_id'] = $students->student_id;
                    $data['default_student_name'] = $students->name;
                    $data['default_student_class'] = $students->priceid;
                    $credentials = ([
                        'no_hp' => $phone,
                        'password' => $otp,
                    ]);

                    if ($generate) {
                        if ($token = JWTAuth::attempt($credentials)) {
                            // return $this->respondWithToken($token, 'parent');
                            return response()->json([
                                'code' => '00',
                                'data' => (object)$data,
                                'token' => $this->respondWithToken($token),
                            ]);
                        }
                        // return response()->json([
                        //     'code' => '00',
                        //     'message' => $message,
                        // ], 200);
                    } else {
                        return response()->json([
                            'code' => '10',
                            'message' => 'error',
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'code' => '10',
                        'message' => 'Nomor HP Salah',
                    ], 200);
                }
            } else {
                return response()->json([
                    'code' => '01',
                    'message' => 'Nama Orang Tua Harus Diisi',
                ], 200);
            }
        } catch (\Throwable $th) {
            return $th;
            return response()->json([
                'code' => '400',
                'error' => 'internal server error', 'message' => $th,
            ], 403);
        }
    }

    public function sendOtp(Request $request)
    {
        try {
            $phone = "";
            $parent_name = $request->name;

            if (substr($request->phone, 0, 2) == '08') {
                $phone = str_replace(substr($request->phone, 0, 2), '62', $request->phone);
            } else if (substr($request->phone, 0, 3) == '+62') {
                $phone = str_replace(substr($request->phone, 0, 3), '62', $request->phone);
            } else {
                $phone = $request->phone;
            }

            $data = Parents::where('no_hp', $phone)->first();
            if ($parent_name) {
                if ($data) {
                    $otp = substr(str_shuffle("0123456789"), 0, 4);

                    $generate = Parents::where('no_hp', $phone)->update(['name' => $parent_name, 'otp' => $otp, 'password' => bcrypt($otp)]);
                    $message = 'Your verification code is: ' . $otp;

                    $sendOTP =  Helper::sendMessage($phone, $message);
                    // $students = ParentStudents::join('student', 'parent_students.student_id', 'student.id')->where('parent_id', $data->id)->first();
                    // $data['default_student_id'] = $students->student_id;
                    // $data['default_student_name'] = $students->name;
                    // $credentials = ([
                    //     'no_hp' => $phone,
                    //     'password' => $otp,
                    // ]);

                    if ($generate && $sendOTP) {
                        // if ($token = JWTAuth::attempt($credentials)) {
                        //     // return $this->respondWithToken($token, 'parent');
                        //     return response()->json([
                        //         'code' => '00',
                        //         'data' => (object)$data,
                        //         'token' => $this->respondWithToken($token),
                        //     ]);
                        // }
                        return response()->json([
                            'code' => '00',
                            'message' => $message,
                        ], 200);
                    } else {
                        return response()->json([
                            'code' => '10',
                            'message' => 'error',
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'code' => '10',
                        'message' => 'Nomor HP Salah',
                    ], 200);
                }
            } else {
                return response()->json([
                    'code' => '01',
                    'message' => 'Nama Orang Tua Harus Diisi',
                ], 200);
            }
        } catch (\Throwable $th) {
            return $th;
            return response()->json([
                'code' => '400',
                'error' => 'internal server error', 'message' => $th,
            ], 403);
        }
    }

    public function authenticate(Request $request)
    {
        $phone = "";

        try {
            if (substr($request->phone, 0, 2) == '08') {
                $phone = str_replace(substr($request->phone, 0, 2), '62', $request->phone);
            } else if (substr($request->phone, 0, 3) == '+62') {
                $phone = str_replace(substr($request->phone, 0, 3), '62', $request->phone);
            } else {
                $phone = $request->phone;
            }
            $credentials = ([
                'no_hp' => $phone,
                'password' => $request->otp,
            ]);

            $data = Parents::where('no_hp', $phone)
                ->where('otp', $request->otp)
                ->first();

            if ($data) {
                $students = ParentStudents::join('student', 'parent_students.student_id', 'student.id')->join('price', 'student.priceid','price.id')->where('parent_id', $data['id'])->first();
                $data['default_student_id'] = $students->student_id;
                $data['default_student_name'] = $students->name;
                $data['default_student_class'] = $students->priceid;
                $data['default_student_class_name'] = $students->program;
                if ($token = JWTAuth::attempt($credentials)) {
                    // return $this->respondWithToken($token, 'parent');
                    return response()->json([
                        'code' => '00',
                        'data' => (object)$data,
                        'token' => $this->respondWithToken($token),
                    ]);
                }
            } else {
                return response()->json([
                    'code' => '10',
                    'message' => 'Login credentials are invalid.',
                ], 400);
            }
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'code' => '10',
                    'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
            return response()->json([
                'code' => '00',
                'message' => 'Could not create token.',
            ], 500);
        }
    }

    public function submitOtp(Request $request)
    {
        // return $request->header('Authorization');
        try {
            $data = Parents::where('no_hp', $request->phone)
                ->where('otp', $request->otp)
                ->first();
            if ($data) {
                $token = substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTVWXYZ"), 0, 32);
                $result = ([
                    'token' => $token,
                    'data' => $data,
                ]);
                return response()->json([
                    'code' => '00',
                    'payload' => $result,
                ], 200);
            } else {
                return response()->json([
                    'code' => '10',
                    'message' => 'Nomor HP Salah',
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'code' => '400',
                'error' => 'internal server error',
                'message' => $th,
            ], 403);
        }
    }

    public function students($parentId)
    {
        try {
            $students = [];
            $data = ParentStudents::join('student', 'student.id', 'parent_students.student_id')->join('price','student.priceid','price.id')
                ->select('price.program','student.*')
                ->where('parent_students.parent_id', $parentId)
                ->get();
            foreach ($data as $key => $val) {

                $score = StudentScore::where('student_id', $val->id)
                    ->select('average_score')
                    ->orderBy('id', 'DESC')
                    ->first();
                $billing = PaymentBillDetail::where('category', 'COURSE')->where('status', 'Waiting')->where('payment', 'COURSE ' . Carbon::now()->format('m-Y'))->where('student_id', $val->id)->sum('price');

                if ($score && $billing) {
                    $student = array_merge($val->toArray(), ([
                        'student_id' => str_pad($val->id, 6, '0', STR_PAD_LEFT),
                        'average_score' => $score->average_score,
                        'price' => $billing,
                    ]));
                    array_push($students, $student);
                } else {
                    $student = array_merge($val->toArray(), ([
                        'student_id' => str_pad($val->id, 6, '0', STR_PAD_LEFT),
                        'average_score' => 0,
                        'price' => 0,
                    ]));
                    array_push($students, $student);
                }
            }
            return response()->json([
                'code' => '00',
                'payload' => $students,
            ], 200);
        } catch (\Throwable $th) {
            return $th;
            return response()->json([
                'code' => '400',
                'error' => 'internal server error',
                'message' => $th,
            ], 403);
        }
    }

    protected function respondWithToken($token)
    {
        return ([
            'access_token' => $token,
            'token_type' => 'bearer',
            // 'expires_in' =>  env('JWT_TTL', 60*24*365),
        ]);
    }
}
