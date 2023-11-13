<?php

namespace App\Http\Controllers\waves\Intern;

use App\Http\Controllers\Controller;
use App\Models\AnnouncementHistory;
use App\Models\Branch;
use App\Models\captain\Captain;
use App\Models\captain\Rating;
use App\Models\CaptainSchedule;
use App\Models\intern\Intern;
use App\Models\intern\InternSessionHistory;
use App\Models\intern\SessionMeta;
use App\Models\PackageType;
use App\Models\SubscriptionType;
use App\Models\User;
use App\Traits\Intern\UpdatesIntern;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    use UpdatesIntern;

    public function index(){
        $user=auth()->user();
        $intern=$user->interns()->get()->first();
        $subscription=SubscriptionType::find($intern->subType);
        $package=PackageType::find($intern->group_type);
        $branch=Branch::find($intern->branch);
        $sessions_meta_first=SessionMeta::all()->where("uid",$user->id);
        $sessions_meta=DB::select("SELECT * FROM session_metas WHERE uid=$user->id and MONTH(month)=MONTH(CURRENT_DATE()) and YEAR(month)=YEAR(CURRENT_DATE())");
        $sessions_meta=count($sessions_meta)==0?null:$sessions_meta[0];
        $sessionTemp=InternSessionHistory::all()->where("uid",$user->id);
        $sessions=[];
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

        $announcements=AnnouncementHistory::all()->where("uid",$user->id);
        $context=[
            "user"=>$user,
            "intern"=>$intern,
            "subscription"=>$subscription,
            "package"=>$package,
            "branch"=>$branch,
            "sessions_meta"=>$sessions_meta,
            "sessions"=>$sessions,
            "announcements"=>$announcements,
        ];

        if(count($sessions_meta_first)==0){
            return view("waves.intern.notreg",$context);
        }

        return view("waves.intern.profile",$context);
    }

    public function profileEdit(){
        $user=auth()->user();
        $captain=Intern::where("uid",$user->id)->get()->first();

        $context=[
            "user"=>$user,
            "captain"=>$captain,
        ];

        return view("waves.intern.edit",$context);
    }


    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'birthdate' => ['required'],
            'address' => ['required'],
            'whatsapp' => ['required'],
        ]);
    }

    protected function update(Request $data,User $user)
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
        $captain=Intern::all()->where("uid",$uid)->first();
//        dd($manager);

        $captainData=[
        ];

        if($data->file("profile_photo")){
            $file=$data->file("profile_photo");
            $photoname= $file->getClientOriginalName();

            $file->move(public_path("images/uploads/"),$photoname);
            $captainData["profile_photo"]=$photoname;
        }



        $captain->update($captainData);
        $response= $captain->save();

        return $response;
    }


    public function terms_and_conditions(){
        return view("waves.apply.termsandcond");
    }


    public function levels(){

        return view("waves.intern.levels");
    }

}
