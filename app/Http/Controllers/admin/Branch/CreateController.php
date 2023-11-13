<?php

namespace App\Http\Controllers\admin\Branch;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Traits\Branch\CreatesBranch;
use Illuminate\Support\Facades\Validator;

class CreateController extends Controller
{
    use CreatesBranch;

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
            'days' => ['required',"array"],
        ]);
    }

    protected function create(\Illuminate\Http\Request $request)
    {
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

        $branch = Branch::create($data);

        return $branch;
    }
}
