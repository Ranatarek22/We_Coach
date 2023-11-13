<?php
namespace App\Http\Middleware;

use App\Models\intern\Intern;
use Closure;
use Illuminate\Support\Facades\Auth;

class isWecoachIntern
{
    public function handle($request, Closure $next)
    {
        $user = \auth()->user();
        if ($user) {
            $intern=Intern::where("uid",$user->id)->get()->first();
            if($intern){
                switch ($intern->academyID) {
                    case '1':
                        return $next($request);
                    case '2':
                        return redirect(route("waves"));
                    // add more cases for each academy
                }
            }

        }

        return $next($request);
    }
}
