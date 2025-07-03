<?php

// 严格模式
declare(strict_types=1);

namespace Bunny\Term;

use Bunny\Term\Base;
use Bunny\Term\Draw;

class Tui
{
    /**
     * 终端运行状态
     *
     * @var boolean
     */
    private bool $isRun;

    /**
     * 终端变量
     *
     * @var array
     */
    public array $var = [];

    /**
     * 事件
     *
     * @var callable
     */
    private $eventCallback;

    /**
     * 绘画
     *
     * @var callable
     */
    private $frameCallback;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->var["control"] = new Base();
        $this->var["draw"] = new Draw();
        $this->isRun = true;
        register_shutdown_function([$this, 'resetTerminal']);
    }

    /**
     * 事件
     *
     * @param callable $func
     * @return self
     */
    public function event(callable $func): self
    {
        $this->eventCallback = \Closure::fromCallable($func);
        return $this;
    }

    /**
     * 绘画
     *
     * @param callable $func 回调函数
     * @return self
     */
    public function frame(callable $func): self
    {
        $this->frameCallback = \Closure::fromCallable($func);
        return $this;
    }

    /**
     * 等待
     *
     * @return void
     */
    public function wait(): void
    {
        $this->var["control"]->hideCursor();
        while ($this->isRun) {
            ob_start();
            try {
                // 默认点击esc退出
                if ($this->var["control"]->isKeyPressed(27)) {
                    $this->clean();
                }
                // 处理事件
                if ($this->eventCallback) {
                    ($this->eventCallback)($this->var);
                }
                // 渲染帧
                if ($this->frameCallback) {
                    ($this->frameCallback)($this->var);
                }
            } catch (\Throwable $e) {
                ob_end_clean();
                throw new \Exception($e->getMessage());
            }
            // file_put_contents("test.txt", ob_get_clean());
            echo ob_get_clean();
            usleep(50_000);
        }

        $this->resetTerminal();
    }

    /**
     * 停止终端绘画
     *
     * @return void
     */
    public function clean()
    {
        $this->isRun = false;
    }

    // 重置终端状态（异常退出时自动调用）
    public function resetTerminal(): void
    {
        $this->var["draw"]->clear();
        $this->var["draw"]->reset();
        $this->var["control"]->showCursor();
        $this->var["control"]->setCursorPosition(0, 0);
    }
}
