<?php

namespace App\Http\Controllers\admin\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\captain\Captain;
use App\Models\captain\ExtraSession;
use App\Models\manager\Manager;
use App\Models\manager\ManagerAttendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ViewController extends Controller
{
    public function all(){
        $managers=Manager::all();

        $pendingManagers=count(Manager::where("profile_status","pending")->get());
        $approvedManagers=count(Manager::where("profile_status","approved")->get());
        $branch=Branch::all();
        $context= [
            "managers"=>$managers,
            "approvedManagers"=>$approvedManagers,
            "pendingManagers"=>$pendingManagers,
            "branches"=>$branch
        ];

        return view("admin.manager.all",$context);
    }


    public function single(Request $request, Manager $manager){
        $user=User::find($manager->uid);
        $branch=Branch::find($manager->branchID);
        $attendance=ManagerAttendance::where("uid",$user->id)->get();
        $context=[
            "user"=>$user,
            "manager"=>$manager,
            "branch"=>$branch,
            "attendance"=>$attendance,

        ];

        return view("admin.manager.single",$context);
    }

}
