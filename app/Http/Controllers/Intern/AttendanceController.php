<?php

namespace App\Http\Controllers\Intern;

use App\Http\Controllers\Controller;
use App\Models\CaptainSchedule;
use App\Models\intern\InternSessionHistory;
use App\Models\WaterCard;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function form(Request $request){
        $user=auth()->user();
        $intern=$user->interns()->get()->first();
        $sessions=InternSessionHistory::all()->where("uid",$user->id);
        $internSession=null;
        foreach ($sessions as $s){
            $sessionData=CaptainSchedule::find($s->sessionID);
            $currentDate = date("Y-m-d");
            if($sessionData->date==$currentDate){
                $internSession=$s;
                break;
            }
        }
        if($internSession==null){
            if($intern->academyID==1){
                return redirect(route("wecoach"))->with("error","No sessions for today");
            }
            else{
                return redirect(route("waves"))->with("error","No sessions for today");

            }
        }
        if($intern->academyID==1){
            return view("wecoach.intern.attendance",["session"=>$internSession]);
        }
        else{
            return view("waves.intern.attendance",["session"=>$internSession]);

        }


    }

    public function record(Request $request){
        $user=auth()->user();
        $intern=$user->interns()->get()->first();
        $sessionID=$request->input("sessionID");
        $session=InternSessionHistory::find($sessionID);
        $session->update(["attendance"=>"true"]);
        $watercard=WaterCard::where("branchID",$intern->branch)->get()->first();
        $points=$watercard->card_credit_temp;
        if($points!=0){
            $points=$points-$watercard->point_per_person;
            $watercard->update([
               "card_credit_temp"=>$points
            ]);
        }

        if($intern->academyID==1){
            return redirect(route("wecoach"))->with("message","Attendance Taken Successfully");
        }
        else{
            return redirect(route("waves"))->with("message","Attendance Taken Successfully");

        }
    }

}
