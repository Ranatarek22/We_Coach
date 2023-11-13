<?php

namespace App\Http\Controllers\admin\Intern;

use App\Http\Controllers\Controller;
use App\Models\AnnouncementHistory;
use App\Models\Branch;
use App\Models\captain\Captain;
use App\Models\captain\ExtraSession;
use App\Models\CaptainSchedule;
use App\Models\intern\Intern;
use App\Models\intern\InternSessionHistory;
use App\Models\intern\SessionMeta;
use App\Models\PackageType;
use App\Models\SubscriptionType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ViewController extends Controller
{
    public function all() {

        $interns=Intern::latest()->get();
        $internsWecoach=Intern::all()->where("academyID","1");
        $internsWaves=Intern::all()->where("academyID","2");
        $branches=Branch::all();
        return view("admin.intern.all",["interns"=>$interns,"branches"=>$branches,"internsWecoach"=>count($internsWecoach),"internsWaves"=>count($internsWaves)]);

    }

    public function single(Request $request, Intern $intern){
        $user=User::find($intern->uid);
        $subscription=SubscriptionType::find($intern->subType);
        $package=PackageType::find($intern->group_type);
        $branch=Branch::find($intern->branch);
        $sessions_meta=DB::select("SELECT * FROM session_metas WHERE uid=$user->id and MONTH(month)=MONTH(CURRENT_DATE()) and YEAR(month)=YEAR(CURRENT_DATE())");
//        $sessions_meta=count($sessions_meta)==0?null:$sessions_meta;
        $sessionTemp=InternSessionHistory::all()->where("uid",$user->id);
        $sessions=[];
//        dd($sessions_meta);
        foreach ($sessionTemp as $item) {
            $id=$item->sessionID;
            $session=CaptainSchedule::find($id);
            $date=date("Y-m");
            $sessionDate=$session->date;
            $sessionDate= explode("-",$sessionDate);
            $sessionDate=$sessionDate[0].'-'.$sessionDate[1];
            if($sessionDate==$date){
                $sessions[]=$item;
            }
        }

        $context=[
            "user"=>$user,
            "intern"=>$intern,
            "subscription"=>$subscription,
            "package"=>$package,
            "branch"=>$branch,
            "sessions_meta"=>$sessions_meta,
            "sessions"=>$sessions,
            "all_sessions"=>$sessionTemp
        ];

        return view("admin.intern.single",$context);
    }

    public function session_meta_form(Request $request,SessionMeta $meta){
        return view("admin.intern.meta",["meta"=>$meta,"intern"=>$request->input("intern")]);
    }

    public function session_meta_update(Request $request,SessionMeta $meta){

        if($request->input("money_paid")==$request->input("money_to_pay")){
            $meta->update([
                "month"=>$request->input("month"),
                "pay_method"=>$request->input("pay_method"),
                "money_paid"=>$request->input("money_paid"),
                "money_to_pay"=>$request->input("money_to_pay"),
                "paid"=>$request->input("true"),
            ]);
            return back()->with("message","Updated Successfully");
        }
        else{
            $meta->update([
                "month"=>$request->input("month"),
                "pay_method"=>$request->input("pay_method"),
                "money_paid"=>$request->input("money_paid"),
                "money_to_pay"=>$request->input("money_to_pay"),
            ]);
            return back()->with("message","Updated Successfully");
        }
    }


    public function editMetaSub(Request $request){
        $metasub=SessionMeta::find($request->input("id"));
        if($request->input("money_paid")==$request->input("money_to_pay")){
            $metasub->update([
                "month"=>$request->input("month"),
                "pay_method"=>$request->input("pay_method"),
                "money_paid"=>$request->input("money_paid"),
                "money_to_pay"=>$request->input("money_to_pay"),
                "paid"=>$request->input("true"),
            ]);
            return redirect(route("interns"))->with("message","Updated Successfully");
        }
        else{
            $metasub->update([
                "month"=>$request->input("month"),
                "pay_method"=>$request->input("pay_method"),
                "money_paid"=>$request->input("money_paid"),
                "money_to_pay"=>$request->input("money_to_pay"),
            ]);
            return redirect(route("interns"))->with("message","Updated Successfully");
        }
    }

}
