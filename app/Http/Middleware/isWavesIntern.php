<?php
namespace App\Http\Middleware;

use App\Models\intern\Intern;
use Closure;
use Illuminate\Support\Facades\Auth;

class isWavesIntern
{
    public function handle($request, Closure $next)
    {
        $user = \auth()->user();
        if ($user) {
            $intern=Intern::where("uid",$user->id)->get()->first();
            if($intern){
                switch ($intern->academyID) {
                    case '1':
                        return redirect(route("wecoach"));
                    case '2':
                        return $next($request);
                    // add more cases for each academy
                }
            }



        }

        return $next($request);
    }
}
