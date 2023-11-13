<?php

namespace App\Http\Controllers\Manager\Branch;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Traits\Branch\UpdatesBranch;
use Illuminate\Support\Facades\Validator;

class UpdateController extends Controller
{
    use UpdatesBranch;

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
            'address' => ['required', 'string', 'max:255'],

        ]);
    }

    protected function update(\Illuminate\Http\Request $request,Branch $branch)
    {
//        dd($data);
        $data=[
            "name"=>$request->input("name"),
            "address"=>$request->input("address"),
            "location"=>$request->input("location"),
        ];
        $data["days"]=serialize($request->input("days"));

        if($request->file("branch_photo")){
            $file=$request->file("branch_photo");
            $photoname= $file->getClientOriginalName();

            $file->move(public_path("images/uploads/"),$photoname);
            $data["branch_photo"]=$photoname;
        }

        $branch->update($data) ;

        $branch->save();

        $response= $branch->save();

        return $response;
    }

    public function changeStatus(Branch $branch){
        if ($branch->branch_status=="activated"){
            $branch->update(["branch_status"=>"deactivated"]);
            return back()->with("message","Branch Deactivated successfully");
        }
        else {
            $branch->update(["branch_status"=>"activated"]);
            return back()->with("message","Branch Activated successfully");
        }
    }


}
