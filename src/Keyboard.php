<?php

// 严格模式
declare(strict_types=1);

namespace Bunny\Term;

/**
 * 键盘类
 * @method bool isKeyPressed(int $key) 检测按键是否按下
 */
class Keyboard
{
    /**
     * FFI接口
     *
     * @var \FFI
     */
    private \FFI $ffi;

    // 改为实例属性
    private $originalTerminalSettings = null;

    /**
     * 构造函数
     */
    public function __construct()
    {
        // 判断是否是windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->ffi = \FFI::cdef(
                'bool isKeyPressed(int key);',
                dirname(__DIR__) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "keyboard.dll"
            );
        } else {
            // 使用 $this-> 访问实例属性
            $this->originalTerminalSettings = shell_exec('stty -g');
            // 设置终端为非规范模式，不显示输入字符，且立即响应
            system('stty -icanon -echo');
        }
    }
    
    /**
     * 恢复终端设置
     *
     * @return void
     */
    public function restore()
    {
        // 使用 $this-> 访问实例属性
        if ($this->originalTerminalSettings) {
            // 添加 trim() 和 escapeshellarg() 确保安全性
            $settings = trim($this->originalTerminalSettings);
            system('stty ' . escapeshellarg($settings));
        }
    }

    /**
     * 调用底层函数
     *
     * @param string $name 函数名
     * @param array $arguments 参数
     * @return void
     */
    public function __call(string $name, array $arguments)
    {
        if ($name === 'isKeyPressed') {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                return $this->ffi->$name($arguments[0]);
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
                        return $charAscii === $arguments[0];
                    }
                }
                return false;
            }
        }
    }
}
