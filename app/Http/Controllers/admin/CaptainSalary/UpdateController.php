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

class UpdateController extends Controller
{
//    salary
    public function updateSalaryForm(Request $request, CaptainSallary $salary){

        return view("admin.captain.salary",["salary"=>$salary]);
    }
    public function updateSalary(Request $request, CaptainSallary $salary){
        $salary->update([
            "salary"=>$request->input("salary")
        ]);
        return back()->with("message","salary updated successfully");
    }

}
