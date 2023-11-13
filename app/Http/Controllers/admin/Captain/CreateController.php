<?php

namespace App\Http\Controllers\admin\Captain;

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
            'money_per_hour' => ['required'],
        ]);
    }

    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'birthdate' => $data["birthdate"],
            'address' => $data["address"],
            'whatsapp' => $data["whatsapp"],
        ]);

        $uid = $user->id;

        $captain = Captain::create(["uid" => $uid,"profile_status"=>"approved","money_per_hour"=>$data["money_per_hour"]]);

        return $captain;
    }



    public function emailAdd(Request $request){
        $email=$request->input("email");
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
            "profile_status"=>"approved",
        ]);
        return back()->with("message","Captain Added Successfully");
    }


    public function showForm(){

        return view("admin.captain.add");
    }

    public function add(Request $request) {
        $this->validator($request->all())->validate();
        $response= $this->create($request->all());

        return $response? back()->with("message","Captain Added Successfully"):back()->with("error","An Error occurred");
    }

}
