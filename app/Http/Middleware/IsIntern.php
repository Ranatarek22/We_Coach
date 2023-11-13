<?php

namespace App\Http\Middleware;

use App\Models\admin\Admin;
use App\Models\captain\Captain;
use App\Models\intern\Intern;
use App\Models\manager\Manager;
use Closure;
use Illuminate\Http\Request;

class IsIntern
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
//        $admin =  auth()->user()->admins()->where("uid",auth()->user()->id)->get()[0];
        $intern =  Intern::all()->where("uid",auth()->user()->id);
        if(count($intern) !=0)
            return $next($request);

        $admin=Admin::all()->where("uid",auth()->user()->id);
        if(count($admin) !=0)
            return redirect(route("dashboard"));

        $manager=Manager::all()->where("uid",auth()->user()->id);
        if(count($manager) !=0)
            return redirect(route("manager.profile"));


        $captain=Captain::all()->where("uid",auth()->user()->id);
        if(count($captain) !=0)
            return redirect(route("captain"));

    }
}
