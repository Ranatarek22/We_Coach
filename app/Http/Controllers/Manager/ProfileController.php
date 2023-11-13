<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Academy;
use App\Models\Branch;
use App\Models\Income;
use App\Models\intern\Intern;
use App\Models\manager\Manager;
use App\Models\Outcome;
use App\Traits\Manager\UpdatesManagers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    use UpdatesManagers;


    public function index(Request $request){
        $manager=Manager::where("uid",$request->user()->id)->get()->first();
        $branch=Branch::find($manager->branchID);
        $internsWecoach=Intern::all()->where("academyID","1")->where("branch",$manager->branchID);
        $internsWaves=Intern::all()->where("academyID","2")->where("branch",$manager->branchID);
        $userID=auth()->user()->id;
        $announcements=\Illuminate\Support\Facades\DB::select("SELECT * FROM announcement_histories WHERE uid=$userID and end_date>=CURRENT_DATE()");

        $watercard=$branch->waterCard()->get()->first();
        $cardPercent= $watercard? ($watercard->card_credit_temp/$watercard->card_credit)*100:0;
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

        $captainSchedule=DB::select("SELECT * FROM captain_schedules WHERE  branchID=$manager->branchID and DAY(date) = DAY(CURRENT_DATE()) and MONTH(date) = MONTH(CURRENT_DATE())  ORDER BY date DESC ");


        $context=[
            "user"=>$request->user(),
            "manager"=>$manager,
            "branch"=>$branch,
            "announcements"=>$announcements,
            "watercard"=>$watercard,
            "cardPercent"=>$cardPercent,
            "incomeWecoach"=>$incomeWecoach,
            "incomeWaves"=>$incomeWaves,
            "outcomeWecoach"=>$outcomeWecoach,
            "outcomeWaves"=>$outcomeWaves,
            "internsWecoach"=>$internsWecoach,
            "internsWaves"=>$internsWaves,
            "captainSchedule"=>$captainSchedule,

        ];

        return view("manager.profile",$context);
    }

    public function showUpdateForm(){
        $user=auth()->user();
        $manager=$user->managers()->where("uid",$user->id)->get()[0];

        $context=[
            "user"=>$user,
            "manager"=>$manager,
        ];
//        dd($context);
        return view("manager.update",$context);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'birthdate' => ['required'],
            'address' => ['required'],
            'whatsapp' => ['required'],
            "study_field"=>['required'],
            "current_employer"=>['required'],
            "previous_experience"=>['required'],
//            "personal_id"=>['required'],
//            "facility_receipt"=>['required'],
        ]);
    }

    protected function update(Request $data,$user)
    {
//        dd($data);
        $user->update([
            'name' => $data->input('name'),
            'email' =>  $data->input('email'),
            'birthdate'=>  $data->input("birthdate"),
            'address'=>  $data->input("address"),
            'whatsapp'=>  $data->input("whatsapp"),
        ]) ;

        $user->save();

        $uid=$user->id;
        $manager=Manager::where("uid",$uid)->get();
//        dd($manager);
        $managerData=[
            "study_field"=> $data->input("study_field"),
            "current_employer"=> $data->input("current_employer"),
            "previous_experience"=> $data->input("previous_experience"),
//            "profile_photo"=>$data["profile_photo"],
//            "personal_id"=>$data["personal_id"],
//            "facility_receipt"=>$data["facility_receipt"],
        ];
        if($data->file("profile_photo")){
            $file=$data->file("profile_photo");
            $photoname= $file->getClientOriginalName();

            $file->move(public_path("images/uploads/"),$photoname);
            $managerData["profile_photo"]=$photoname;
        }

        if($data->file("personal_id")){
            $file=$data->file("personal_id");
            $photoname= $file->getClientOriginalName();
            $file->move(public_path("images/uploads/"),$photoname);
            $managerData["personal_id"]=$photoname;
        }

        if($data->file("facility_receipt")){
            $file=$data->file("facility_receipt");
            $photoname= $file->getClientOriginalName();
            $file->move(public_path("images/uploads/"),$photoname);
            $managerData["facility_receipt"]=$photoname;
        }


        $manager[0]->update($managerData);
        $response= $manager[0]->save();

        return $response;
    }


    public function updateManager(Request $request) {
        $user=auth()->user();
        $this->validator($request->all())->validate();
        $response= $this->update($request,$user);

        return $response? back()->with("message","Profile Updated Successfully"):back()->with("error","An Error Occurred");
    }

    public function billing(){
        $manager=Manager::where("uid",auth()->user()->id)->get()->first() ;
        $branch=Branch::find($manager->branchID);


        $incomeWecoach=DB::select("SELECT SUM(value) as totalIncomes FROM incomes WHERE DAY(incomeDate) = DAY(CURRENT_DATE()) and branchID=$manager->branchID and academyID=1")[0];
        $incomeWaves=DB::select("SELECT SUM(value) as totalIncomes FROM incomes WHERE DAY(incomeDate) = DAY(CURRENT_DATE())  and branchID=$manager->branchID and academyID=2")[0];
        if($incomeWecoach->totalIncomes==null){
            $incomeWecoach->totalIncomes=0;
        }
        if($incomeWaves->totalIncomes==null){
            $incomeWaves->totalIncomes=0;
        }

        $outcomeWecoach=DB::select("SELECT SUM(value) as totalOutcome FROM outcomes WHERE DAY(outcomeDate) = DAY(CURRENT_DATE()) and branchID=$manager->branchID and academyID=1")[0];
        $outcomeWaves=DB::select("SELECT SUM(value) as totalOutcome FROM outcomes WHERE DAY(outcomeDate) = DAY(CURRENT_DATE()) and branchID=$manager->branchID and academyID=2")[0];
        if($outcomeWecoach->totalOutcome==null){
            $outcomeWecoach->totalOutcome=0;
        }
        if($outcomeWaves->totalOutcome==null){
            $outcomeWaves->totalOutcome=0;
        }

        $incomes=Income::latest()->get();
        $outcomes=Outcome::latest()->get();


        $context=[
            "branch"=>$branch,
            "academies"=>Academy::all(),
            "incomeWecoach"=>$incomeWecoach,
            "incomeWaves"=>$incomeWaves,
            "outcomeWecoach"=>$outcomeWecoach,
            "outcomeWaves"=>$outcomeWaves,
            "incomes"=>$incomes,
            "outcomes"=>$outcomes,
        ];

        return view("manager.billing.index",$context);
    }

}
