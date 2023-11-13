<?php

namespace App\Http\Controllers\Manager\Captain;

use App\Http\Controllers\Controller;
use App\Models\admin\Admin;
use App\Models\captain\Captain;
use App\Models\intern\Intern;
use App\Models\manager\Manager;
use App\Models\User;
use App\Traits\Captain\CreatesCaptain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateController extends Controller
{
    use CreatesCaptain;

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'birthdate' => ['required'],
            'address' => ['required'],
            'whatsapp' => ['required'],
        ]);
    }

    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['first_name'].' '.$data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'birthdate' => $data["birthdate"],
            'address' => $data["address"],
            'whatsapp' => $data["whatsapp"],
        ]);

        $uid = $user->id;

        $captain = Captain::create([
            "uid" => $uid,
            "profile_status"=>"pending",
            "captain_status"=>$data["captain_status"]
        ]);

        return $captain;
    }

    public function emailAdd(Request $request){
        $email=$request->input("email");
        $status=$request->input("captain_status");
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
            return back()->with("error","This Email Is Not for a Captain");

        }

        $captain=Captain::where("uid",$user->id)->get()->first();

        if($captain){
            return back()->with("error","The Captain Already Exists");

        }

        $intern=Intern::where("uid",$user->id)->get()->first();

        if($intern){
            $intern->delete();
        }

        $cap=Captain::create([
            "uid"=>$user->id,
            "profile_status"=>"pending",
            "captain_status"=>$status,
        ]);
        return back()->with("message","Captain Added And Waiting To Be Accepted");
    }

    public function showForm()
    {
        $captain=Captain::all()
            ->where("profile_status","approved")
            ->where("upgraded","false");
        return view("manager.captain.add",["captains"=>$captain]);
    }
}
