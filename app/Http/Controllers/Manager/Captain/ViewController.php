<?php

namespace App\Http\Controllers\Manager\Captain;

use App\Http\Controllers\Controller;
use App\Models\captain\Captain;
use App\Models\captain\ExtraSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ViewController extends Controller
{
    public function all() {

        $captain=Captain::all()
            ->where("profile_status","approved")
            ->where("upgraded","false");

        $pendingCaptains=Captain::all()
            ->where("profile_status","pending")
            ->where("upgraded","false");

        return view("manager.captain.all",["captains"=>$captain,"pendingCaptains"=>$pendingCaptains]);

    }

    public function single(Request $request, Captain $captain){
        $user=User::find($captain->uid);
        $captainSchedule=DB::select("SELECT * FROM captain_schedules WHERE uid=$user->id and DAY(date) = DAY(CURRENT_DATE()) and MONTH(date) = MONTH(CURRENT_DATE())  ORDER BY date DESC ");
        $captainMonthSchedule=DB::select("SELECT * FROM captain_schedules WHERE uid=$user->id and MONTH(date) = MONTH(CURRENT_DATE())  ORDER BY date DESC ");
        $extraSession=ExtraSession::all()->where("uid",$user->id);
        $capRatings=\App\Models\captain\Rating::all()->where("uid",$user->id);
        $totalVal=0;
        $totalRatings=0;
        foreach ($capRatings as $r){
            $totalVal+=$r->value;
            $totalRatings+=1;
        }

        $averageRating=$totalRatings!=0?$totalVal/$totalRatings:0;
        $stars=round(($averageRating/100)*5,1);
        $context=[
            "user"=>$user,
            "captain"=>$captain,
            "captainSchedule"=>$captainSchedule,
            "captainMonthSchedule"=>$captainMonthSchedule,
            "extraSession"=>$extraSession,
            "stars"=>$stars,
        ];

        return view("manager.captain.single",$context);
    }

}
