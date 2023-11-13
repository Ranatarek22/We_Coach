<?php

namespace App\Models\captain;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraSession extends Model
{
    use HasFactory;
    protected $fillable=[
        "uid",
        "branchID",
        "absent_cap",
        "session_date",
        "session_time",
        "created_at",
        "updated_at",

    ];
}
