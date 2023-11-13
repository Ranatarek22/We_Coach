<?php

namespace App\Http\Controllers\Manager\Branch;

use App\Http\Controllers\Controller;
use App\Models\Branch;

class DeleteController extends Controller
{

    public function delete(Branch $branch){
//        dd($manager);
        $branch->delete();
        return redirect(route("manager.branches"))->with("message","Branch Deleted Successfully");
    }
}
