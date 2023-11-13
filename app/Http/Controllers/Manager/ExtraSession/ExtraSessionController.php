<?php

namespace App\Http\Controllers\Manager\ExtraSession;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\captain\Captain;
use App\Models\captain\ExtraSession;
use Illuminate\Http\Request;

class ExtraSessionController extends Controller
{
    public function form(){
        $captains=Captain::all();
        $context=[
          "captains"=>$captains,
          "branches"=>Branch::all()
        ];

        return view("manager.extrasession.index",$context);
    }

    public function add(Request $request){
        $resp=ExtraSession::create($request->all());

        return $resp?back()->with("message","Extra Session Added Successfully"):back()->with("error","An Error Occured");
    }
}
