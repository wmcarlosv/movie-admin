<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class CheckClient
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $secure_data = $request->route('secure_link');
        $data = explode("|", $secure_data);
        $user = User::where([
            ['client_id','=',$data[0]],
            ['status','=','A']
        ])->get();

        if($user->count() == 0){
            abort('404','Access Denied');
        }

        return $next($request);
    }
}
