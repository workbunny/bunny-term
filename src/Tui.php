<?php

// 严格模式
declare(strict_types=1);

namespace Bunny\Term;

use Bunny\Term\Event;

class Tui
{
    /**
     * 终端运行状态
     *
     * @var boolean
     */
    private bool $isRun;

    /**
     * 事件
     *
     * @var Event
     */
    public Event $event;

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
        $this->event = new Event();
        $this->isRun = true;
        register_shutdown_function([$this, 'resetTerminal']);
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
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            register_shutdown_function([$this->event->keyboard, 'restore']);
        }
        $this->event->control->hideCursor();
        while ($this->isRun) {
            ob_start();
            try {
                // 默认点击esc退出
                if ($this->event->keyboard->isKeyPressed(27)) {
                    $this->clean();
                }
                // 渲染帧
                if ($this->frameCallback) {
                    ($this->frameCallback)($this->event);
                }
            } catch (\Throwable $e) {
                ob_end_clean();
                throw new \Exception($e->getMessage());
            }
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
        $this->event->draw->clear();
        $this->event->draw->reset();
        $this->event->control->showCursor();
        $this->event->control->setCursorPosition(0, 0);
    }
}
