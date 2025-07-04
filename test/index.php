<?php

require dirname(__DIR__) . "/vendor/autoload.php";

use Bunny\Term\Tui;
use Bunny\Term\Event;

// 示例
$tui = new Tui();

// 给事件定义新的参数
$tui->event->num = 24;

// 绘画
$tui->frame(function (Event $e) {
    // 点击A键盘
    if ($e->keyboard->isKeyPressed(65)) {
        $e->num++;
    }
    // 清除
    $e->draw->clear();
    // 输出内容
    $e->draw->bgColor(63, 81, 181); // 背景色
    $e->draw->rect(20, 6, 41, 10); // 矩形
    $e->draw->text($e->num, 8, "hello from php!"); // 文本
    // 光标位置设置
    $e->control->setCursorPosition(0, 0);
    // 重置
    $e->draw->reset();
});
// 等待
$tui->wait();

// /**
//  * 读取单个键盘输入（无需按回车键）
//  * 支持 Linux/macOS 和 Windows 系统
//  */
// function readKey(): string {
//     $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
//     return $isWindows ? readKeyWindows() : readKeyUnix();
// }

// /**
//  * Windows 系统专用实现（修复版）
//  */
// function readKeyWindows(): string {
//     // 保存原始阻塞模式
//     $blocking = stream_get_meta_data(STDIN)['blocked'];

//     // 设置非阻塞模式读取
//     stream_set_blocking(STDIN, false);

//     $char = '';
//     while (true) {
//         $c = fread(STDIN, 1);
//         if ($c !== false && $c !== '') {
//             $char = $c;
//             // 尝试读取更多（用于处理组合键）
//             while (($extra = fread(STDIN, 1)) !== false && $extra !== '') {
//                 $char .= $extra;
//             }
//             break;
//         }
//         usleep(10000); // 10ms等待减少CPU占用
//     }

//     // 恢复原始阻塞模式
//     stream_set_blocking(STDIN, $blocking);

//     return $char;
// }

// /**
//  * Linux/macOS 系统专用实现
//  */
// function readKeyUnix(): string {
//     // 保存原始终端设置
//     $term = shell_exec('stty -g');

//     try {
//         // 设置终端：关闭行缓冲，关闭回显
//         shell_exec('stty -icanon -echo');

//         // 读取单个字符
//         $char = fread(STDIN, 1);

//         // 读取方向键等多字节序列（如方向键：↑ = \e[A）
//         if ($char === "\e") {
//             $char .= stream_get_line(STDIN, 3, '');
//         }

//         return $char;
//     } finally {
//         // 无论成功与否都尝试恢复终端设置
//         shell_exec("stty $term 2>/dev/null");
//     }
// }

// // 使用示例 -----------------------------------------------------
// echo "按键监听测试 (按 q 退出):\n";

// while (true) {
//     echo "等待按键: ";

//     // 获取按键
//     $key = readKey();

//     // 处理特殊键序列
//     $keyName = match (bin2hex($key)) {
//         '1b5b41' => '↑',
//         '1b5b42' => '↓',
//         '1b5b43' => '→',
//         '1b5b44' => '←',
//         '09' => 'TAB',
//         '0d' => 'ENTER',
//         '7f' => 'BACKSPACE',
//         default => $key
//     };

//     $hex = bin2hex($key);

//     echo sprintf(" %s (十六进制: %s)\n", $keyName, $hex);

//     if ($key === 'q' || $key === "\x1b") { // q 或 ESC 退出
//         echo "已退出!\n";
//         break;
//     }
// }



/* $hello = "hello";

$num = 0;

while (true) {
    echo "\x1b]0;{$hello}-{$num}\x07";
    flush();
    sleep(1);
    $num++;
} */
