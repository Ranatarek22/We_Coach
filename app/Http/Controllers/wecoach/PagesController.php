<?php

namespace App\Http\Controllers\wecoach;

use App\Http\Controllers\Controller;
use App\Models\Academy;
use App\Models\Branch;
use App\Models\captain\Captain;
use App\Models\PackageType;
use App\Models\SubscriptionSchedule;
use App\Models\SubscriptionType;
use App\Models\User;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function index (){
        $branches=Branch::all()->where("branch_status","activated");
        $captains=Captain::all();
        $context=[
          "branches"=>$branches,
          "captains"=>$captains,

        ];

        return view("wecoach.index",$context);

    }

    public function products (){


        return view("wecoach.products");

    }

    public function services (){


        return view("wecoach.services");

    }

    public function branchSingle(Request $request,Branch $branch){

        $subs=SubscriptionType::where("branchID",$branch->id)->get();
        $resp=[
            "branch"=>$branch,
            "subscriptions"=>$subs
        ];



//        dd($resp);
       return view("wecoach.branch",$resp);

    }


    public function captainSingle(Request $request,Captain $captain){
        $user=User::find($captain->uid);
        $capRatings=\App\Models\captain\Rating::all()->where("uid",$user->id);
        $totalVal=0;
        $totalRatings=0;
        foreach ($capRatings as $r){
            $totalVal+=$r->value;
            $totalRatings+=1;
        }

        $averageRating=$totalRatings!=0?$totalVal/$totalRatings:0;
        $stars=round(($averageRating/100)*5,1);
        $resp=[
            "user"=>$user,
            "captain"=>$captain,
            "stars"=>$stars
        ];


//        dd($resp);
        return view("wecoach.captainProfile",$resp);

    }


}
