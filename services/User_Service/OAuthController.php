<?php
// services/User_Service/OAuthController.php
session_start();
require_once '../../config/database.php';
require_once '../../config/oauth_config.php'; // 存放各平台 CLIENT_ID 和 SECRET

$provider = $_GET['provider'] ?? '';
$action = $_GET['action'] ?? 'redirect'; // redirect 或 callback

if ($action === 'redirect') {
    // 构造各个平台的授权跳转 URL
    switch ($provider) {
        case 'google':
            $url = "https://accounts.google.com/o/oauth2/v2/auth?client_id=".GOOGLE_CLIENT_ID."&redirect_uri=".urlencode(GOOGLE_REDIRECT_URI)."&response_type=code&scope=email%20profile";
            break;
        case 'wechat':
            // 微信开放平台网页扫码登录
            $url = "https://open.weixin.qq.com/connect/qrconnect?appid=".WECHAT_APP_ID."&redirect_uri=".urlencode(WECHAT_REDIRECT_URI)."&response_type=code&scope=snsapi_login&state=STATE#wechat_redirect";
            break;
        case 'facebook':
            $url = "https://www.facebook.com/v12.0/dialog/oauth?client_id=".FB_CLIENT_ID."&redirect_uri=".urlencode(FB_REDIRECT_URI)."&scope=email";
            break;
        case 'twitter':
            // Twitter OAuth 2.0 PKCE 流程
            $url = "https://twitter.com/i/oauth2/authorize?response_type=code&client_id=".TWITTER_CLIENT_ID."&redirect_uri=".urlencode(TWITTER_REDIRECT_URI)."&scope=tweet.read%20users.read&state=state&code_challenge=challenge&code_challenge_method=plain";
            break;
        default:
            die("Invalid Provider");
    }
    header("Location: " . $url);
    exit;
} elseif ($action === 'callback') {
    // 收到授权码 $code 后，使用 cURL 向对应平台请求 Access Token，并获取用户信息
    $code = $_GET['code'] ?? '';
    if (!$code) die("Authorization Failed.");

    $db = (new Database())->getConnection();
    $oauth_id = ''; 
    $db_field = '';
    $user_email = '';
    
    // 伪代码：解析不同平台的 Token 和 UserInfo (生产环境需使用官方SDK或编写完整CURL请求)
    if ($provider === 'google') {
        // ...执行 cURL 请求获取 Google User Info...
        $oauth_id = 'google_123456789'; // 模拟获取到的 Google ID
        $db_field = 'google_id';
        $user_email = 'user@gmail.com';
    } elseif ($provider === 'wechat') {
        $oauth_id = 'wechat_unionid_abcdefg'; // 模拟 UnionID
        $db_field = 'wechat_unionid';
    } // ... facebook, twitter 省略 ...

    // 数据库层：检查该第三方 ID 是否已绑定
    $stmt = $db->prepare("SELECT id, role FROM users WHERE {$db_field} = :oauth_id LIMIT 1");
    $stmt->execute([':oauth_id' => $oauth_id]);
    $user = $stmt->fetch();

    if ($user) {
        // 已存在，直接登录
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
    } else {
        // 不存在，注册新用户（静默注册）
        $email_to_insert = $user_email ?: "{$provider}_{$oauth_id}@placeholder.cscabridge.com"; // 如果平台不提供邮箱，使用占位邮箱
        $insert_stmt = $db->prepare("INSERT INTO users (email, {$db_field}, role) VALUES (:email, :oauth_id, 'student')");
        $insert_stmt->execute([':email' => $email_to_insert, ':oauth_id' => $oauth_id]);
        
        session_regenerate_id(true);
        $_SESSION['user_id'] = $db->lastInsertId();
        $_SESSION['role'] = 'student';
    }
    
    // 登录/注册完成后统一跳转回个人中心
    header("Location: /learning");
    exit;
}
?>