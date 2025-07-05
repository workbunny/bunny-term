<?php

// 终端绘画
require dirname(__DIR__) . "/vendor/autoload.php";

use Bunny\Term\Tui;

Tui::init(); // 初始化
Tui::setTitle("奔跑的兔子"); // 标题
// 获取终端尺寸
$size = Tui::getSize();
$width = $size['width'];
$height = $size['height'];
// 地板高度
$floorHeight = round($height / 2);
// 兔子位置
$positionX = $width - 1;
$positionY = $floorHeight - 1;
//主循环
while (Tui::shouldClose()) {
    // 更新兔子位置
    $positionX--;
    if ($positionX <= 0) {
        $positionX = $width - 1;
    }
    if ($positionX % 2 == 0) {
        $positionY =  $floorHeight - 2;
    } else {
        $positionY =  $floorHeight - 1;
    }
    Tui::begin();
    // 绘制太阳
    Tui::text($width - 5, 2, "☀️");
    // 绘制云朵
    Tui::text($width - 10, 2, "☁️");
    // 绘制地版
    Tui::color(34, 139, 34); //绿色
    Tui::text(1, $floorHeight, str_repeat("=", $width));
    // 绘制兔子
    Tui::resetColor();
    Tui::text($positionX, $positionY, "🐇");
    Tui::end();
}

Tui::close();
