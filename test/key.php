<?php

class KeyboardListener
{
    // 改为实例属性 (移除 static)
    private $originalTerminalSettings = null;

    public function __construct()
    {
        // 使用 $this-> 访问实例属性
        $this->originalTerminalSettings = shell_exec('stty -g');
        
        // 设置终端为非规范模式，不显示输入字符，且立即响应
        system('stty -icanon -echo');
    }

    public function restore()
    {
        // 使用 $this-> 访问实例属性
        if ($this->originalTerminalSettings) {
            // 添加 trim() 和 escapeshellarg() 确保安全性
            $settings = trim($this->originalTerminalSettings);
            system('stty ' . escapeshellarg($settings));
        }
    }

    public function isKeyPressed(int $key): bool
    {
        $read = [STDIN];
        $write = [];
        $except = [];
        
        // 设置非阻塞，检查是否有输入 (移出循环)
        stream_set_blocking(STDIN, false);
        
        if (stream_select($read, $write, $except, 0) > 0) {
            $char = fgetc(STDIN);
            
            if ($char !== false) {
                $charAscii = ord($char);
                return $charAscii === $key;
            }
        }
        return false;
    }
}

// 创建实例
$keyboardListener = new KeyboardListener();

// 注册关闭函数，确保程序结束时恢复终端
register_shutdown_function([$keyboardListener, 'restore']);

// 检查是否按下了某个键（比如按下了 'a'，其ASCII为97）
while (true) {
    if ($keyboardListener->isKeyPressed(97)) { // 'a' 的ASCII
        echo "You pressed 'a'!\n";
    }
    usleep(10000); // 10毫秒，避免过度占用CPU
}