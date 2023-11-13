<?php

namespace App\Http\Controllers\admin\Manager;

use App\Http\Controllers\Controller;
use App\Models\admin\Admin;
use App\Models\Branch;
use App\Models\captain\Captain;
use App\Models\intern\Intern;
use App\Models\manager\Manager;
use App\Models\User;
use App\Traits\Manager\CreatesManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateController extends Controller
{
    use CreatesManager;

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'birthdate' => ['required'],
            'address' => ['required'],
            'whatsapp' => ['required'],
        ]);
    }

    protected function create(array $data)
    {
        $user= User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'birthdate'=> $data["birthdate"],
            'address'=> $data["address"],
            'whatsapp'=> $data["whatsapp"],
        ]);

        $uid=$user->id;
//        $managerData=[
//            "uid"=>$uid,
//            "study_field"=>$data["study_field"],
//            "current_employer"=>$data["current_employer"],
//            "previous_experience"=>$data["previous_experience"],
//            "profile_photo"=>$data["profile_photo"],
//            "personal_id"=>$data["personal_id"],
//            "facility_receipt"=>$data["facility_receipt"],
//        ];

        $manager= Manager::create(["uid"=>$uid,"branchID"=>$data["branchID"],"profile_status"=>"approved"]);

        return $manager;
    }

    public function showManagerForm(){
        $branches=Branch::all();
        $context=["branches"=>$branches];
        return view("admin.manager.add",$context);
    }



    public function emailAdd(Request $request){
        $email=$request->input("email");
        $branch=$request->input("branchID");
        $user= User::where("email",$email)->get()->first();

        if(!$user){
            return back()->with("error","This Email Doesn't Exist");
        }

        $admin=Admin::where("uid",$user->id)->get()->first();

        if($admin){
            return back()->with("error","This Email Is Not for a Captain");

        }

        $manager=Manager::where("uid",$user->id)->get()->first();

        if($manager){
            return back()->with("error","The Manager Already Exists");

        }

        $captain=Captain::where("uid",$user->id)->get()->first();

        if($captain){

            $captain->delete();
            $man=Manager::create([
                "uid"=>$user->id,
                "profile_status"=>"approved",
                "branchID"=>$branch
            ]);
            return back()->with("message","Manager Added Successfully");
        }

        $intern=Intern::where("uid",$user->id)->get()->first();

        if($intern){
            $intern->delete();
        }

        $man=Manager::create([
            "uid"=>$user->id,
            "profile_status"=>"approved",
            "branchID"=>$branch
        ]);
        return back()->with("message","Manager Added Successfully");
    }

}
