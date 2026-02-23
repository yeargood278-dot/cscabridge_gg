<?php
// public/auth/login.php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 语言切换逻辑
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = in_array($_GET['lang'], ['zh', 'en']) ? $_GET['lang'] : 'zh';
}
$lang = $_SESSION['lang'] ?? 'zh';
$i18n = include('../../config/i18n_auth.php');
$t = $i18n[$lang];

// 判断是登录还是注册视图
$view = $_GET['view'] ?? 'login';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $view == 'login' ? $t['login_title'] : $t['register_title']; ?> | CSCABridge</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #1A56DB; --bg: #F3F4F6; --text: #374151; --white: #fff; --border: #D1D5DB; }
        * { box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: var(--bg); display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .auth-container { background: var(--white); width: 100%; max-width: 420px; padding: 2.5rem; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .auth-header { text-align: center; margin-bottom: 2rem; }
        .auth-header h2 { color: var(--primary); margin-bottom: 0.5rem; }
        .lang-switch { position: absolute; top: 20px; right: 20px; }
        .lang-switch a { text-decoration: none; color: var(--text); background: #e5e7eb; padding: 5px 10px; border-radius: 4px; font-size: 0.85rem; }
        
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; color: var(--text); font-size: 0.9rem; font-weight: 500; }
        .form-control { width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 6px; font-size: 1rem; }
        .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(26,86,219,0.1); }
        
        .captcha-wrapper { display: flex; gap: 10px; }
        .captcha-img { border-radius: 6px; cursor: pointer; border: 1px solid var(--border); }
        
        .btn-submit { width: 100%; background: var(--primary); color: var(--white); padding: 0.8rem; border: none; border-radius: 6px; font-size: 1rem; cursor: pointer; font-weight: bold; transition: 0.3s; }
        .btn-submit:hover { background: #1e40af; }
        
        .divider { display: flex; align-items: center; margin: 1.5rem 0; color: #9ca3af; font-size: 0.85rem; }
        .divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid var(--border); }
        .divider span { padding: 0 10px; }
        
        .oauth-buttons { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .btn-oauth { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 0.6rem; border: 1px solid var(--border); border-radius: 6px; background: var(--white); cursor: pointer; transition: 0.2s; color: var(--text); text-decoration: none; font-size: 0.9rem;}
        .btn-oauth:hover { background: #f9fafb; }
        .icon-google { color: #DB4437; }
        .icon-wechat { color: #07C160; }
        .icon-facebook { color: #1877F2; }
        .icon-twitter { color: #1DA1F2; }
        
        .toggle-view { text-align: center; margin-top: 1.5rem; font-size: 0.9rem; }
        .toggle-view a { color: var(--primary); text-decoration: none; font-weight: 600; }
        
        .alert { padding: 10px; border-radius: 6px; margin-bottom: 15px; display: none; font-size: 0.9rem;}
        .alert-error { background: #FEE2E2; color: #B91C1C; border: 1px solid #F87171; }
        .alert-success { background: #D1FAE5; color: #065F46; border: 1px solid #34D399; }
    </style>
</head>
<body>

    <div class="lang-switch">
        <a href="?lang=<?php echo $lang == 'zh' ? 'en' : 'zh'; ?>&view=<?php echo $view; ?>">
            <?php echo $lang == 'zh' ? 'English' : '中文'; ?>
        </a>
    </div>

    <div class="auth-container">
        <div class="auth-header">
            <h2>CSCABridge</h2>
            <p><?php echo $view == 'login' ? $t['login_title'] : $t['register_title']; ?></p>
        </div>

        <div id="alertBox" class="alert"></div>

        <form id="authForm" onsubmit="submitForm(event)">
            <input type="hidden" name="action" value="<?php echo $view; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="form-group">
                <label><?php echo $t['email']; ?></label>
                <input type="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label><?php echo $t['password']; ?></label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <?php if ($view == 'register'): ?>
            <div class="form-group">
                <label><?php echo $t['confirm_password']; ?></label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label><?php echo $t['captcha']; ?></label>
                <div class="captcha-wrapper">
                    <input type="text" name="captcha" class="form-control" required maxlength="4" autocomplete="off">
                    <img src="captcha.php" class="captcha-img" id="captchaImage" onclick="this.src='captcha.php?'+Math.random()" title="点击刷新">
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <?php echo $view == 'login' ? $t['btn_login'] : $t['btn_register']; ?>
            </button>
        </form>

        <div class="divider"><span><?php echo $t['or_login_with']; ?></span></div>

        <div class="oauth-buttons">
            <a href="../../services/User_Service/OAuthController.php?provider=google&action=redirect" class="btn-oauth">
                <i class="fa-brands fa-google icon-google"></i> Google
            </a>
            <a href="../../services/User_Service/OAuthController.php?provider=wechat&action=redirect" class="btn-oauth">
                <i class="fa-brands fa-weixin icon-wechat"></i> WeChat
            </a>
            <a href="../../services/User_Service/OAuthController.php?provider=facebook&action=redirect" class="btn-oauth">
                <i class="fa-brands fa-facebook icon-facebook"></i> Facebook
            </a>
            <a href="../../services/User_Service/OAuthController.php?provider=twitter&action=redirect" class="btn-oauth">
                <i class="fa-brands fa-twitter icon-twitter"></i> Twitter
            </a>
        </div>

        <div class="toggle-view">
            <?php if ($view == 'login'): ?>
                <?php echo $t['no_account']; ?> <a href="?view=register"><?php echo $t['btn_register']; ?></a>
            <?php else: ?>
                <?php echo $t['has_account']; ?> <a href="?view=login"><?php echo $t['btn_login']; ?></a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const messages = <?php echo json_encode($t); ?>;

        async function submitForm(e) {
            e.preventDefault();
            const form = document.getElementById('authForm');
            const formData = new FormData(form);
            const alertBox = document.getElementById('alertBox');

            if(formData.get('action') === 'register' && formData.get('password') !== formData.get('confirm_password')) {
                showAlert('两次密码输入不一致。', 'error');
                return;
            }

            try {
                const response = await fetch('../../services/User_Service/AuthController.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status === 'success') {
                    showAlert(messages[result.message] || 'Success', 'success');
                    setTimeout(() => {
                        window.location.href = result.redirect || '?view=login';
                    }, 1500);
                } else {
                    showAlert(messages[result.message] || 'Error occurred.', 'error');
                    document.getElementById('captchaImage').click(); // 刷新验证码
                }
            } catch (error) {
                showAlert('Network error, please try again.', 'error');
            }
        }

        function showAlert(msg, type) {
            const alertBox = document.getElementById('alertBox');
            alertBox.textContent = msg;
            alertBox.className = 'alert ' + (type === 'error' ? 'alert-error' : 'alert-success');
            alertBox.style.display = 'block';
        }
    </script>
</body>
</html>