<?php

namespace App\Http\Controllers;

use App\Models\DocumentPrintout;
use App\Models\PrintOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PrintOutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (Auth::guard('teacher')->check()) {
            $teacherId = Auth::guard('teacher')->id();
            $printOut = PrintOut::with('teacher', 'documentPrintouts', 'price')->where('teacher_id', $teacherId)->orderBy('created_at', 'desc')->get();
        } else {
            $printOut = PrintOut::with('teacher', 'documentPrintouts', 'price',)->orderBy('created_at', 'desc')->get();
        }

        return view('print_out.index', compact('printOut'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $teacherId = Auth::guard('teacher')->id();

        if (!$teacherId) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        // Fetch unique schedules belonging strictly to the logged-in teacher
        $classes = DB::table('student')
            ->join('price', 'price.id', '=', 'student.priceid')
            ->join('day as day_one', 'day_one.id', '=', 'student.day1')
            ->join('day as day_two', 'day_two.id', '=', 'student.day2')
            ->where('student.status', 'ACTIVE')
            ->where('student.id_teacher', $teacherId)
            ->whereNotNull('student.course_time')
            ->where('student.course_time', '!=', '')
            ->whereNotNull('student.priceid')
            ->select(
                'student.priceid as class_id',
                'price.program as program_name',
                'student.course_time',
                'student.day1 as day1_id',
                'student.day2 as day2_id',
                'day_one.day as day1_name',
                'day_two.day as day2_name'
            )
            ->orderBy('class_id', 'asc')
            ->distinct()
            ->get();

        // dd($classes);

        return view('print_out.create', compact('classes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'class_id'      => 'required',
    //         'course_time'   => 'required|string',
    //         'day1_id'       => 'required|integer',
    //         'day2_id'       => 'required|integer',
    //         'note'          => 'required|string',
    //         'document_file' => 'required|file|mimes:pdf,docx|max:5120', // Validate file types
    //     ]);

    //     try {
    //         $print = new PrintOut();
    //         $print->class_id    = (int)$request->class_id;
    //         $print->course_time = $request->course_time;
    //         $print->day1_id     = (int)$request->day1_id;
    //         $print->day2_id     = (int)$request->day2_id;
    //         $print->note        = $request->note;
    //         $print->teacher_id  = Auth::guard('teacher')->id();
    //         $print->created_at  = now();

    //         if ($request->hasFile('document_file')) {
    //             $file = $request->file('document_file');
    //             $filename = time() . '_' . $file->getClientOriginalName();
    //             $file->move(public_path('uploads/print_files'), $filename);
    //             $print->file_link = 'uploads/print_files/' . $filename; // Saves down file access route
    //         }

    //         $print->save();

    //         return redirect('/print-out')->with('success', 'Print request created successfully!');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->withInput()->with('error', 'Failed to save data: ' . $e->getMessage());
    //     }
    // }

    // public function store(Request $request)
    // {
    //     // Validasi input masal (ditambahkan ekstensi gambar jpeg, png, jpg)
    //     $request->validate([
    //         'class_id'         => 'required',
    //         'course_time'      => 'required|string',
    //         'day1_id'          => 'required|integer',
    //         'day2_id'          => 'required|integer',
    //         'note'             => 'required|string',
    //         'document_files'   => 'required|array',
    //         'document_files.*' => 'required|file|mimes:pdf,docx,jpeg,png,jpg|max:5120', // Maks 5MB per file
    //     ]);

    //     // Menggunakan DB Transaction demi keamanan integritas relasi data
    //     DB::beginTransaction();

    //     try {
    //         $teacherId = Auth::guard('teacher')->id();
    //         $files = $request->file('document_files');

    //         // Loop untuk memproses setiap file yang diunggah bersamaan
    //         foreach ($files as $file) {

    //             // A. Pindahkan file fisik ke folder public uploads
    //             $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
    //             $file->move(public_path('uploads/print_files'), $filename);
    //             $fileLink = 'uploads/print_files/' . $filename;

    //             // B. Buat record baru di tabel `document_print_out`
    //             $document = DocumentPrintout::create([
    //                 'file_link'  => $fileLink,
    //                 'created_at' => now(),
    //                 'updated_at' => now()
    //             ]);

    //             // C. Buat record baru di tabel `print_out` dengan note yang sama
    //             PrintOut::create([
    //                 'class_id'    => (int)$request->class_id,
    //                 'course_time' => $request->course_time,
    //                 'day1_id'     => (int)$request->day1_id,
    //                 'day2_id'     => (int)$request->day2_id,
    //                 'note'        => $request->note,      // Satu note diaplikasikan ke semua file
    //                 'teacher_id'  => $teacherId,
    //                 'id_document' => $document->id,       // Mengikat ID dari document_print_out yang baru dibuat
    //                 'created_at'  => now(),
    //                 'updated_at'  => now()
    //             ]);
    //         }

    //         // Terapkan semua data ke database jika loop aman tanpa error
    //         DB::commit();

    //         return redirect('/print-out')->with('success', 'Print request and all files created successfully!');
    //     } catch (\Exception $e) {
    //         // Gagalkan transaksi jika ada file yang bermasalah atau gagal unggah
    //         DB::rollBack();
    //         return redirect()->back()->withInput()->with('error', 'Failed to save data: ' . $e->getMessage());
    //     }
    // }


    public function store(Request $request)
    {
        // Validasi input masal
        $request->validate([
            'class_id'         => 'required',
            'course_time'      => 'required|string',
            'day1_id'          => 'required|integer',
            'day2_id'          => 'required|integer',
            'note'             => 'required|string',
            'document_files'   => 'required|array',
            'document_files.*' => 'required|file|mimes:pdf,docx,jpeg,png,jpg|max:5120',
        ]);

        // Menggunakan DB Transaction demi keamanan integritas relasi data
        DB::beginTransaction();

        try {
            $teacherId = Auth::guard('teacher')->id();
            $files = $request->file('document_files');

            // 1. Dapatkan nama teacher yang sedang login (Ganti spasi/simbol dengan underscore)
            $teacherName = Auth::guard('teacher')->user()->name ?? 'Teacher_' . $teacherId;
            $cleanTeacherName = str_replace(' ', '_', preg_replace('/[^A-Za-z0-9 ]/', '', $teacherName));

            // 2. Dapatkan nama kelas/program dari database berdasarkan class_id
            $classData = DB::table('price')->where('id', $request->class_id)->first();
            $className = $classData->program ?? 'Class_' . $request->class_id;
            $cleanClassName = str_replace(' ', '_', preg_replace('/[^A-Za-z0-9 ]/', '', $className));

            // 3. Format waktu upload saat ini
            $timestamp = now()->format('Ymd_His');

            // ==================== HANYA 1 KALI BUAT DATA INDUK ====================
            // Membuat record data induk sekali saja di tabel `print_out`
            $printOutParent = PrintOut::create([
                'class_id'    => (int)$request->class_id,
                'course_time' => $request->course_time,
                'day1_id'     => (int)$request->day1_id,
                'day2_id'     => (int)$request->day2_id,
                'note'        => $request->note, // Catatan global
                'teacher_id'  => $teacherId,
                'created_at'  => now(),
                'updated_at'  => now()
            ]);

            // Inisialisasi nomor urut file awal
            $fileNumber = 1;

            // ==================== LOOP DOKUMEN YANG DIUNGHAH ====================
            // Memasukkan setiap berkas ke tabel anak `document_print_out`
            foreach ($files as $file) {

                // Dapatkan ekstensi asli file
                $extension = $file->getClientOriginalExtension();

                // Generate string acak sepanjang 6 karakter untuk keunikan mutlak nama file
                $randomString = Str::random(6);

                // FORMAT RENAME SESUAI REQ: 1.kelasnya_teachernya_created_at_randomstring.ext
                $filename = $fileNumber . '.' . $cleanClassName . '_' . $cleanTeacherName . '_' . $timestamp . '_' . $randomString . '.' . $extension;

                // A. Pindahkan file fisik ke folder public uploads
                $file->move(public_path('uploads/print_files'), $filename);
                $fileLink = 'uploads/print_files/' . $filename;

                // B. Buat record baru di tabel `document_print_out` terikat ke ID Parent
                DocumentPrintout::create([
                    'id_printout' => $printOutParent->id, // Mengikat ke id di tabel print_out
                    'file_link'   => $fileLink,
                    'created_at'  => now(),
                    'updated_at'  => now()
                ]);

                // Naikkan nomor urut untuk berkas selanjutnya
                $fileNumber++;
            }

            // Terapkan semua data ke database jika loop aman tanpa error
            DB::commit();

            return redirect('/print-out')->with('success', 'Print request and all files created successfully!');
        } catch (\Exception $e) {
            // Gagalkan transaksi jika ada berkas yang bermasalah atau gagal unggah
            Log::error('Failed to store print request: ' . $e->getMessage());
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Failed to save data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // Gunakan database transaction agar jika salah satu proses gagal, data tidak pincang
            DB::beginTransaction();

            // Load data PrintOut beserta semua relasi dokumennya
            $printOut = PrintOut::with('documentPrintouts')->findOrFail($id);

            // 1. Looping untuk menghapus semua file fisik dari storage/public
            foreach ($printOut->documentPrintouts as $document) {
                if ($document->file_link && file_exists(public_path($document->file_link))) {
                    unlink(public_path($document->file_link));
                }

                // Hapus data baris dokumen dari tabel document_print_out
                $document->delete();
            }

            // 2. Hapus data utama dari tabel print_out
            $printOut->delete();

            DB::commit();

            return redirect('/print-out')->with('success', 'Print request and all files deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete data: ' . $e->getMessage());
        }
    }
}
