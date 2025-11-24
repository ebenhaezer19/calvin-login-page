<?php
// Konfigurasi bot
$BOT_TOKEN = '8394213465:AAFcQ5Cmr5j0FLC5WqfHaGuezRl1IPOERuo';
$CHAT_ID = '1127003304';

// Pesan test
$message = "✅ Bot berhasil terhubung!\n";
$message .= "Waktu: " . date('Y-m-d H:i:s') . "\n";
$message .= "IP Server: " . ($_SERVER['SERVER_ADDR'] ?? 'localhost');

// Kirim pesan
$url = "https://api.telegram.org/bot$BOT_TOKEN/sendMessage";
$data = [
    'chat_id' => $CHAT_ID,
    'text' => $message,
    'parse_mode' => 'HTML'
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    ]
];

$context  = stream_context_create($options);
$result = @file_get_contents($url, false, $context);

if ($result === FALSE) {
    echo "❌ Gagal mengirim pesan. Error: " . error_get_last()['message'];
} else {
    $response = json_decode($result, true);
    if ($response['ok']) {
        echo "✅ Pesan terkirim ke bot Telegram!";
    } else {
        echo "❌ Error dari Telegram: " . ($response['description'] ?? 'Unknown error');
    }
}