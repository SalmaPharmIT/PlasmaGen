<?php

namespace App\Http\Middleware;

use App\Models\AuditTrail;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuditTrailMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if this is a login or logout request
        $isLoginAttempt = $request->is('login') && $request->isMethod('post');
        $isLogoutAttempt = $request->is('logout') && $request->isMethod('post');
        
        // Get the response
        $response = $next($request);
        
        // Check for login success
        if ($isLoginAttempt && Auth::check()) {
            // Log successful login
            AuditTrail::log(
                'login',
                'Authentication',
                null,
                Auth::id(),
                [],
                [],
                'User logged in successfully'
            );
        }
        
        // Check for logout
        if ($isLogoutAttempt && !Auth::check()) {
            // Get the user ID before logout if available from session
            $userId = session('last_user_id', null);
            $userName = session('last_user_name', 'Unknown User');
            
            // Log logout
            AuditTrail::log(
                'logout',
                'Authentication',
                null,
                $userId,
                [],
                [],
                'User logged out'
            );
        }
        
        return $response;
    }
}
