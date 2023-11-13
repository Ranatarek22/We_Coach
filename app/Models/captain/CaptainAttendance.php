<?php

namespace App\Models\captain;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaptainAttendance extends Model
{
    use HasFactory;
    protected $fillable=[
        "capID",
        "sessionID",
        "session_date",
        "session_time",
        "first_scan",
        "first_scan_time",
        "second_scan",
        "second_scan_time",
        "confirmed",
        "created_at",
        "updated_at",

    ];
}
