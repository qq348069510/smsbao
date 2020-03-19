<?php
require_once "./src/Smsbao.php";

use smsbao\Smsbao;

$smsbao = new Smsbao("qq348069510", "123456");
$send = $smsbao->send("13812345678", "【测试消息】您好，您的验证码是：" . $smsbao->random(6, true), Smsbao::GN_SMS);
if ($send) {
    echo "短信发送成功！";
} else {
    echo "短信发送失败，" . $smsbao->getErrorMessage();
}
echo "<hr/>";
sleep(3);
echo $smsbao->query();