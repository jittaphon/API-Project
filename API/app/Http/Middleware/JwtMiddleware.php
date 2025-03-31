<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class JwtMiddleware // middleware สำหรับ JWT
{
    public function handle($request, Closure $next)
    {
        try {

            $token = JWTAuth::parseToken(); // ดึง Token
            var_dump($token); // แสดง Token
            exit; // หยุดการทำงานที่นี่เพื่อดู Token

            $user = $token->authenticate(); // ตรวจสอบ Token และดึงข้อมูลผู้ใช้


        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        } catch (TokenBlacklistedException $e) {
            return response()->json(['error' => 'Token is blacklisted'], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token not provided'], 401);
        } catch (Exception $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
