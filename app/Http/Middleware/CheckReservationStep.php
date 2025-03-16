<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckReservationStep
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $step): Response
    {
        $currentStep = session('reservation.step', 1);
    
        if ($currentStep < $step) {
            return redirect()->route('reservation.step' . $currentStep);
        }
    
        return $next($request);
    }
}
