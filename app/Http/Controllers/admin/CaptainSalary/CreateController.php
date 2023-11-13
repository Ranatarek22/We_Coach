<?php

namespace App\Http\Controllers\admin\CaptainSalary;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementHistory;
use App\Models\Branch;
use App\Models\captain\Captain;
use App\Models\captain\CaptainSallary;
use App\Models\captain\ExtraSession;
use App\Models\CaptainSchedule;
use App\Models\intern\Intern;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Psy\Util\Str;

class CreateController extends Controller
{

    public function createSalary(Request $request){

        $captains = Captain::with('user')->get();

        foreach ($captains as $captain) {
            $user = $captain->user;

            $all_sessions = CaptainSchedule::where('uid', $user->id)
                ->whereMonth('date', date('m'))
                ->whereYear('date', date('Y'))
                ->get();

            $sessions_attended = $all_sessions->where('attended', "true");
            $sessions_absent = $all_sessions->where('attended', "false");

            $extra_sessions = ExtraSession::where('uid', $user->id)
                ->whereMonth('session_date', date('m'))
                ->whereYear('session_date', date('Y'))
                ->get();
            $total_hours=count($extra_sessions)+count($sessions_attended);
            $salary=$captain->money_per_hour!=null?$captain->money_per_hour*$total_hours:0;

            $month=date("Y-m");
            $captain_salary=CaptainSallary::where("uid",$user->id)->where("month",$month)->get()->first();
            if($captain_salary){
                $captain_salary->update([
                    "salary"=>$salary,
                    "sessions_number"=>count($all_sessions),
                    "attended_sessions"=>count($sessions_attended),
                    "absent_sessions"=>count($sessions_absent),
                    "extra_sessions"=>count($extra_sessions),
                ]);
            }
            else{
                CaptainSallary::create([
                    "uid"=>$user->id,
                    "sessions_number"=>count($all_sessions),
                    "attended_sessions"=>count($sessions_attended),
                    "absent_sessions"=>count($sessions_absent),
                    "extra_sessions"=>count($extra_sessions),
                    "salary"=>$salary,
                    "month"=>$month,
                ]);
            }
        }

        return back()->with("message","Salaries Created Successfully");

    }

}
