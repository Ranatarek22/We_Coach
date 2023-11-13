<?php

namespace App\Http\Controllers\admin\Branch;

use App\Http\Controllers\Controller;
use App\Models\Academy;
use App\Models\Branch;
use App\Models\captain\Captain;
use App\Models\intern\Intern;
use App\Models\SubscriptionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ViewController extends Controller
{
    public function index(){

        $branches=Branch::all();
        $subscription=SubscriptionType::all();

        return view("admin.branch.index",["branches"=>$branches,"subscription"=>$subscription]);
    }

    public function single(Request $request,Branch $branch){
        $managers=$branch->managers()->get();
        $watercard=$branch->waterCard()->get()->first();
        $interns=Intern::all();
        $internsWecoach=Intern::all()->where("academyID","1")->where("branch",$branch->id);
        $internsWaves=Intern::all()->where("academyID","2")->where("branch",$branch->id);
        $captains=Captain::all();
        $cardPercent= $watercard? ($watercard->card_credit_temp/$watercard->card_credit)*100:0;

        $incomeWecoach=DB::select("SELECT SUM(value) as totalIncomes FROM incomes WHERE MONTH(incomeDate) = MONTH(CURRENT_DATE()) and branchID=$branch->id and academyID=1")[0];
        $incomeWaves=DB::select("SELECT SUM(value) as totalIncomes FROM incomes WHERE MONTH(incomeDate) = MONTH(CURRENT_DATE()) and branchID=$branch->id and academyID=2")[0];
        if($incomeWecoach->totalIncomes==null){
            $incomeWecoach->totalIncomes=0;
        }
        if($incomeWaves->totalIncomes==null){
            $incomeWaves->totalIncomes=0;
        }

        $outcomeWecoach=DB::select("SELECT SUM(value) as totalOutcome FROM outcomes WHERE MONTH(outcomeDate) = MONTH(CURRENT_DATE()) and branchID=$branch->id and academyID=1")[0];
        $outcomeWaves=DB::select("SELECT SUM(value) as totalOutcome FROM outcomes WHERE MONTH(outcomeDate) = MONTH(CURRENT_DATE()) and branchID=$branch->id and academyID=2")[0];
        if($outcomeWecoach->totalOutcome==null){
            $outcomeWecoach->totalOutcome=0;
        }
        if($outcomeWaves->totalOutcome==null){
            $outcomeWaves->totalOutcome=0;
        }
        $captainSchedule=DB::select("SELECT * FROM captain_schedules WHERE  branchID=$branch->id and DAY(date) = DAY(CURRENT_DATE()) and MONTH(date) = MONTH(CURRENT_DATE()) and YEAR(date) = YEAR(CURRENT_DATE()) ORDER BY date DESC ");
        $captainMonthSchedule=DB::select("SELECT * FROM captain_schedules WHERE  branchID=$branch->id and Month(date) = Month(CURRENT_DATE()) and YEAR(date) = YEAR(CURRENT_DATE()) ORDER BY date DESC ");
        $services=SubscriptionType::all()->where("branchID",$branch->id);

        $context=[
            "branch"=>$branch,
            "managers"=>$managers,
            "interns"=>$interns,
            "captains"=>$captains,
            "watercard"=>$watercard,
            "cardPercent"=>$cardPercent,
            "incomeWecoach"=>$incomeWecoach,
            "incomeWaves"=>$incomeWaves,
            "outcomeWecoach"=>$outcomeWecoach,
            "outcomeWaves"=>$outcomeWaves,
            "internsWecoach"=>$internsWecoach,
            "internsWaves"=>$internsWaves,
            "captainSchedule"=>$captainSchedule,
            "captainMonthSchedule"=>$captainMonthSchedule,
            "services"=>$services,

        ];

        return view("admin.branch.single",$context);
    }

    public function all() {

        $branch=Branch::all();

        return view("admin.branch.all",["branches"=>$branch]);

    }

}
