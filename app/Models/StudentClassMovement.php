<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentClassMovement extends Model
{
    use HasFactory;

    public const TYPE_JUMP = 'jump_class';
    public const TYPE_TRANSFER = 'transfer_class';
    public const TYPE_UPDATE = 'update_class';

    protected $table = 'student_class_movements';
    protected $guarded = [];

    private const FIELDS = ['price_id', 'day1', 'day2', 'course_time', 'teacher_id'];

    /**
     * Catat perpindahan kelas satu student.
     * $from / $to: array dengan key price_id, day1, day2, course_time, teacher_id.
     * Tidak mencatat apa pun bila kelas sebelum dan sesudahnya sama persis.
     */
    public static function record(int $studentId, string $type, array $from, array $to, ?string $userName = null): ?self
    {
        $row = ['student_id' => $studentId, 'type' => $type, 'user_name' => $userName];
        $changed = false;

        foreach (self::FIELDS as $field) {
            $row['from_' . $field] = $from[$field] ?? null;
            $row['to_' . $field] = $to[$field] ?? null;
            $changed = $changed || (string) $row['from_' . $field] !== (string) $row['to_' . $field];
        }

        return $changed ? static::create($row) : null;
    }

    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id');
    }
}
