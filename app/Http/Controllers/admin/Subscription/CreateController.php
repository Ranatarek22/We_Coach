<?php

namespace App\Http\Controllers\admin\Subscription;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionSchedule;
use App\Models\SubscriptionType;
use App\Traits\Subscription\CreatesSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreateController extends Controller
{
    use CreatesSubscription;

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
            'branchID' => ['required'],
        ]);
    }

    protected function create(array $data)
    {
        $subscription = SubscriptionType::create($data);

        return $subscription;
    }


    public function subsSchedule(Request $request){
        $data=$request->all();
        $flag=SubscriptionSchedule::create($data);
        return $flag?back()->with("message","Subscription Time Added Successfully"):back()->with("error","An Error Occured");
    }

}
