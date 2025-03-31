<?php

use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon; // เพิ่มบรรทัดนี้
/** @var \Laravel\Lumen\Routing\Router $router */
$router->options('/{any:.*}', function () {
    $response = response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');

    // ตั้งค่า Cache-Control ให้ไม่เก็บข้อมูลใน Cache
    $response->headers->set('Cache-Control', 'no-store');

    return $response;
});

$router->get('/api/get-hospitals', 'NotifyController@getHospitalData');


$router->get('/secure-data', ['middleware' => 'jwt.auth', function () { // ถ้ามัน ผ่าน middleware jwt.auth ก็จะไปที่ function นี้
    // ตรวจสอบ Token และดึงข้อมูลผู้ใช้
    return response()->json([
        'message' => 'This is a secured API response.',
        'data' => [
            'name' => 'Open Data Example',
            'info' => 'This data is protected by JWT.'
        ]
    ]);
}]);

$router->get('/generate-token', function () {
    // กำหนด payload ในแบบอาร์เรย์ธรรมดา
    $payload = [
        'role' => 'api_user',
        'exp' => time() + (7 * 24 * 60 * 60) // 7 วันจากปัจจุบัน
    ];

    // เข้ารหัส payload เพื่อสร้าง token
    $token = JWTAuth::encode($payload);

    return response()->json(['token' => $token]);
});
