<?php

namespace App\Http\Controllers\API;

use PDF;
use Helper;
use DateTime;
use Throwable;
use Carbon\Carbon;
use App\Models\Price;
use App\Models\Parents;
use App\Models\Students;
use App\Models\PaymentBill;
use Illuminate\Http\Request;
use App\Models\HistoryBilling;
use App\Models\ParentStudents;
use App\Models\PaymentFromApp;
use App\Models\PaymentBillDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\PaymentFromAppDetail;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function getHistory(Request $request, $studentId)
    {
        try {

            $query = [];
            $class = Students::join('price', 'student.priceid', 'price.id')->where('student.id', $studentId)->first();
            $query = HistoryBilling::join('payment_bill_detail as pbd', 'pbd.unique_code', 'history_billing.unique_code')
                ->select('history_billing.*')
                ->where('pbd.student_id', $studentId)
                ->distinct();

            if ($request->start && $request->end) {
                $query = $query->whereBetween('history_billing.created_at',  [$request->start . " 00:00", $request->end . " 23:59"]);
            }
            $data = $query->paginate($request->perpage);
            $class = Students::join('price', 'price.id', 'student.priceid')
                ->select('price.program')
                ->where('student.id', $studentId)->first();
            return response()->json([
                'code' => '00',
                'class' => $class->program,
                'payload' => $data,
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

    public function getDetailHistory($idPayment)
    {
        try {
            // $data = PaymentFromAppDetail::join('payment_from_apps as pfa', 'pfa.id', 'payment_from_app_details.payment_from_app_id')
            //     ->join('student as st', 'st.id', 'pfa.student_id')
            //     ->select('st.name', 'payment_from_app_details.*')
            //     ->where('payment_from_app_details.payment_from_app_id', $idPayment)
            //     ->orderBy('payment_from_app_details.id', 'ASC')
            //     ->get();
            $data = PaymentBillDetail::join('student as st', 'st.id', 'payment_bill_detail.student_id')
                ->select('payment_bill_detail.*', 'st.name')
                ->where('payment_bill_detail.unique_code', $idPayment)->get();
            return response()->json([
                'code' => '00',
                'payload' => $data,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => '400',
                'error' => 'internal server error',
                'message' => $th,
            ], 403);
        }
    }

    public function listBill($studentId)
    {
        try {
            $class = Students::join('price', 'student.priceid', 'price.id')->where('student.id', $studentId)->first();
            $tmp = PaymentBillDetail::join('student', 'student.id', 'payment_bill_detail.student_id')
                ->select('student.name', 'payment_bill_detail.*')
                ->where('payment_bill_detail.student_id', $studentId)
                ->where('payment_bill_detail.category', 'COURSE')->where('payment_bill_detail.status',  'Waiting')->where('payment_bill_detail.payment', 'COURSE ' . Carbon::now()->format('m-Y'))
                ->get();
            $data = [];
            foreach ($tmp as $value) {
                $value->student_id = str_pad($value->student_id, 6, '0', STR_PAD_LEFT);
                array_push($data, $value);
            }
            $class = Students::join('price', 'price.id', 'student.priceid')
                ->select('price.program')
                ->where('student.id', $studentId)->first();
            return response()->json([
                'code' => '00',
                'class' => $class->program,
                'payload' => $data,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => '400',
                'error' => 'internal server error',
                'message' => $th,
            ], 403);
        }
    }

    public function checkout(Request $request)
    {
        try {
            $uniqCode = substr($request->total_payment, 0, -3) . rand(111, 999);
            $code = 'PB' . date('ymd') . rand(1, 9);

            $history = HistoryBilling::create([
                'amount' => $uniqCode,
                'unique_code' => $code,
                'created_by' => Auth::guard('parent')->user()->id,
            ]);
            for ($i = 0; $i < count($request->id_bill); $i++) {
                PaymentBillDetail::where('id', $request->id_bill[$i])
                    ->update([
                        'unique_code' => $code,
                    ]);
            }
            $data = ([
                'total_pay' => $uniqCode,
                'id_transaction' => $code,
            ]);
            return response()->json([
                'code' => '00',
                'payload' => $data,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => '400',
                'error' => 'internal server error',
                'message' => $th,
            ], 403);
            return $th;
        }
    }

    public function verifyPayment($transId)
    {
        try {
            $data = HistoryBilling::where('unique_code', $transId)->first();
            $student = PaymentBillDetail::join('student', 'student.id', 'payment_bill_detail.student_id')
                ->select('student.name')
                ->where('payment_bill_detail.unique_code', $transId)->first();
            PaymentBillDetail::where('unique_code', $transId)
                ->update([
                    'status' => 'To Be Confirm'
                ]);
            $amount =  "Rp " . number_format($data->amount, 0, ',', '.');
            $message = $student->name . " telah melakukan pembayaran dengan nominal *" . $amount . "* dengan kode pembayaran *" . $data->unique_code . "*";

            $send = Helper::sendMessage(env('ADMIN_PHONE'), $message);

            if ($send) {
                return response()->json([
                    'code' => '00',
                    'payload' => 'Success',
                ], 200);
            } else {
                return response()->json([
                    'code' => '10',
                    'message' => 'Failed verify payment, please try again later',
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

    function eReceipt($transId, $phoneNumber, Request $request)
    {
        $program = $request->program;

        try {
            // $data = HistoryBilling::where('unique_code', $transId)->first();
            $data = DB::table('payment')->where('id', $transId)->first();
            $amount =  "Rp " . number_format($data->total, 0, ',', '.');

            $url_cetak = '';

            if (strpos($program, "Private") !== false) {
                //redirect(base_url("escpos/example/reprintprivate.php?id=".$id));
                $url_cetak = "https://ui-payment.primtechdev.com/cetak/printprivate_parent/" . $data->id;
            } elseif (strpos($program, "Regular") !== false) {
                //redirect("escpos/example/reprintregular.php?id=".$id));
                $url_cetak = "https://ui-payment.primtechdev.com/cetak/printregular_parent/" . $data->id;
            } else {
                $url_cetak = "https://primtech-sistem.com/ui-master-update/cetak/printother_parent/" . $data->id;
            }

            // $url_receipt = '<a href="' . $url_cetak . '">' . $url_cetak . '</a>';
            // $message = "Pembayaran dengan nominal *" . $amount . "* dengan kode pembayaran *" . $data->id . "*"  .
            //     "Berikut link bukti pembayaran : " . $url_receipt;

            $message = sprintf(
                "*Payment is successful*\n\n" .
                    "Total Amount: *%s*\n" .
                    "Payment Code: *%s*\n\n\n" .
                    "This is your e-receipt link:\n\n%s\n\n" . // Extra newline added here
                    "Thank you for preserving our environment by being paperless. \n\n" .
                    "This WhatsApp number (0823-3890-5700) is U&I English Course's official number specified to send:\n" .
                    "1. the link of E-Receipt for any payments\n" .
                    "2. OTP code for U&I's App Member\n" .
                    "Please save this number to enable the activation of E-Receipt Link. \n\n" .
                    "-U&I English Course-",
                $amount,
                $data->id,
                $url_cetak
            );

            //  $message = sprintf(
            //                 "*Payment is successful*\n\n" .
            //                     "Total Amount: *%s*\n" .
            //                     "Payment Code: *%s*\n\n\n" .
            //                     "_This is your e-receipt link:_\n%s",
            //                     // "Thank you for preserving our environment by being paperless. \n\n" .
            //                     // "-U&I English Course-",
            //                     $amount,
            //                 $data->id,
            //                 $url_cetak
            //             );

            $send = Helper::sendMessage($phoneNumber, $message);
            if ($send) {
                return response()->json([
                    'code' => '00',
                    'payload' => 'Success',
                ], 200);
            } else {
                return response()->json([
                    'code' => '10',
                    'message' => 'Failed verify payment, please try again later',
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

    public function printInvoice($paymentId)
    {
        // $data = DB::select('select py.total, py.method, py.number, py.bank, py.trfdate, pd.id, pd.paymentid, pd.studentid, pd.voucherid, pd.category, pd.monthpay, SUM(pd.amount) as subtotal, s.name, p.program, pd.explanation
        // FROM paydetail pd
        // INNER JOIN student s ON pd.studentid = s.id
        // INNER JOIN price p ON s.priceid = p.id
        // INNER JOIN payment py ON pd.paymentid = py.id
        // WHERE pd.paymentid = ?
        // GROUP BY pd.studentid', [$paymentId]);
        // $data = PaymentFromAppDetail::join('payment_from_apps as pfa', 'pfa.id', 'payment_from_app_details.payment_from_app_id')
        //     ->join('student as st', 'st.id', 'pfa.student_id')
        //     ->join('price as pr', 'pr.id', 'st.priceid')
        //     ->select('st.name', 'st.id as student_id', 'pr.program', 'payment_from_app_details.*')
        //     ->where('payment_from_app_details.payment_from_app_id', $paymentId)
        //     ->orderBy('payment_from_app_details.id', 'ASC')
        //     ->get();
        $data = PaymentBillDetail::join('student as st', 'st.id', 'payment_bill_detail.student_id')
            ->join('price as pr', 'pr.id', 'st.priceid')
            ->select('payment_bill_detail.*', 'st.name', 'pr.program')
            ->where('payment_bill_detail.unique_code', $paymentId)->get();
        $detail = HistoryBilling::where('unique_code', $paymentId)->first();

        // return $data;
        $fileName = "invoice_payment_" . $paymentId . ".pdf";

        $width = 5.5 / 2.54 * 72;
        $height = 18 / 2.54 * 72;
        $customPaper = array(0, 0, $height, $width);
        $pdf = PDF::loadview('report.print', ['data' => $data, 'detail' => $detail])->setPaper($customPaper, 'landscape');
        return $pdf->download($fileName);
        // return  $pdf->stream($fileName);
    }

    public function getBillMonth($studentId)
    {
        try {
            $detailPaid = PaymentBillDetail::where('student_id', $studentId)->where('category', 'COURSE')->orderBy('id', 'DESC')->first();
            $payDetail = DB::table('paydetail')->where('studentid', $studentId)->where('category', 'COURSE')->first();
            $exPayDetailMonth = explode('-', $payDetail->monthpay);
            $getPayDetailMonth = (int)$exPayDetailMonth[1]/*  . '-' . $exPayDetailMonth[0] */;
            $student = Students::find($studentId);
            $price = Price::find($student->priceid);
            $detailPaidPenalty = PaymentBillDetail::where('student_id', $studentId)->where('category', 'COURSE')->where('payment', '!=', 'COURSE ' . Carbon::now()->format('m') . '-' . Carbon::now()->year)->where('status', '!=', 'paid')->where('is_penalty_payment', 'true')->orderBy('id', 'DESC')->update([
                'is_penalty' => 'true'
            ]);
            $getParent = ParentStudents::where('student_id', $studentId)->first();
            $parent = Parents::find($getParent->parent_id);
            if ($detailPaid != null) {
                $exMonth = explode(' ', $detailPaid->payment);
                $month = explode('-', $exMonth[1]);
                $detailPaid->month = (int)$month[0];
                $exCourse = explode('COURSE ', $detailPaid->payment);
                $exCourseMonth = explode('-', $exCourse[1]);
                if ($getPayDetailMonth != (int)$exCourseMonth[0]) {
                    if ($detailPaid->month != now()->month) {
                        $model = new PaymentBill();
                        $model->class_type = $price->program != 'Private' || $price->program != 'Semi Private' ? 'Reguler' : 'Private';
                        $model->total_price = $price->course;
                        // $model->created_by = $parent->name;
                        // $model->updated_by = $parent->name;
                        $model->created_by = Auth::guard('parent')->user()->name;
                        $model->updated_by = Auth::guard('parent')->user()->name;
                        $model->save();

                        $modelDetail = new PaymentBillDetail();
                        $modelDetail->id_payment_bill = $model->id;
                        $modelDetail->student_id = $studentId;
                        $modelDetail->category = 'COURSE';
                        $modelDetail->price = $price->course;
                        $modelDetail->unique_code = '-';
                        $modelDetail->payment = now()->month < 10 ? 'COURSE 0' . now()->month . '-' . now()->year : 'COURSE ' . now()->month . '-' . now()->year;
                        $modelDetail->status = 'Waiting';
                        $modelDetail->save();
                        return response()->json([
                            'code' => '00',
                            'payload' => 'Success1 ' . $detailPaid->id . '/' . $payDetail->id
                        ], 200);
                    } else {
                        return response()->json([
                            'code' => '00',
                            'payload' => 'Pembayaran untuk bulan ini sudah tertagih',
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'code' => '00',
                        'payload' => 'Pembayaran untuk bulan ini sudah terbayar',
                    ], 200);
                }
            } else {
                $model = new PaymentBill();
                $model->class_type = $price->program != 'Private' || $price->program != 'Semi Private' ? 'Reguler' : 'Private';
                $model->total_price = $price->course;
                // $model->created_by = $parent->name;
                // $model->updated_by = $parent->name;
                $model->created_by = Auth::guard('parent')->user()->name;
                $model->updated_by = Auth::guard('parent')->user()->name;
                $model->save();

                $modelDetail = new PaymentBillDetail();
                $modelDetail->id_payment_bill = $model->id;
                $modelDetail->student_id = $studentId;
                $modelDetail->category = 'COURSE';
                $modelDetail->price = $price->course;
                $modelDetail->unique_code = '-';
                $modelDetail->payment = now()->month < 10 ? 'COURSE 0' . now()->month . '-' . now()->year : 'COURSE ' . now()->month . '-' . now()->year;
                $modelDetail->status = 'Waiting';
                $modelDetail->save();
                return response()->json([
                    'code' => '00',
                    'payload' => 'Success3',
                ], 200);
            }
            return response()->json([
                'code' => '00',
                'payload' => 'Nothing Happend',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => '400',
                'error' => 'internal server error ' . $th,
            ], 403);
        }
    }

    function broadcastLatePayment(Request $request)
    {
        // try{
        //     // Ambil data dari body request
        // $data = $request->input(); // Jika data dikirim sebagai JSON

        // // Jika data dalam format JSON, langsung decode jika diperlukan
        // $decodedData = is_array($data) ? $data : json_decode($data, true);
        //     // $data = urldecode($data);
        //     foreach($decodedData as $datanya){
        //         $message = sprintf(
        //             "*📢 Announcement 📢*\n\n" .
        //                 "Dear parents of *%s*,\n\n" .
        //                 "We would like to kindly remind you that the latest course payment was for *%s*. " .
        //                 "Please ensure to complete the payment for this month's course at your earliest convenience.\n\n" .
        //                 "Thank you for your prompt attention to this matter.\n\n" .
        //                 "This WhatsApp number (0823-3890-5700) is the official contact number of *U&I English Course* and is used exclusively for:\n" .
        //                 "1. Sending the E-Receipt link for any payments.\n" .
        //                 "2. Delivering OTP codes for U&I's App Member.\n\n" .
        //                 "Please save this number in your contacts to activate the E-Receipt Link feature.\n\n" .
        //                 "Thank you, and have a great day!\n\n" .
        //                 "- U&I English Course -"
        //             ,
        //             $datanya['name'],
        //             $datanya['lastpaydate']
        //         );
        //     $send = Helper::sendMessage($datanya['phone'], $message);
        //     if ($send) {
        //         return response()->json([
        //             'code' => '00',
        //             'payload' => 'Success',
        //         ], 200);
        //     } else {
        //         return response()->json([
        //             'code' => '10',
        //             'message' => 'Failed verify payment, please try again later',
        //         ], 200);
        //     }
        // }
        // } catch (\Throwable $th) {
        //     return response()->json([
        //         'code' => '400',
        //         'error' => 'internal server error',
        //         'message' => $th,
        //     ], 403);
        // }


        try {
            $data = $request->input();
            $decodedData = is_array($data) ? $data : json_decode($data, true);
            Log::info($decodedData);

            if (!is_array($decodedData)) {
                return response()->json([
                    'code' => '400',
                    'error' => 'Invalid data format',
                    'message' => 'Data should be an array',
                ], 400);
            }

            $success = [];
            $failed = [];
            $currentDate = new DateTime();
            $monthlyFee = 300000;

            foreach ($decodedData as $datanya) {
                if (!isset($datanya['name'], $datanya['phone'], $datanya['lastpaydate'])) {
                    $failed[] = $datanya['phone'] ?? 'Unknown Phone';
                    continue;
                }

                $lastPayDate = DateTime::createFromFormat('Y-m-d', $datanya['lastpaydate']);
                if (!$lastPayDate) {
                    $failed[] = $datanya['phone'];
                    continue;
                }

                // 🟢 Perbaikan LOOP bulan yang belum dibayar
                $monthsUnpaid = [];
                $totalAmount = 0;
                $monthIterator = clone $lastPayDate;
                $monthIterator->modify('+1 month');

                while ((int)$monthIterator->format('Ym') <= (int)$currentDate->format('Ym')) {
                    $monthsUnpaid[] = $monthIterator->format('F Y');
                    $totalAmount += $monthlyFee;
                    $monthIterator->modify('+1 month');
                }

                if (empty($monthsUnpaid)) {
                    continue;
                }

                // $message = "*📢 Pengumuman 📢*\n\n" .
                //     "*Yth: Orang tua murid " . $datanya['name'] . ",*\n\n" .
                //     "Mohon segera melakukan pembayaran *SPP* untuk bulan berikut:\n\n" .
                //     implode(", ", $monthsUnpaid) . "\n\n" .
                //     "Total yang harus dibayar: *Rp" . number_format($totalAmount, 0, ',', '.') . "*.\n\n" .
                //     "● Pembayaran bisa dilakukan langsung di *front desk U&I* atau transfer ke *BCA (Lie Citro Dewi Ruslie) 464 1327 187* hingga akhir bulan ini.\n\n" .
                //     "● Pembayaran lewat batas waktu akan dikenakan *biaya keterlambatan 10%*.\n\n" .
                //     "Terima kasih.\n\n" .
                //     "*U&I ENGLISH COURSE*\n\n" .
                //     "*NB: Abaikan pesan ini jika telah melakukan pembayaran.*";

                $message = "*📢 PENGUMUMAN PEMBAYARAN 📢*\n\n" .
                    "*Yth. Bapak/Ibu Orang Tua/Wali Murid " . $datanya['name'] . ",*\n\n" .
                    "Mohon segera melakukan pembayaran *SPP* untuk bulan berikut:\n" .
                    implode(", ", $monthsUnpaid) . "\n\n" .
                    "*Total yang harus dibayar: Rp" . number_format($totalAmount, 0, ',', '.') . "*\n\n" .
                    "📌 *Pembayaran dapat dilakukan melalui:*\n" .
                    "• Front desk *U&I* (tunai/kartu)\n" .
                    "• Transfer ke *BCA a.n. Lie Citro Dewi Ruslie*\n" .
                    "  No. Rek: *464 1327 187*\n\n" .
                    "*Batas waktu pembayaran adalah sampai akhir bulan ini.*\n" .
                    "Pembayaran lewat batas waktu tersebut akan dikenakan *biaya keterlambatan 10%*.\n\n" .
                    "📌 *Konfirmasi Pembayaran:*\n" .
                    "Jika Bapak/Ibu telah membayar namun belum menerima e-struk/nota, mohon kirimkan:\n" .
                    "• Bukti transfer\n" .
                    "• Nama siswa/No. ID\n" .
                    "• Program/Kelas\n" .
                    "Agar pembayaran dapat diproses dengan tepat.\n\n" .
                    "Terima kasih atas kerjasamanya. 🙏\n\n" .
                    "*U&I English Course*\n\n" .
                    "_NB: Abaikan pesan ini jika pembayaran sudah dilakukan dan dikonfirmasi._";


                // 🟢 Perbaikan jumlah argumen di Helper::sendBroadCast
                $send = Helper::sendBroadCast($datanya['phone'], $message); // Pastikan hanya mengirim argumen yang dibutuhkan
                Log::info($send);

                if ($send) {
                    $success[] = $datanya['phone'];
                } else {
                    $failed[] = $datanya['phone'];
                }
            }

            return response()->json([
                'code' => '00',
                'payload' => [
                    'success' => $success,
                    'failed' => $failed,
                ],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => '400',
                'error' => 'internal server error',
                'message' => $th->getMessage(),
            ], 403);
        }
    }

    public function sendPaymentReminders(Request $request)
    {
        try {
            // Mengambil input dari request
            $data = $request->input();

            // Mendekode data jika bukan array (misalnya dari JSON string)
            // Menggunakan operator null coalescing untuk memastikan $data selalu array
            $decodedData = is_array($data) ? $data : json_decode($data, true);

            // Log data yang diterima untuk debugging
            // Log::info('Data diterima untuk pengingat pembayaran:', ['data' => $decodedData]);

            // Validasi format data utama
            if (!is_array($decodedData)) {
                // Log::warning('Format data tidak valid: Bukan array.', ['received_data' => $data]);
                return response()->json([
                    'code' => '400',
                    'error' => 'Invalid data format',
                    'message' => 'Data should be an array of student records.',
                ], 400);
            }

            $success = []; // Array untuk menyimpan nomor telepon yang berhasil
            $failed = [];  // Array untuk menyimpan nomor telepon yang gagal
            $currentDate = new DateTime(); // Tanggal saat ini
            $monthlyFee = 300000; // Biaya SPP bulanan

            // Melakukan iterasi untuk setiap data siswa
            foreach ($decodedData as $datanya) {
                // Validasi kelengkapan data siswa yang dibutuhkan
                if (!isset($datanya['name'], $datanya['phone'], $datanya['lastpaydate'])) {
                    $failed[] = $datanya['phone'] ?? 'Unknown Phone (missing name, phone, or lastpaydate)';
                    // Log::warning('Data siswa tidak lengkap, dilewati.', ['student_data' => $datanya]);
                    continue; // Lanjutkan ke data siswa berikutnya
                }

                // Membuat objek DateTime dari tanggal pembayaran terakhir
                $lastPayDate = DateTime::createFromFormat('Y-m-d', $datanya['lastpaydate']);
                if (!$lastPayDate) {
                    $failed[] = $datanya['phone'] . ' (Invalid lastpaydate format)';
                    // Log::warning('Format tanggal pembayaran terakhir tidak valid, dilewati.', ['phone' => $datanya['phone'], 'lastpaydate' => $datanya['lastpaydate']]);
                    continue; // Lanjutkan ke data siswa berikutnya
                }

                // Inisialisasi array untuk bulan-bulan yang belum dibayar dan total jumlah
                $monthsUnpaid = [];
                $totalAmount = 0;
                $monthIterator = clone $lastPayDate; // Duplikasi tanggal pembayaran terakhir
                $monthIterator->modify('+1 month'); // Mulai dari bulan setelah pembayaran terakhir

                // Loop untuk mencari bulan-bulan yang belum dibayar hingga bulan saat ini
                while ((int)$monthIterator->format('Ym') <= (int)$currentDate->format('Ym')) {
                    $monthsUnpaid[] = $monthIterator->format('F Y'); // Tambahkan bulan ke daftar
                    $totalAmount += $monthlyFee; // Tambahkan biaya bulanan ke total
                    $monthIterator->modify('+1 month'); // Maju ke bulan berikutnya
                }

                // Jika tidak ada bulan yang belum dibayar, lewati siswa ini
                if (empty($monthsUnpaid)) {
                    // Log::info('Tidak ada tunggakan pembayaran untuk ' . $datanya['name'], ['phone' => $datanya['phone']]);
                    continue; // Lanjutkan ke data siswa berikutnya
                }

                // Membuat pesan pengingat pembayaran
                $message = "*📢 PENGUMUMAN PEMBAYARAN 📢*\n\n" .
                    "*Yth. Bapak/Ibu Orang Tua/Wali Murid " . $datanya['name'] . ",*\n\n" .
                    "Mohon segera melakukan pembayaran *SPP* untuk bulan berikut:\n" .
                    implode(", ", $monthsUnpaid) . "\n\n" .
                    "*Total yang harus dibayar: Rp" . number_format($totalAmount, 0, ',', '.') . "*\n\n" .
                    "📌 *Pembayaran dapat dilakukan melalui:*\n" .
                    "• Front desk *U&I* (tunai/kartu)\n" .
                    "• Transfer ke *BCA a.n. Lie Citro Dewi Ruslie*\n" .
                    " No. Rek: *464 1327 187*\n\n" .
                    "*Batas waktu pembayaran adalah sampai akhir bulan ini.*\n" .
                    "Pembayaran lewat batas waktu tersebut akan dikenakan *biaya keterlambatan 10%*.\n\n" .
                    "📌 *Konfirmasi Pembayaran:*\n" .
                    "Jika Bapak/Ibu telah membayar namun belum menerima e-struk/nota, mohon kirimkan:\n" .
                    "• Bukti transfer\n" .
                    "• Nama siswa/No. ID\n" .
                    "• Program/Kelas\n" .
                    "Agar pembayaran dapat diproses dengan tepat.\n\n" .
                    "Terima kasih atas kerjasamanya. 🙏\n\n" .
                    "*U&I English Course*\n\n" .
                    "_NB: Abaikan pesan ini jika pembayaran sudah dilakukan dan dikonfirmasi._";

                // Memanggil Helper untuk mengirim broadcast
                // Pastikan Helper::sendBroadCast menerima argumen sesuai urutan: (phone, message)
                $send = Helper::sendBroadCast($datanya['phone'], $message);

                // Log hasil dari Helper::sendBroadCast untuk debugging
                // Log::info('Hasil pengiriman broadcast untuk ' . $datanya['phone'] . ':', ['status' => $send]);

                // Memeriksa apakah pengiriman berhasil
                if ($send) {
                    $success[] = $datanya['phone']; // Tambahkan ke daftar berhasil
                } else {
                    $failed[] = $datanya['phone']; // Tambahkan ke daftar gagal
                }
            }

            // Mengembalikan respons JSON dengan status keberhasilan dan kegagalan
            return response()->json([
                'code' => '00',
                'payload' => [
                    'success' => $success,
                    'failed' => $failed,
                ],
            ], 200);
        } catch (Throwable $th) {
            // Menangkap dan mencatat setiap pengecualian yang terjadi
            // Log::error('Terjadi kesalahan internal server saat memproses pengingat pembayaran:', [
            //     'error_message' => $th->getMessage(),
            //     'file' => $th->getFile(),
            //     'line' => $th->getLine(),
            //     'trace' => $th->getTraceAsString(),
            // ]);

            // Mengembalikan respons JSON untuk kesalahan internal server
            return response()->json([
                'code' => '400',
                'error' => 'internal server error',
                'message' => $th->getMessage(),
            ], 403);
        }
    }


    public function billDetail($studentId)
    {
        // 1. Cek apakah student aktif
        $student = Students::where('id', $studentId)->first();

        if ($student->status != 'ACTIVE') {
            return response()->json([
                'code' => '01',
                'message' => 'Student tidak aktif atau tidak ditemukan',
                'payload' => [
                    'bill' => 0,
                    'student_data' => $student,
                    'unpaid_months' => [],
                    'last_payment' => null,
                ],
            ]);
        }

        // 2. Ambil pembayaran terakhir
        $lastPay = DB::table('paydetail')
            ->where('studentid', $studentId)
            ->where('category', 'COURSE')
            ->orderByDesc('monthpay')
            ->first();

        $lastPaidMonth = $lastPay->monthpay ?? null;
        $unpaidMonths = [];

        // 3. Hitung bulan belum dibayar
        if ($lastPaidMonth) {
            $start = new DateTime($lastPaidMonth);
            $start->modify('+1 month');
        } else {
            $start = new DateTime(date('Y-01-01'));
        }

        $now = new DateTime();

        while ((int)$start->format('Ym') <= (int)$now->format('Ym')) {
            $unpaidMonths[] = $start->format('F Y');
            $start->modify('+1 month');
        }

        // 4. Batasi hanya bulan sekarang jika belum bayar ≥ 4 bulan
        if (count($unpaidMonths) >= 4) {
            $unpaidMonths = [date('F Y')]; // hanya bulan ini yang dibolehkan dibayar
        }

        return response()->json([
            'code' => '00',
            'payload' => [
                'student_data' => $student,
                'last_payment' => $lastPaidMonth ? date('F Y', strtotime($lastPaidMonth)) : null,
                'unpaid_months' => $unpaidMonths,
            ],
        ]);
    }
}
