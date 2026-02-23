<?php
// services/User_Service/AuthController.php
session_start();
require_once '../../config/database.php';

// 生成 CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$db = (new Database())->getConnection();
$response = ['status' => 'error', 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';
    
    // CSRF 校验
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF token validation failed.");
    }

    // 验证码校验
    $user_captcha = strtoupper(trim($_POST['captcha']));
    if ($user_captcha !== $_SESSION['captcha']) {
        $response['message'] = 'error_captcha';
        echo json_encode($response);
        exit;
    }

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if ($action === 'register') {
        // 检查邮箱是否存在
        $stmt = $db->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        if ($stmt->rowCount() > 0) {
            $response['message'] = 'error_email_exists';
        } else {
            // 安全哈希密码 (Bcrypt)
            $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            
            $insert_stmt = $db->prepare("INSERT INTO users (email, password_hash, role) VALUES (:email, :pwd, 'student')");
            if ($insert_stmt->execute([':email' => $email, ':pwd' => $hashed_password])) {
                $response['status'] = 'success';
                $response['message'] = 'success_register';
            }
        }
    } elseif ($action === 'login') {
        $stmt = $db->prepare("SELECT id, password_hash, role, nickname FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        // 验证密码
        if ($user && password_verify($password, $user['password_hash'])) {
            // 重新生成 session id 防止会话固定攻击
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $response['status'] = 'success';
            $response['redirect'] = '/learning'; // 登录成功跳转
        } else {
            $response['message'] = 'error_auth';
        }
    }
    echo json_encode($response);
    exit;
}
?>