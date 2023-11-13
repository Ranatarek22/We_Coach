<?php

namespace App\Http\Controllers\wecoach\Intern;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Intern\AttendanceController;
use App\Http\Controllers\wecoach\Intern\Session\SessionsController;
use App\Http\Controllers\wecoach\PagesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


class InternRoutes extends Controller
{

    public static function routes(){

        Route::group(["middleware"=>["weauth","isIntern"]],function (){

            Route::get("/",[ProfileController::class,"index"])->name("weintern.profile");
            Route::get("edit",[ProfileController::class,"profileEdit"])->name("weintern.profile.editform");
            Route::post("edit/{user}",[ProfileController::class,"updateIntern"])->name("weintern.profile.edit");
            Route::get("levels",[ProfileController::class,"levels"])->name("weintern.profile.levels");

            Route::group(["prefix"=>"apply"],function (){

                Route::get("/",[SessionsController::class,"form"])->name("wecoach.apply");
                Route::post("/",[SessionsController::class,"reserve"])->name("wecoach.apply");

            });

            Route::get("attendance",[AttendanceController::class,"form"])->name("wecoach.attendance");
            Route::post("attendance",[AttendanceController::class,"record"])->name("wecoach.attendance");

            Route::get("terms-and-conditions",[ProfileController::class,"terms_and_conditions"])->name("wecoach.terms");

            Route::group(["prefix"=>"ajax"],function (){

                Route::get("branch-subs",[\App\Http\Controllers\admin\Package\CreateController::class,"getSubsOfBranch"])->name("jq.getBranchSubs");
                Route::get("branch-cap",[SessionsController::class,"getBranchCap"])->name("jq.getBranchCap");
                Route::get("subs-pack",[SessionsController::class,"getPackOfSubs"])->name("jq.getPackOfSubs");
                Route::get("subs-sched",[SessionsController::class,"getSchedOfSubs"])->name("jq.getSchedOfSubs");

                Route::get("pack-price",[SessionsController::class,"getPackPrice"])->name("jq.getPackPrice");
                Route::get("promocode",[SessionsController::class,"promocode"])->name("jq.promocode");

            });
        });

        Route::get("branch/{branch}",[PagesController::class,"branchSingle"])->name("wecoach.branch");
        Route::get("captain/{captain}",[PagesController::class,"captainSingle"])->name("wecoach.captain");



    }
}
