<?php

// 严格模式
declare(strict_types=1);

namespace Bunny\Term;

use Bunny\Term\KeyboardKey;

/**
 * 终端UI类
 */
class Tui
{
    /**
     * 终端运行状态
     *
     * @var boolean
     */
    private static bool $isRun;

    /**
     * FFI接口
     *
     * @var \FFI
     */
    private static \FFI $ffi;

    // 属性
    private static $settings = null;

    /**
     * 终端标题
     *
     * @var string
     */
    public static string $title = "Bunny-Tui";

    /**
     * 初始化
     *
     * @return void
     */
    public static function init(): void
    {
        // 判断是否是windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            self::$ffi = \FFI::cdef(
                'bool isKeyPressed(int key);',
                dirname(__DIR__) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "keyboard.dll"
            );
        } else {
            // 使用 self::$ 访问静态属性
            self::$settings = shell_exec('stty -g');
            // 设置终端为非规范模式，不显示输入字符，且立即响应
            system('stty -icanon -echo');
        }
        self::$isRun = true;
        register_shutdown_function([self::class, 'reset']);
    }

    /**
     * 设置标题
     *
     * @param string $title
     * @return void
     */
    public static function setTitle(string $title): void
    {
        self::$title = $title;
    }

    /**
     * 终端是否关闭
     *
     * @return boolean
     */
    public static function shouldClose(): bool
    {
        return self::$isRun;
    }

    /**
     * 绘画开始
     *
     * @return void
     */
    public static function begin(): void
    {
        if (self::keyPressed(KeyboardKey::KeyEsc)) {
            self::$isRun = false;
        }
        self::hideCursor();
        ob_start();
        // 显示标题
        echo "\x1b]0;" . self::$title . "\x07";
        flush();
        // 清除
        self::clear();
    }

    /**
     * 绘画结束
     *
     * @return void
     */
    public static function end(): void
    {
        echo "\x1b[0m";
        flush();
        echo ob_get_clean();
        usleep(50_000);
    }

    /**
     * 关闭
     *
     * @return void
     */
    public static function close(): void
    {
        self::$isRun = false;
        self::reset();
    }

    /**
     * 清除整个终端窗口和所有保存的行
     *
     * @return void
     */
    public static function clear(): void
    {
        echo "\x1b[2J\x1b[3J";
        flush();
    }

    /**
     * 重置终端状态
     *
     * @return void
     */
    public static function reset(): void
    {
        self::clear();
        self::showCursor();
        self::setCursorPosition(0, 0);
        // 使用 self::$ 访问静态属性
        if (self::$settings) {
            // 添加 trim() 和 escapeshellarg() 确保安全性
            $settings = trim(self::$settings);
            system('stty ' . escapeshellarg($settings));
        }
    }

    /**
     * 按下按键
     *
     * @param integer $key 按键码
     * @return boolean
     */
    public static function keyPressed(KeyboardKey $keys): bool
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return self::$ffi->isKeyPressed($keys->key());
        } else {
            $read = [STDIN];
            $write = [];
            $except = [];
            // 设置非阻塞，检查是否有输入 (移出循环)
            stream_set_blocking(STDIN, false);
            if (stream_select($read, $write, $except, 0) > 0) {
                $char = fgetc(STDIN);

                if ($char !== false) {
                    $charAscii = ord($char);
                    return $charAscii === $keys->key();
                }
            }
            return false;
        }
    }

    /**
     * 获取终端尺寸
     *
     * @return array
     */
    public static function getSize(): array
    {
        // 默认回退值
        $default = ['width' => 80, 'height' => 24];

        // 如果不是 CLI 环境，返回默认值
        if (PHP_SAPI !== 'cli') {
            return $default;
        }

        // Windows 系统
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $output = @shell_exec('mode con');

            if ($output) {
                $matches = [];
                // 匹配 Windows 终端尺寸
                if (preg_match('/Columns:\s+(\d+)/', $output, $matches)) {
                    $default['width'] = (int)$matches[1];
                }
                if (preg_match('/Lines:\s+(\d+)/', $output, $matches)) {
                    $default['height'] = (int)$matches[1];
                }
            }
            return $default;
        }

        // Unix-like 系统 (Linux, macOS, BSD 等)
        $methods = [
            // 方法1: 使用 stty
            function () {
                $size = @shell_exec('stty size 2>/dev/null');
                if ($size && preg_match('/(\d+)\s+(\d+)/', $size, $matches)) {
                    return [(int)$matches[2], (int)$matches[1]];
                }
                return null;
            },

            // 方法2: 使用 tput
            function () {
                $width = (int)@shell_exec('tput cols 2>/dev/null');
                $height = (int)@shell_exec('tput lines 2>/dev/null');
                return ($width > 0 && $height > 0) ? [$width, $height] : null;
            },

            // 方法3: 环境变量
            function () {
                $width = (int)getenv('COLUMNS');
                $height = (int)getenv('LINES');
                return ($width > 0 && $height > 0) ? [$width, $height] : null;
            },

            // 方法4: 使用 $_SERVER 变量
            function () {
                $width = (int)($_SERVER['COLUMNS'] ?? 0);
                $height = (int)($_SERVER['LINES'] ?? 0);
                return ($width > 0 && $height > 0) ? [$width, $height] : null;
            }
        ];

        // 尝试所有方法直到成功
        foreach ($methods as $method) {
            if ($size = $method()) {
                return [
                    'width' => $size[0],
                    'height' => $size[1]
                ];
            }
        }

        return $default;
    }

    /**
     * 设置光标位置
     *
     * @param integer $x 水平位置
     * @param integer $y 垂直位置
     * @return void
     */
    public static function setCursorPosition(int $x, int $y): void
    {
        echo "\x1b[{$y};{$x}H";
        flush();
    }

    /**
     * 显示光标
     *
     * @return void
     */
    public static function showCursor(): void
    {
        echo "\x1b[?25h";
        flush();
    }

    /**
     * 隐藏光标
     *
     * @return void
     */
    public static function hideCursor(): void
    {
        echo "\x1b[?25l";
        flush();
    }

    /**
     * 字符粗体
     *
     * @return void
     */
    public static function bold()
    {
        echo "\x1b[1m";
        flush();
    }

    /**
     * 绘制点
     *
     * @param integer $x 横坐标
     * @param integer $y 纵坐标
     * @return void
     */
    public static function point(int $x, int $y): void
    {
        self::setCursorPosition($x, $y);
        echo " ";
        flush();
    }

    /**
     * 绘制文本
     *
     * @param integer $x 横坐标
     * @param integer $y 纵坐标
     * @param string $str 文本
     * @return void
     */
    public static function text(int $x, int $y, string $str): void
    {
        self::setCursorPosition($x, $y);
        echo $str;
        flush();
    }

    /**
     * 绘制一条从点到点的线段
     *
     * @param integer $x1 起点横坐标
     * @param integer $y1 起点纵坐标
     * @param integer $x2 终点横坐标
     * @param integer $y2 终点纵坐标
     * @return void
     */
    public static function line(int $x1, int $y1, int $x2, int $y2): void
    {
        $min_x = min($x1, $x2);
        $min_y = min($y1, $y2);
        $max_x = max($x1, $x2);
        if ($y1 === $y2) {
            // 水平线，性能优化
            self::setCursorPosition($min_x, $min_y);
            echo str_repeat(" ", $max_x + 1 - $min_x);
            return;
        }
        // 使用 Bresenham 直线算法绘制各个点：
        $x11 = $x1;
        $x22 = $x2;
        $y11 = $y1;
        $y22 = $y2;
        $sx = $x11 < $x22 ? 1 : -1;
        $sy = $y11 < $y22 ? 1 : -1;
        $dx = abs($x22 - $x11);
        $dy = abs($y22 - $y11);
        $err = $dx + $dy;
        do {
            self::point($x11, $y11);
            $e2 = 2 * $err;
            if ($e2 >= $dy) {
                $err += $dy;
                $x11 += $sx;
            }
            if ($e2 <= $dx) {
                $err += $dx;
                $y11 += $sy;
            }
        } while ($x11 === $x22 && $y11 === $y22);
    }

    /**
     * 绘制虚线
     *
     * @param integer $x1 起点横坐标
     * @param integer $y1 起点纵坐标
     * @param integer $x2 终点横坐标
     * @param integer $y2 终点纵坐标
     * @return void
     */
    public static function dashedLine(int $x1, int $y1, int $x2, int $y2): void
    {
        $x11 = $x1;
        $x22 = $x2;
        $y11 = $y1;
        $y22 = $y2;
        $sx = $x11 < $x22 ? 1 : -1;
        $sy = $y11 < $y22 ? 1 : -1;
        $dx = abs($x22 - $x11);
        $dy = abs($y22 - $y11);
        $err = $dx + $dy;
        $i = 0;
        do {
            if ($i % 2 == 0) {
                self::point($x11, $y11);
            }
            $e2 = 2 * $err;
            if ($e2 >= $dy) {
                $err += $dy;
                $x11 += $sx;
            }
            if ($e2 <= $dx) {
                $err += $dx;
                $y11 += $sy;
            }
            $i++;
        } while ($x11 === $x2 && $y11 === $y22);
    }

    /**
     * 绘制矩形
     *
     * @param integer $x1 左上角横坐标
     * @param integer $y1 左上角纵坐标
     * @param integer $x2 右下角横坐标
     * @param integer $y2 右下角纵坐标
     * @return void
     */
    public static function rect(int $x1, int $y1, int $x2, int $y2): void
    {
        if ($y1 === $y2 || $x1 === $x2) {
            self::line($x1, $y1, $x2, $y2);
            return;
        }
        if ($y1 < $y2) {
            $min_y = $y1;
            $max_y = $y2;
        } else {
            $min_y = $y2;
            $max_y = $y1;
        }
        for ($y_pos = $min_y; $y_pos <= $max_y; $y_pos++) {
            self::line($x1, $y_pos, $x2, $y_pos);
        }
    }

    /**
     * 绘制虚线矩形
     *
     * @param integer $x1 左上角横坐标
     * @param integer $y1 左上角纵坐标
     * @param integer $x2 右下角横坐标
     * @param integer $y2 右下角纵坐标
     * @return void
     */
    public static function dashedRect(int $x1, int $y1, int $x2, int $y2): void
    {
        if ($y1 === $y2 || $x1 === $x2) {
            self::dashedLine($x1, $y1, $x2, $y2);
            return;
        }
        $min_x = min($x1, $x2);
        $min_y = min($y1, $y2);
        $max_x = max($x1, $x2);
        $max_y = max($y1, $y2);

        self::dashedLine($min_x, $min_y, $max_x, $min_y);
        self::dashedLine($min_x, $min_y, $min_x, $max_y);
        if (($max_y - $min_y) & 1 == 0) {
            self::dashedLine($min_x, $max_y, $max_x, $max_y);
        } else {
            self::dashedLine($min_x + 1, $max_y, $max_x, $max_y);
        }
        if (($max_x - $min_x) & 1 == 0) {
            self::dashedLine($max_x, $min_y, $max_x, $max_y);
        } else {
            self::dashedLine($max_x, $min_y + 1, $max_x, $max_y);
        }
    }

    /**
     * 设置颜色
     *
     * @param array<int> $color 颜色数组，包含 ['r'、'g'、'b'] 键
     * @return void
     */
    public static function color(...$color): void
    {
        echo "\x1b[38;2;{$color[0]};{$color[1]};{$color[2]}m";
        flush();
    }

    /**
     * 设置背景颜色
     *
     * @param array<int> $color 颜色数组，包含 ['r'、'g'、'b'] 键
     * @return void
     */
    public static function bgColor(...$color): void
    {
        echo "\x1b[48;2;{$color[0]};{$color[1]};{$color[2]}m";
        flush();
    }

    /**
     * 重置颜色
     *
     * @return void
     */
    public static function resetColor(): void
    {
        echo "\x1b[39m";
        flush();
    }

    /**
     * 重置背景色
     *
     * @return void
     */
    public static function resetBgColor(): void
    {
        echo "\x1b[49m";
        flush();
    }
}
