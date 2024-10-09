<?php
// Instagram API Erişim Tokeni
$accessToken = '1096662008567613|k1AyNfQflp-zJhOpN6Pjztd-8BY';
$pageId = '17841467621423006'; // Facebook Page ID

// Instagram API URL'si
$url = "https://graph.facebook.com/v16.0/{$pageId}/conversations?access_token={$accessToken}";

// cURL oturumu başlat
$ch = curl_init();

// cURL ayarları
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// API'den yanıt al
$response = curl_exec($ch);

// cURL oturumunu kapat
curl_close($ch);

// Yanıtı JSON formatında çöz
$messages = json_decode($response, true);

// Eğer mesajlar varsa, JSON formatında yanıt verelim
if (isset($messages)) {
    echo '<pre>' . json_encode($messages) . '</pre>';
} else {
    echo json_encode(['error' => 'No messages found or an error occurred.']);
}
