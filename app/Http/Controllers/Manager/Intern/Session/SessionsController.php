<?php

namespace App\Http\Controllers\Manager\Intern\Session;

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

    public function reserve(Request $request){

        $intern=Intern::find($request->input("internID"));
        $uid=User::find($intern->uid);
        $capID=$request->input("capID");
        $branchID=$request->input("branchID");
        $subsID=$request->input("subType");
        $groupType=$request->input("group_type");
        $day=$request->input("daytoattend");
        $package=PackageType::find($groupType);
        $sessionNum=$package->sessions_number;
        $start_time=$request->input("schedTime");
        $money=$package->price;
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
                "uid"=>$uid->id,
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
            "uid"=>$uid->id,
            "capID"=>$capID,
            "month"=>date('Y-m-d'),
            "pay_method"=>$request->input("payment_method"),
            "money_to_pay"=>$money
        ]);

        Income::create([
            'academyID' => $intern->academyID,
            'branchID' => $intern->branch,
            'incomeType' => "اشتراك جديد - $uid->name",
            'value' => $money,
            'incomeDate' => date("Y-m-d"),
        ]);

        return back()->with("message","Sessions Reserved Successfully");
    }






}
