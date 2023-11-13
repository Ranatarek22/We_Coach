<?php

namespace App\Http\Controllers\waves;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\captain\Captain;
use App\Models\intern\Intern;
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

        return view("waves.index",$context);

    }

    public function products (){


        return view("waves.products");

    }

    public function services (){


        return view("waves.services");

    }

    public function branchSingle(Request $request,Branch $branch){

        $subs=SubscriptionType::where("branchID",$branch->id)->get();
        $resp=[
            "branch"=>$branch,
            "subscriptions"=>$subs
        ];


//        dd($resp);
        return view("waves.branch",$resp);

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
        return view("waves.captainProfile",$resp);

    }
}
