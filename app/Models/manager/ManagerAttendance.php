<?php

namespace App\Models\manager;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagerAttendance extends Model
{
    use HasFactory;
    protected $fillable=[
        "uid",
        "branchID",
        "date",
        "time",
        "created_at",
        "updated_at",
    ];
}
