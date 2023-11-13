<?php

namespace App\Http\Controllers\admin\Subscription;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\SubscriptionSchedule;
use App\Models\SubscriptionType;
use Illuminate\Http\Request;

class DeleteController extends Controller
{

    public function delete(SubscriptionType $subs){
//        dd($manager);
        $subs->delete();
        return back();
    }

    public function deleteSubsSchedule(Request $request,SubscriptionSchedule $subsched){
        $subsched->delete();
        return back()->with("message","Subscription Time Deleted Successfully");
    }
}
