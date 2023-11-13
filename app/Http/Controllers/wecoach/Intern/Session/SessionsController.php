<?php

namespace App\Http\Controllers\wecoach\Intern\Session;

use App\Http\Controllers\Controller;
use App\Models\Academy;
use App\Models\admin\PromoCode;
use App\Models\Branch;
use App\Models\CaptainSchedule;
use App\Models\Income;
use App\Models\intern\Intern;
use App\Models\intern\InternSessionHistory;
use App\Models\intern\SessionMeta;
use App\Models\PackageType;
use App\Models\SubscriptionSchedule;
use App\Models\SubscriptionType;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class SessionsController extends Controller
{
    public function form(){
        $user=auth()->user();
        $sessions_meta=DB::select("SELECT * FROM session_metas WHERE uid=$user->id and MONTH(month)=MONTH(CURRENT_DATE()) and YEAR(month)=YEAR(CURRENT_DATE())");
        $sessions_meta=count($sessions_meta)==0?null:$sessions_meta[0];

        if($sessions_meta!=null){
            return redirect(route("wecoach"))->with("error","انت مشتركة بالفعل");
        }
        $intern=$user->interns()->get()->first();
        $academy=Academy::find($intern->academyID);
        $branches=Branch::all()->where("branch_status","activated");
        $context=[
            "academy"=>$academy,
            "branches"=>$branches
        ];


        return view("wecoach.apply.index",$context);
    }

    public function reserve(Request $request){
        $uid=auth()->user()->id;
        $capID=$request->input("capID");
        $branchID=$request->input("branchID");
        $subsID=$request->input("subType");
        $groupType=$request->input("group_type");
        $money=$request->input("money_to_pay");
        $day=$request->input("daytoattend");
        $package=PackageType::find($groupType);
        $sessionNum=$package->sessions_number;
        $intern=Intern::where("uid",$uid)->get()->first();
        $start_time=$request->input("schedTime");

        // Set the default timezone to your desired timezone
        date_default_timezone_set('Africa/Cairo');

        // Get the current month and year
        $month = date('m');
        $year = date('Y');

        // Create an empty array to store the captains
        $sessions = [];

        // Loop through each day of the current month
        for ($i = date("d"); count($sessions) < $sessionNum && $i <= 31; $i++) {
            // Create a DateTime object for the current day
            $date = new DateTime("$year-$month-$i");
            // Check if the day is the same as the user input day
            if (strtolower( $date->format('l')) == $day) {
                // Get the captain schedule for the current day and branch
                $captainSched = CaptainSchedule::where("branchID", $branchID)
                    ->where("date", $date)
                    ->where(function ($query) use ($package) {
                        $query->where("session_type", $package->package_name)->orWhereNull("session_type");

                    })->where("start_time", $start_time)->get()->first();


                if($captainSched!=null){

                    $sessions[]=$captainSched;
                }

            }
        }
//        dd($sessions);

        foreach ($sessions as $session){

            InternSessionHistory::create([
                "uid"=>$uid,
                "capID"=>$capID,
                "sessionID"=>$session->id,
                "sessionTime"=>$start_time
            ]);
            if($package->package_limit==5){
            $tmp=$session->session_limit;
            $session->update([
                "session_limit"=>$tmp-1,
                "session_type"=>$package->package_name
            ]);

            }
            else if($package->package_limit==2){
                if($session->session_limit==5){
                    $session->update([
                        "session_limit"=>1,
                        "session_type"=>$package->package_name
                    ]);
                }
                else {
                    $session->update([
                        "session_limit"=>$session->session_limit-1,
                        "session_type"=>$package->package_name
                    ]);
                }
            }

            else if($package->package_limit==1){
                if($session->session_limit==5){
                    $session->update([
                        "session_limit"=>0,
                        "session_type"=>$package->package_name
                    ]);
                }
            }

        }

        if($intern->level){
            $intern->update([
                "branch"=>$branchID,
                "subType"=>$subsID,
                "group_type"=>$groupType,
                "level"=>$intern->level,
                "ill_history"=>$request->input("ill_history"),
            ]);
        }
        else{
            $intern->update([
                "branch"=>$branchID,
                "subType"=>$subsID,
                "group_type"=>$groupType,
                "level"=>"0",
                "ill_history"=>$request->input("ill_history"),
            ]);
        }

        $session_meta=SessionMeta::create([
            "uid"=>$uid,
            "capID"=>$capID,
            "month"=>date('Y-m-d'),
            "pay_method"=>$request->input("payment_method"),
            "money_to_pay"=>$money
        ]);

        $response=[
            "package_name"=>$package->package_name,
            "package_number"=>$package->sessions_number,
            "branch"=>Branch::find($branchID)->name,
            "money"=>Branch::find($branchID)->name,
        ];
        $user=auth()->user();
        Income::create([
            'academyID' => $intern->academyID,
            'branchID' => $intern->branch,
            'incomeType' => "اشتراك جديد - $user->name",
            'value' => $money,
            'incomeDate' => date("Y-m-d"),
        ]);

        return view("wecoach.apply.finish");
    }




//    Ajax
    protected function getPackOfSubs(Request $request){

        $SubsID=$request->input("subsID");
        $subcPack=PackageType::where("subsID",$SubsID)->get();

//        dd($branchSubs);

        return response()->json($subcPack);
    }

    protected function getSchedOfSubs(Request $request){

        $SubsID=$request->input("subsID");
        $subcSched=SubscriptionSchedule::where("subsID",$SubsID)->get();

//        dd($branchSubs);

        return response()->json($subcSched);
    }

    protected function getBranchCap(Request $request) {
        $branchID = $request->input("branchID");
        $day = $request->input("day");
        $hour=$request->input("hour");
        $tmp=explode(":",$hour);
        $tmp2=explode(" ",$tmp[1]);
        if($tmp2[0]=="00"){
            $hour2=$tmp[0].':'.'0 '.$tmp2[1];

        }
        else{
            $hour2=$hour;
        }
//        return response()->json($hour2) ;

        $packageID=$request->input("packageID");
        $package=PackageType::all()->find($packageID);
        // Set the default timezone to your desired timezone
        date_default_timezone_set('Africa/Cairo');

        // Get the current month and year
        $month = date('m');
        $year = date('Y');

        // Create an empty array to store the captains
        $captains = [];

        // Loop through each day of the current month
        for ($i = 1; $i <= 31; $i++) {
            // Create a DateTime object for the current day
            $date = new DateTime("$year-$month-$i");
            // Check if the day is the same as the user input day
            if (strtolower( $date->format('l')) == $day) {
                $date2=date("$year-$month-$i");
                // Get the captain schedule for the current day and branch
//                $tmp3=explode(" ",$date->date)[0] ;

                $captainSched = CaptainSchedule::where("branchID", $branchID)
                    ->where("date", $date2)
                    ->where(function ($query) use ($package) {
                        $query->where("session_type", $package->package_name)->orWhereNull("session_type");

                    })
                    ->where("start_time", $hour2)->get();

//                return response()->json($hour2) ;
                // Loop through each captain in the schedule
                foreach ($captainSched as $cs) {

//                    if($cs->session_limit !=0){
                        // Get the captain object from the user id
                        $captain = User::find($cs->uid);
                        // Add the captain to the array if they are not already in it
                        if (!in_array($captain, $captains)) {
                            $captains[] = $captain;
                        }
//                    }


                }
            }
        }

        return response()->json(array_unique($captains));
    }

    protected function getPackPrice(Request $request){

        $packID=$request->input("packID");
        $pack=PackageType::find($packID);

//        dd($branchSubs);

        return response()->json(["price"=>$pack->price]);
    }

//    protected function getPackPrice(Request $request){
//
//        $subsID=$request->input("subsID");
//        $packName=$request->input("packName");
//        $packNum=$request->input("packNumber");
//        $pack=PackageType::where("subsID",$subsID)
//            ->where("package_name",$packName)
//            ->where("sessions_number",$packNum)
//            ->get()->first();
//
////        dd($branchSubs);
//
//        return response()->json(["price"=>$pack->price]);
//    }

    protected function promocode(Request $request){
        $promocode=$request->input("promocode");
        $branch=$request->input("branch");
        $intern=Intern::where("uid",auth()->id())->get()->first();
        $promo=PromoCode::where("code",$promocode)->where("branchID",$branch)->where("academyID",$intern->academyID)->first();
        $money=$request->input('money');
        $resp=[];
        if($promo){
            if(\date("Y-m-d")<=$promo->end_date){
                $tmp=$money*($promo->discount_percent/100);
                $money=$money-$tmp;
                $resp["message"]="Promocode Used";
                $resp["status"]="true";
            }
            else{
                $resp["message"]="Promocode Expired";
                $resp["status"]="false";

            }
        }
        else{
            $resp["message"]="Promocode Expired";
            $resp["status"]="false";

        }

        $resp["price"]=$money;
        return response()->json($resp);

    }

}
