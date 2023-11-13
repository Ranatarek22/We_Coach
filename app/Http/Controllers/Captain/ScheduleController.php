<?php

namespace App\Http\Controllers\Captain;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CaptainSchedule;
use App\Models\Income;
use App\Models\SubscriptionSchedule;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    public function form(){
        $user=auth()->user();
        $branches=Branch::all();
        $schedules=$user->schedules()->latest()->get();
        $context=[
            "branches"=>$branches,
            "schedules"=>$schedules,
        ];
        return view("captain.schedule.index",$context);
    }

    public function addSchedule(Request $request){

        // Get the current day and total days in the current month
        $dayOfMonth = date('j');
        $totalDaysInMonth = date('t');

        // Check if we are in the last 7 days of the current month
        if ($dayOfMonth < ($totalDaysInMonth - 7)) {
            // Do something if we are in the last 7 days of the current month
            return back()->with("error","Cannot add schedule before the last 7 days of the month");
        }



        $hours=$request->input("hours");
        if(count($hours)!=2){
            return back()->with("error","Please Choose Time in This Form ( Start Time - End Time )");
        }
        $hours= $this->getHours($hours[0],$hours[1]);

        array_pop($hours);
        $data=array(
            "day"=>$request->input("day"),
            "branchID"=>$request->input("branchID"),
            "hours"=>$hours,
            "uid"=>auth()->user()->id
        );
        $this->validator($data)->validate();
        $this->create($data);
        return back()->with("message","Schedules Added Successfully");
    }

    public function deleteSchedule(Request $request,CaptainSchedule $captainSchedule){
        $captainSchedule->delete();
        return back()->with("message","Schedule Deleted Successfully");
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'day' => 'required',
            'branchID' => 'required',
        ]);
    }

    protected function create(array $data)
    {
        $days=$data["day"];
        $hours=$data["hours"];
//        dd($days);
        $dates=$this->getDayDates($days);
//        dd($dates);
        foreach ($dates as $date){
            foreach ($hours as $hour){
                CaptainSchedule::create(["uid"=>$data["uid"],"branchID"=>$data["branchID"],"date"=>$date,"start_time"=>$hour]);
            }
        }


    }

//  Helping functions
    private function getDayDates($days){

// Set the default timezone to your desired timezone
        date_default_timezone_set('Africa/Cairo');

        // Get the current month and year
        $month = date('m');
        $year = date('Y');
        $month++;
        // If the next month is January of the following year, update the year accordingly
        if ($month > 12) {
            $month = 1;
            $year++;
        }


// Get the current date and time
        $currentDateTime = new DateTime('now');

// Create an empty array to store the dates
        $dates = array();

// Loop through each day of the current month
        for ($i = 1; $i <= 31; $i++) {
            // Create a DateTime object for the current day
            $day = new DateTime("$year-$month-$i");

//                    dd($d);
            if (strtolower($day->format('l')) == $days) {
                // Check if the day is after the current date and time
                if ($day > $currentDateTime) {
                    // Add the date to the array
                    $dates[] = $day->format('Y-m-d');
                }

            }
            // Check if the day is in the $days array


        }

        return $dates;
    }

    function getHours($start, $end) {
        $startHour = intval(substr($start, 0, 2));
        $endHour = intval(substr($end, 0, 2));
        $startMinute = intval(substr($start, 3, 2));
        $endMinute = intval(substr($end, 3, 2));
        $startSuffix = substr($start, -2);
        $endSuffix = substr($end, -2);
        $hours = array();

        if ($startSuffix === 'PM') {

            if($startHour!=12){
                $startHour += 12;

            }
        }

        if ($endSuffix === 'PM') {
            if($endHour!=12){
                $endHour += 12;

            }
        }

        for ($i = $startHour; $i <= $endHour; $i++) {
            $suffix = 'AM';
            $minute = $startMinute;

            if ($i >= 12) {
                $suffix = 'PM';
            }

            if ($i > 12) {
                $formattedHour = sprintf("%02d", $i - 12);
            } else if ($i === 12) {
                $formattedHour = sprintf("%02d", $i);
            } else {
                $formattedHour = sprintf("%02d", $i);
            }

            if ($i === $startHour) {
                $minute = $startMinute;
            } else if ($i === $endHour) {
                $minute = $endMinute;
            }
            if($minute=="0"){
                $minute="00";
            }
            $hours[] = "{$formattedHour}:{$minute} {$suffix}";

//            if ($i === 12 && $startSuffix === 'AM') {
//                break;
//            }
        }

        return $hours;
    }



//    Ajax

    public function getSubsDaysOfBranch(Request $request){
        $branchID=$request->input("branchID");
        $day=$request->input("day");
        $subsSched=SubscriptionSchedule::all()->where("branchID",$branchID)->where("day",$day);
        $hours=[];
//        $tst=[];
        foreach ($subsSched as $s){
            $tmp=$this->getHours($s->start_time,$s->end_time);
//            $tst[]=$s->start_time;
//            $tst[]=$s->end_time;
            foreach ($tmp as $t){
                $hours[]=$t;
            }

        }

        $uniqueHours = array_unique($hours);

// Convert the hours to 24-hour format and sort the array
        usort($uniqueHours, function($a, $b) {
            $aTime = date('H:i', strtotime($a));
            $bTime = date('H:i', strtotime($b));
            return $aTime <=> $bTime;
        });

        return response()->json($uniqueHours);

    }
}
