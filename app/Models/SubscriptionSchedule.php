<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionSchedule extends Model
{
    use HasFactory;

    protected $fillable=[
        "branchID",
        "subsID",
        "day",
        "start_time",
        "end_time",
        "created_at",
        "updated_at",
    ];
}
