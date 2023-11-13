<?php

namespace App\Models\captain;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaptainSallary extends Model
{
    use HasFactory;
    protected $fillable=[
        "uid",
        "sessions_number",
        "attended_sessions",
        "absent_sessions",
        "extra_sessions",
        "salary",
        "month",
        "created_at",
        "updated_at",
    ];
}
