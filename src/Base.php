<?php

// 严格模式
declare(strict_types=1);

namespace Bunny\Term;

class Base
{
    private \FFI $ffi;

    public function __construct()
    {
        // 判断是否是windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->ffi = \FFI::cdef(
                'bool isKeyPressed(int key);',
                dirname(__DIR__) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "keyboard.dll"
            );
        }
    }

    /**
     * 检测按键是否按下
     *
     * @param integer $key
     * @return boolean
     */
    public function isKeyPressed(int $key): bool
    {
        return $this->ffi->isKeyPressed($key);
    }

    /**
     * 获取终端尺寸
     *
     * @return array
     */
    public function getSize(): array
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
    public function setCursorPosition(int $x, int $y): void
    {
        echo "\x1b[{$y};{$x}H";
        flush();
    }

    /**
     * 显示光标
     *
     * @return void
     */
    public function showCursor(): void
    {
        echo "\x1b[?25h";
        flush();
    }

    /**
     * 隐藏光标

     *
     * @return void
     */
    public function hideCursor(): void
    {
        echo "\x1b[?25l";
        flush();
    }
}
