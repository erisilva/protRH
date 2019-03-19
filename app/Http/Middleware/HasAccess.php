<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class HasAccess
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
        /* continua se o usuário logado não estiver bloqueado */
        if(Auth::user()->hasAccess()) {
            return $next($request);    
        }

        /*  aborta e chama a view error.403 */
        abort(403, 'Operador com acesso bloqueado.');
    }
}
