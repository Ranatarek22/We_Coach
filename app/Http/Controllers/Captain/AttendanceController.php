<?php

namespace App\Http\Controllers\Captain;

use App\Http\Controllers\Controller;
use App\Models\captain\CaptainAttendance;
use App\Models\CaptainSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function attendancePage(){
        $currentDate = date("Y-m-d");
        $currentTime = Carbon::now();
        $context = [];
        $session = CaptainSchedule::all()->where("date", $currentDate)->where("uid", auth()->id());
        if(count($session) == 0){
            return redirect(route("captain"))->with("error", "No sessions for today");
        }
        foreach ($session as $s){
            $sessionAttendance=CaptainAttendance::where("sessionID",$s->id)->get()->first();
            if($sessionAttendance){
                if($sessionAttendance->second_scan=="false"){
                    $context['session'] = $s;
                    return view("captain.attendance",$context);
                }
            }

            $tmp=explode(" ", $s->start_time);
            $tmp2=explode(":",$tmp[0]);
            if(strlen($tmp2[1])==1){
                $time=$tmp2[0].':'.$tmp2[1].'0 '.$tmp[1];
            }
            else{
                $time= $s->start_time;
            }
            $sessionTime = Carbon::createFromFormat('h:iA', $time); // Create a Carbon instance from the session start time in 12-hour format
            $diffInMinutes = $currentTime->diffInMinutes($sessionTime); // Get the difference in minutes between the current time and session time
            $diffInMinutes2=$sessionTime->diffInMinutes($currentTime);
            if($diffInMinutes <= 10 or $diffInMinutes2>=0 and $diffInMinutes2<=15){

                $context['session'] = $s;
                return view("captain.attendance",$context);
            }
        }

        return redirect(route("captain"))->with("error", "Session time is not close yet Scan before session by max 10 min");
    }

    public function record(CaptainSchedule $session){
//        if($session->attended=="pending"){
//            $session->update(["attended"=>"true"]);
//        }
        $sessionAttendance=CaptainAttendance::all()->where("sessionID",$session->id);
        if(count($sessionAttendance)>0){
            $flag=false;
            foreach ($sessionAttendance as $sa){
                if($sa->second_scan=="false"){
                    $time=date('h:i:s A');
                    $sa->update(["second_scan"=>"true","second_scan_time"=>$time]);
                    $flag=true;
                    break;
                }
            }

            if(!$flag){
                CaptainAttendance::create([
                    "capID"=>auth()->user()->id,
                    "sessionID"=>$session->id,
                    "session_date"=>$session->date,
                    "session_time"=> date('h:i:s A'),
                    "first_scan"=> "true",
                    "first_scan_time"=> date('h:i:s A'),
                ]);
            }

        }
        else{
            CaptainAttendance::create([
                "capID"=>auth()->user()->id,
                "sessionID"=>$session->id,
                "session_date"=>$session->date,
                "session_time"=> date('h:i:s A'),
                "first_scan"=> "true",
                "first_scan_time"=> date('h:i:s A'),
            ]);
        }

        return redirect(route("captain"));

    }

    public function confirmAttendance(Request $request, CaptainSchedule $session){
        $status=$request->input("status");
        if($status=="true"){
        $session->update(["attended"=>"true"]);

        }
        else{
        $session->update(["attended"=>"false"]);

        }
        $session->save();
        return back()->with("message","Session Confirmation Done");
    }

}
