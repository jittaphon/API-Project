<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class NotifyController extends BaseController
{
    // ดึงข้อมูลจากฐานข้อมูลแล้วแจ้งเตือนผ่าน LINE
    public function getHospitalData()
    {
        try {
            // ดึงข้อมูลทั้งหมดจาก tb_hospital
            $hospitals = DB::table('tb_hospital')->get();

            if ($hospitals->isEmpty()) {
                return response()->json(['status' => 'No data found']);
            }

            // สร้างข้อความแจ้งเตือน
            $message = "📢 รายชื่อโรงพยาบาลล่าสุด:\n";
            foreach ($hospitals as $hospital) {
                $message .= "🏥 " . $hospital->hospital_name . " | Beds: " . $hospital->number_of_beds . " | People: " . $hospital->number_of_people . "\n\n";  // เพิ่มบรรทัดว่างระหว่างรายการ
            }

            // ส่ง LINE แจ้งเตือน

            $this->sendLineMessage($message);

            return response()->json([
                'status' => 'success',
                'message' => 'Notification sent!',
                'data' => $hospitals
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch data: ' . $e->getMessage()
            ], 500);
        }
    }
    // ฟังก์ชันส่ง LINE Message
    private function sendLineMessage($message)
    {
        $accessToken = "UsPDHIN4bsfHDGP0alaw9hOXNjeCAgNKzo7LX5DHB931nMagIklWrj8EK2sjkqeVVDdAbtRIJlaTbH9HmPFnKWnr9B15//5BuJYHqUaLcOeEi+vXK4qb2k9bP/USOXirQ+P81Eh0StRHs0/lcTNUpQdB04t89/1O/w1cDnyilFU="; // ใส่ Access Token ของคุณ
        $url = "https://api.line.me/v2/bot/message/broadcast";

        $postData = [
            "messages" => [
                ["type" => "text", "text" => $message]
            ]
        ];



        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer " . $accessToken
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true); // กำหนดเป็นเมธอด POST
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // กำหนด Header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // กำหนดให้ส่งค่ากลับ
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData)); // กำหนดข้อมูลที่ต้องการส่ง Playload
        $result = curl_exec($ch); // ส่งข้อมูล Run
        curl_close($ch); // ปิดการเชื่อมต่อ // มันเข้ามาใน line แล้ว

        return $result; // ส่งค่ากลับ
    }
}
