<?php

namespace App\Http\Controllers\admin\Announcement;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementHistory;
use App\Models\Branch;
use App\Models\captain\Captain;
use App\Models\CaptainSchedule;
use App\Models\intern\Intern;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Psy\Util\Str;

class CreateController extends Controller
{

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'announcement' => ['required'],
            'type' => ['required'],
            'end_date' => ['required',"date"],
        ]);
    }

    protected function create(array $data){

        $users=$this->getUsers($data["branchID"],$data["userType"]);
        $announcement=new Announcement();
        $announcement->setMessage($data);
        foreach ($users as $u){
            $announcement->attach($u);
        }
        $announcement->notify();
    }

    public function form(){

        $branch=Branch::all();
        $context=[
            "branches"=>$branch
        ];
        return view("admin.announcement.add",$context);
    }




    public function createsAnnouncement(Request $request){
        $this->validator($request->all())->validate();
        $this->create($request->all());

        return back()->with("message","Announcement Created Successfully");
    }


    private function getUsers(array $branchID,array $userType) {
        $user=[];
        foreach ($userType as $utype) {
            foreach ($branchID as $bid) {
                if ($utype == "all") {


//                    get managers in branch
                    $branch = Branch::find($bid);
                    $managers = $branch->managers()->get();
                    foreach ($managers as $m) {
                        $user[] = $m->user()->first();
                    }

//                    get captains in branch
                    $capSessions = CaptainSchedule::all()->where("branchID", $bid);
                    foreach ($capSessions as $cs) {
                        $captain = User::find($cs->uid);
                        $user[] = $captain;
                    }

//                    get interns in branch
                    $interns = Intern::all()->where("branch", $bid);
                    foreach ($interns as $i) {
                        $user[] = $i->user()->first();
                    }

                }
                elseif ($utype == "manager") {
                    $branch = Branch::find($bid);
                    $managers = $branch->managers()->get();
                    foreach ($managers as $m) {
                        $user[] = $m->user()->first();
                    }
                }
                elseif ($utype == "captain") {
                    $capSessions = CaptainSchedule::all()->where("branchID", $bid);
                    foreach ($capSessions as $cs) {
                        $captain = User::find($cs->uid);
                        $user[] = $captain;
                    }

                }
                else {
                    $interns = Intern::all()->where("branch", $bid);
                    foreach ($interns as $i) {
                        $user[] = $i->user()->first();
                    }
                }
            }
        }

        return $user;

    }

    public function deleteAnnouncement(Request $request, AnnouncementHistory $announcement){
        $announcements = AnnouncementHistory::where("announcement",$announcement->announcement)
            ->get();
        foreach ($announcements as $ann) {

            $ann->delete();
        }
        return back()->with("message","Deleted Sunccessfully");
    }
}
