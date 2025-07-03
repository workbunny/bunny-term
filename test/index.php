<?php

require dirname(__DIR__) . "/vendor/autoload.php";

use Bunny\Term\Color;
use Bunny\Term\Tui;

$tui = new Tui();
$tui->var["num"] = 0;
$tui->event(function (Tui $ui) {
    if ($ui->control->isKeyPressed(65)) {
        $ui->var["num"]++;
    }
    if ($ui->control->isKeyPressed(27)) {
        $ui->clean();
    }
});

$tui->frame(function (Tui $ui) {
    $ui->control->hideCursor();
    $ui->control->setCursorPosition($ui->var["num"], 10);
    echo Color::rgb(255, 0, 0, "你好");
    $ui->control->setCursorPosition(0, 0);
});

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
