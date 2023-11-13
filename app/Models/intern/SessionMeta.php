<?php

namespace App\Models\intern;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionMeta extends Model
{
    use HasFactory;

    protected $fillable=[
        "uid",
        "capID",
        "month",
        "pay_method",
        "money_to_pay",
        "money_paid",
        "paid",
        "created_at",
        "updated_at",
    ];

}
