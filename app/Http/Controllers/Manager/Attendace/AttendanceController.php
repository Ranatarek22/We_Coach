<?php

namespace App\Http\Controllers\Manager\Attendace;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\manager\ManagerAttendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(){
        $branches=Branch::all()->where("branch_status","activated");
        $context=[
          "branches"=>$branches
        ];
        return view("manager.attendance",$context);
    }

    public function takeAttendance(Request $request){
        $branch=$request->input("branchID");
        $user=auth()->user()->id;
        $date=date("Y-m-d");
        $time=date("H:m:i");
        $data=[
            "uid"=>$user,
            "branchID"=>$branch,
            "date"=>$date,
            "time"=>$time,
        ];
        ManagerAttendance::create($data);
        return redirect(route("manager.profile"))->with("message","Attendance Taken");
    }
}
