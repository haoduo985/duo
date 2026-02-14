<?php
// 解决跨域问题
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// 获取POST数据
$input = json_decode(file_get_contents('php://input'), true);

// 验证必要参数
if (empty($input['wechat'])) {
    echo json_encode([
        'success' => false,
        'message' => '微信号不能为空'
    ]);
    exit;
}

// 配置QQ邮箱SMTP（关键！需要先开启QQ邮箱SMTP）
$smtp_host = 'smtp.qq.com'; // QQ邮箱SMTP服务器
$smtp_port = 465; // SSL端口
$smtp_user = '944980377@qq.com'; // 你的QQ邮箱
$smtp_pass = 'qruehwjwvzqhbbbb'; // 不是QQ密码！需要去QQ邮箱设置获取
$to_email = '944980377@qq.com'; // 接收邮件的邮箱

// 构建邮件内容
$subject = '缘分测试报名 - 得分' . $input['testScore'] . '分';
$body = "得分：{$input['testScore']}分\n\n";
$body .= "个人信息：\n";
$body .= "出生年份：" . ($input['birthYear'] ?: '未填写') . "\n";
$body .= "身高：" . ($input['height'] ?: '未填写') . " cm\n";
$body .= "体重：" . ($input['weight'] ?: '未填写') . " kg\n";
$body .= "工作：" . ($input['job'] ?: '未填写') . "\n";
$body .= "性格：" . ($input['personality'] ?: '未填写') . "\n";
$body .= "爱好：" . ($input['hobbies'] ?: '未填写') . "\n";
$body .= "微信号：{$input['wechat']}\n";
$body .= "其他介绍：" . ($input['introduction'] ?: '未填写') . "\n\n";
$body .= "提交时间：" . date('Y-m-d H:i:s') . "\n";

// 使用PHPMailer发送邮件（需要先下载PHPMailer）
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // 服务器配置
    $mail->SMTPDebug = 0; // 0=关闭调试，1=开启
    $mail->isSMTP();
    $mail->Host       = $smtp_host;
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtp_user;
    $mail->Password   = $smtp_pass;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = $smtp_port;

    // 收件人
    $mail->setFrom($smtp_user, '缘分测试系统');
    $mail->addAddress($to_email, '收件人');

    // 邮件内容
    $mail->isHTML(false); // 纯文本格式
    $mail->Subject = $subject;
    $mail->Body    = $body;

    $mail->send();
    
    echo json_encode([
        'success' => true,
        'message' => '邮件发送成功'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => '邮件发送失败：' . $mail->ErrorInfo
    ]);
}
?>
