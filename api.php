<?php
// api.php - สำหรับติดต่อ Supabase API
$api_url = "https://reedfppffweswquzbeet.supabase.co";  // URL ของ Supabase API
$api_key = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InJlZWRmcHBmZndlc3dxdXpiZWV0Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3MzA5MTIyNzMsImV4cCI6MjA0NjQ4ODI3M30.vDCcx46g0EoMZ2Kc1sS5c1oqb5JPzxp2gGDvFseNIg8"; // Supabase API Key

function getUser($user_id) {
    global $api_url, $api_key;

    $headers = [
        "Authorization: Bearer $api_key",
        "Content-Type: application/json",
        "Accept: application/json"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$api_url?user_id=eq.$user_id");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}
?>
