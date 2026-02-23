<?php
// 开启 Session 用于记录用户语言偏好
session_start();

// 处理语言切换请求
if (isset($_GET['lang']) && in_array($_GET['lang'], ['zh', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
    header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?')); // 移除GET参数重定向
    exit;
}

// 默认语言设置 
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'zh';

// 双语字典字典
$i18n = [
    'zh' => [
        'title' => 'CSCA 备考培训与留学服务平台',
        'nav_home' => '首页',
        'nav_assessment' => '测评中心',
        'nav_learning' => '学习中心',
        'nav_agency' => '机构入驻',
        'nav_admin' => '管理后台',
        'login' => '登录 / 注册',
        'hero_title' => '直通中国顶尖高校的学术桥梁',
        'hero_subtitle' => '基于 2026 年 CSCA 核心录取标准，打造“基础测评 → 学习 → 测评 → 申请”一站式闭环服务。精准定位您的学术实力，获取中国政府奖学金。',
        'btn_start_test' => '开始基础测评 (免费)',
        'btn_explore' => '探索课程包',
        'feat_1_title' => '精准能力评估',
        'feat_1_desc' => '基于 IRT 模型的自适应测评，智能匹配 A/B/C/D 高校档位。',
        'feat_2_title' => '全真模拟与海量题库',
        'feat_2_desc' => '顺序练习与随机抽题，详细错题解析，助您攻克学术短板。',
        'feat_3_title' => 'B端机构生态',
        'feat_3_desc' => '全透明财务分账与学情监控，赋能教育机构高效招生与管理。'
    ],
    'en' => [
        'title' => 'CSCA Prep & Study Abroad Platform',
        'nav_home' => 'Home',
        'nav_assessment' => 'Assessments',
        'nav_learning' => 'Learning Center',
        'nav_agency' => 'Agencies',
        'nav_admin' => 'Admin Portal',
        'login' => 'Login / Sign Up',
        'hero_title' => 'Your Academic Bridge to Top Chinese Universities',
        'hero_subtitle' => 'Based on the 2026 CSCA core admission standards. We offer a one-stop closed-loop service: Basic Assessment → Study → Stage Test → Application.',
        'btn_start_test' => 'Start Basic Assessment (Free)',
        'btn_explore' => 'Explore Courses',
        'feat_1_title' => 'Accurate Evaluation',
        'feat_1_desc' => 'Adaptive testing based on the IRT model, intelligently matching A/B/C/D university tiers.',
        'feat_2_title' => 'Massive Question Bank',
        'feat_2_desc' => 'Sequential & random practice with detailed explanations to overcome weak points.',
        'feat_3_title' => 'Agency Ecosystem',
        'feat_3_desc' => 'Transparent financial settlement and student tracking to empower educational institutions.'
    ]
];

$t = $i18n[$lang];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['title']; ?> | CSCABridge</title>
    <style>
        :root {
            --primary-color: #1A56DB;
            --primary-hover: #1E40AF;
            --bg-color: #F9FAFB;
            --text-dark: #111827;
            --text-gray: #6B7280;
            --white: #FFFFFF;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: var(--bg-color); color: var(--text-dark); line-height: 1.6; }
        a { text-decoration: none; color: inherit; }
        
        /* Navbar */
        header { background: var(--white); box-shadow: 0 2px 4px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 100; }
        .nav-container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 1rem 2rem; }
        .logo { font-size: 1.5rem; font-weight: 800; color: var(--primary-color); letter-spacing: -0.5px; }
        .nav-links { display: flex; gap: 2rem; align-items: center; font-weight: 500; }
        .nav-links a:hover { color: var(--primary-color); transition: 0.3s; }
        .lang-switch a { padding: 4px 8px; border-radius: 4px; background: #eee; font-size: 0.85rem; }
        .btn-login { background: var(--primary-color); color: var(--white) !important; padding: 0.5rem 1.2rem; border-radius: 6px; }
        .btn-login:hover { background: var(--primary-hover); }

        /* Hero Section */
        .hero { background: linear-gradient(135deg, #1A56DB 0%, #047857 100%); color: var(--white); padding: 6rem 2rem; text-align: center; }
        .hero-content { max-width: 800px; margin: 0 auto; }
        .hero h1 { font-size: 3rem; margin-bottom: 1.5rem; line-height: 1.2; }
        .hero p { font-size: 1.2rem; margin-bottom: 2.5rem; opacity: 0.9; }
        .cta-buttons { display: flex; gap: 1rem; justify-content: center; }
        .btn-primary { background: var(--white); color: var(--primary-color); padding: 0.8rem 2rem; font-size: 1.1rem; font-weight: bold; border-radius: 8px; transition: 0.3s; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .btn-secondary { background: rgba(255,255,255,0.2); border: 1px solid var(--white); color: var(--white); padding: 0.8rem 2rem; font-size: 1.1rem; border-radius: 8px; transition: 0.3s; }
        .btn-secondary:hover { background: rgba(255,255,255,0.3); }

        /* Features Section */
        .features { padding: 5rem 2rem; max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; }
        .feature-card { background: var(--white); padding: 2.5rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center; border-top: 4px solid var(--primary-color); }
        .feature-card h3 { font-size: 1.3rem; margin-bottom: 1rem; }
        .feature-card p { color: var(--text-gray); }

        /* Footer */
        footer { background: #1F2937; color: #9CA3AF; text-align: center; padding: 2rem; font-size: 0.9rem; }
    </style>
</head>
<body>

    <header>
        <div class="nav-container">
            <div class="logo">CSCABridge</div>
            <nav class="nav-links">
                <a href="#"><?php echo $t['nav_home']; ?></a>
                <a href="/assessment"><?php echo $t['nav_assessment']; ?></a>
                <a href="/learning"><?php echo $t['nav_learning']; ?></a>
                <a href="/agency"><?php echo $t['nav_agency']; ?></a>
                <a href="/admin"><?php echo $t['nav_admin']; ?></a>
                
                <div class="lang-switch">
                    <?php if($lang == 'zh'): ?>
                        <a href="?lang=en">English</a>
                    <?php else: ?>
                        <a href="?lang=zh">中文</a>
                    <?php endif; ?>
                </div>

                <a href="/auth/login" class="btn-login"><?php echo $t['login']; ?></a>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1><?php echo $t['hero_title']; ?></h1>
            <p><?php echo $t['hero_subtitle']; ?></p>
            <div class="cta-buttons">
                <a href="/assessment/basic" class="btn-primary"><?php echo $t['btn_start_test']; ?></a>
                <a href="/learning/courses" class="btn-secondary"><?php echo $t['btn_explore']; ?></a>
            </div>
        </div>
    </section>

    <section class="features">
        <div class="feature-card">
            <h3><?php echo $t['feat_1_title']; ?></h3>
            <p><?php echo $t['feat_1_desc']; ?></p>
        </div>
        <div class="feature-card">
            <h3><?php echo $t['feat_2_title']; ?></h3>
            <p><?php echo $t['feat_2_desc']; ?></p>
        </div>
        <div class="feature-card">
            <h3><?php echo $t['feat_3_title']; ?></h3>
            <p><?php echo $t['feat_3_desc']; ?></p>
        </div>
    </section>

    <footer>
        <p>&copy; 2026 CSCABridge. All Rights Reserved. | Designed for China Scholastic Competency Assessment.</p>
    </footer>

</body>
</html>