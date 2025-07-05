<?php

// 终端绘画

require dirname(__DIR__) . "/vendor/autoload.php";

use Bunny\Term\Tui;
use Bunny\Term\KeyboardKey;

// 示例
Tui::init(); // 初始化

Tui::setTitle("MyTui"); // 标题

$num = 24;
while (Tui::shouldClose()) { //主循环
    // 按键A按下时
    if (Tui::keyPressed(KeyboardKey::KeyA)) {
        $num++;
    }
    Tui::begin(); // 开始绘画

    Tui::bgColor(63, 81, 181); //背景色
    Tui::rect(20, 6, 41, 10); // 矩形
    Tui::text($num, 8, "你好"); //绘制文本

    Tui::end(); // 结束绘画
}
Tui::close();//关闭
