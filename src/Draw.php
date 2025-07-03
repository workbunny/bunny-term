<?php

// 严格模式
declare(strict_types=1);

namespace Bunny\Term;

use Bunny\Term\Base;

class Draw extends Base
{
    /**
     * 绘制点
     *
     * @param integer $x 横坐标
     * @param integer $y 纵坐标
     * @return void
     */
    public function point(int $x, int $y): void
    {
        $this->setCursorPosition($x, $y);
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
    public function text(int $x, int $y, string $str): void
    {
        $this->setCursorPosition($x, $y);
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
    public function line(int $x1, int $y1, int $x2, int $y2): void
    {
        $min_x = min($x1, $x2);
        $min_y = min($y1, $y2);
        $max_x = max($x1, $x2);
        if ($y1 === $y2) {
            // 水平线，性能优化
            $this->setCursorPosition($min_x, $min_y);
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
            $this->point($x11, $y11);
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
     * 绘制矩形
     *
     * @param integer $x1 左上角横坐标
     * @param integer $y1 左上角纵坐标
     * @param integer $x2 右下角横坐标
     * @param integer $y2 右下角纵坐标
     * @return void
     */
    public function rect(int $x1, int $y1, int $x2, int $y2): void
    {
        if ($y1 === $y2 || $x1 === $x2) {
            $this->line($x1, $y1, $x2, $y2);
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
            $this->line($x1, $y_pos, $x2, $y_pos);
        }
    }

    /**
     * 设置颜色
     *
     * @param array<int> $color 颜色数组，包含 ['r'、'g'、'b'] 键
     * @return void
     */
    public function color(...$color): void
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
    public function bgColor(...$color): void
    {
        echo "\x1b[48;2;{$color[0]};{$color[1]};{$color[2]}m";
        flush();
    }

    /**
     * 重置颜色
     *
     * @return void
     */
    public function resetColor(): void
    {
        echo "\x1b[39m";
        flush();
    }

    /**
     * 重置背景色
     *
     * @return void
     */
    public function resetBgColor(): void
    {
        echo "\x1b[49m";
        flush();
    }

    /**
     * 重置所有颜色和文本
     *
     * @return void
     */
    public function reset(): void
    {
        echo "\x1b[0m";
        flush();
    }

    /**
     * 清除整个终端窗口和所有保存的行
     *
     * @return void
     */
    public function clear(): void
    {
        echo "\x1b[2J\x1b[3J";
        flush();
    }
}
