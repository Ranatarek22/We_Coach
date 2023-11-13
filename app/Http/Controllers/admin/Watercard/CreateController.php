<?php

namespace App\Http\Controllers\admin\Watercard;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\WaterCard;
use App\Traits\WaterCard\CreatesWaterCard;
use Illuminate\Support\Facades\Validator;

class CreateController extends Controller
{
    use CreatesWaterCard;

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'branchID' => ['required'],
            'card_credit' => ['required'],
            'price' => ['required'],
            'point_per_person' => ['required'],

        ]);
    }


    protected function creates(array $data){
        $data["card_credit_temp"]=$data["card_credit"];
        $branch=Branch::find($data["branchID"]);
        $watercard=$branch->waterCard()->get()->first();
        if($watercard){
            $watercard->update(["card_credit"=>$data["card_credit"],"card_credit_temp"=>$data["card_credit"],"price"=>$data["price"],"point_per_person"=>$data["point_per_person"]]);
        }
        else {
            $watercard=WaterCard::create($data);
        }

        return $watercard;
    }

}
