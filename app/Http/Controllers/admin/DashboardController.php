<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Academy;
use App\Models\admin\PromoCode;
use App\Models\AnnouncementHistory;
use App\Models\Branch;
use App\Models\captain\Captain;
use App\Models\captain\CaptainSallary;
use App\Models\captain\ExtraSession;
use App\Models\CaptainSchedule;
use App\Models\Income;
use App\Models\intern\Intern;
use App\Models\manager\Manager;
use App\Models\Outcome;
use App\Models\User;
use App\Models\WaterCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(){
        $internsWecoach=Intern::all()->where("academyID","1");
        $internsWaves=Intern::all()->where("academyID","2");
        $captains=Captain::all();
        $managers=Manager::all();
        $branches=Branch::all();
//        $watercard=WaterCard::all()->first();
//        $cardpercent=($watercard->card_credit/5000)*100;
        $incomeWecoach=DB::select("SELECT SUM(value) as totalIncomes FROM incomes WHERE MONTH(incomeDate) = MONTH(CURRENT_DATE()) and academyID=1")[0];
        $incomeWaves=DB::select("SELECT SUM(value) as totalIncomes FROM incomes WHERE MONTH(incomeDate) = MONTH(CURRENT_DATE()) and academyID=2")[0];
        if($incomeWecoach->totalIncomes==null){
            $incomeWecoach->totalIncomes=0;
        }
        if($incomeWaves->totalIncomes==null){
            $incomeWaves->totalIncomes=0;
        }

        $outcomeWecoach=DB::select("SELECT SUM(value) as totalOutcome FROM outcomes WHERE MONTH(outcomeDate) = MONTH(CURRENT_DATE()) and academyID=1")[0];
        $outcomeWaves=DB::select("SELECT SUM(value) as totalOutcome FROM outcomes WHERE MONTH(outcomeDate) = MONTH(CURRENT_DATE()) and academyID=2")[0];
        if($outcomeWecoach->totalOutcome==null){
            $outcomeWecoach->totalOutcome=0;
        }
        if($outcomeWaves->totalOutcome==null){
            $outcomeWaves->totalOutcome=0;
        }
        $context=[
            "internsWecoach"=>$internsWecoach,
            "internsWaves"=>$internsWaves,
            "captains"=>$captains,
            "managers"=>$managers,
            "branches"=>$branches,
            "incomeWecoach"=>$incomeWecoach,
            "incomeWaves"=>$incomeWaves,

            "outcomeWecoach"=>$outcomeWecoach,
            "outcomeWaves"=>$outcomeWaves,
//            "watercard"=>$cardpercent,
        ];

        return view("admin.index",$context);
    }

    public function billing(){
        $branches=Branch::all();
        $incomeWecoach=DB::select("SELECT SUM(value) as totalIncomes FROM incomes WHERE DAY(incomeDate) = DAY(CURRENT_DATE()) and academyID=1")[0];
        $incomeWaves=DB::select("SELECT SUM(value) as totalIncomes FROM incomes WHERE DAY(incomeDate) = DAY(CURRENT_DATE()) and academyID=2")[0];
        if($incomeWecoach->totalIncomes==null){
            $incomeWecoach->totalIncomes=0;
        }
        if($incomeWaves->totalIncomes==null){
            $incomeWaves->totalIncomes=0;
        }

        $outcomeWecoach=DB::select("SELECT SUM(value) as totalOutcome FROM outcomes WHERE DAY(outcomeDate) = DAY(CURRENT_DATE()) and academyID=1")[0];
        $outcomeWaves=DB::select("SELECT SUM(value) as totalOutcome FROM outcomes WHERE DAY(outcomeDate) = DAY(CURRENT_DATE()) and academyID=2")[0];
        if($outcomeWecoach->totalOutcome==null){
            $outcomeWecoach->totalOutcome=0;
        }
        if($outcomeWaves->totalOutcome==null){
            $outcomeWaves->totalOutcome=0;
        }

        $incomes=Income::latest()->get();
        $outcomes=Outcome::latest()->get();

        $salaries=CaptainSallary::latest()->get();

        $context=[
            "branches"=>$branches,
            "incomeWecoach"=>$incomeWecoach,
            "incomeWaves"=>$incomeWaves,
            "academies"=>Academy::all(),
            "outcomeWecoach"=>$outcomeWecoach,
            "outcomeWaves"=>$outcomeWaves,
            "incomes"=>$incomes,
            "outcomes"=>$outcomes,
            "salaries"=>$salaries,
        ];

        return view("admin.billing.index",$context);
    }

    public function promocode(){
        $branches=Branch::all();
        $academy=Academy::all();
        $promocodes=PromoCode::all();
        $context=[
            "branches"=>$branches,
            "academies"=>$academy,
            "promocodes"=>$promocodes,
        ];

        return view("admin.promocode.index",$context);
    }

    public function announcements(){
        $branches=Branch::all();
        $academy=Academy::all();
        $announcements = AnnouncementHistory::latest()
            ->get()
            ->groupBy('announcement')
            ->map(function ($group) {
                return $group->first();
            });
        $context=[
            "branches"=>$branches,
            "academies"=>$academy,
            "announcements"=>$announcements,
        ];

        return view("admin.announcement.index",$context);
    }



}
