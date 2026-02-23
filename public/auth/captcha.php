<?php
// public/auth/captcha.php
session_start();

// 生成随机验证码字符串 (4位字母数字)
$captcha_code = substr(str_shuffle("23456789ABCDEFGHJKLMNPQRSTUVWXYZ"), 0, 4);
$_SESSION['captcha'] = $captcha_code;

// 创建图片
$image = imagecreatetruecolor(120, 40);
$bg_color = imagecolorallocate($image, 243, 244, 246);
$text_color = imagecolorallocate($image, 26, 86, 219);
$line_color = imagecolorallocate($image, 200, 200, 200);

imagefilledrectangle($image, 0, 0, 120, 40, $bg_color);

// 添加干扰线
for ($i = 0; $i < 5; $i++) {
    imageline($image, rand(0, 120), rand(0, 40), rand(0, 120), rand(0, 40), $line_color);
}
// 添加干扰点
for ($i = 0; $i < 50; $i++) {
    imagesetpixel($image, rand(0, 120), rand(0, 40), $line_color);
}

// 将字符串写入图片 (使用内置字体或TTF)
imagestring($image, 5, 40, 12, $captcha_code, $text_color);

header("Content-type: image/png");
// 禁用缓存
header("Cache-Control: no-cache, must-revalidate");
imagepng($image);
imagedestroy($image);
?>