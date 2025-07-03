<?php

// 严格模式
declare(strict_types=1);

namespace Bunny\Term;

class Color
{
    /**
     * formatEsc生成一个ANSI转义码，用于选择下列图形的图形形式
     * 文本。每个可以在‘ code ’中传递的属性，用‘;’分隔，将会生效。
     * 直到终端遇到另一个SGR ANSI转义码。有关不同的更多详细信息
     * 代码及其含义，参见：https://en.wikipedia.org/wiki/ANSI_escape_code#SGR_(Select_Graphic_Rendition)_parameters
     *
     * @param string $code 代码
     * @return string
     */
    public static function formatEsc(string $code): string
    {
        return "\x1b[{$code}m";
    }

    /**
     * format返回用前面格式化的ANSI转义编码的‘ msg ’
     * “打开”和随后的“关闭”。
     * 例如:`format("hi", "9", "29")` 返回 `"\x1b[9mhi\x1b[29m"`
     * 或者"hi"，其中"\x1b[9m"表示
     * 划掉/划线文本和" \x1b[29m" ‘ ’关闭划线。
     *
     * @param string $msg 文本
     * @param string $open 打开
     * @param string $close 关闭
     * @return string
     */
    public static function format(string $msg, string $open, string $close): string
    {
        return "\x1b[{$open}m{$msg}\x1b[{$close}m";
    }

    /**
     * 返回用前面格式化的ANSI转义编码的‘ msg ’
     * ‘ open ‘，随后的’ close ’和提供的RGB颜色‘ r ’， ‘ g ’和‘ b ’。
     *
     * @param integer $r 红色
     * @param integer $g 绿色
     * @param integer $b 蓝色
     * @param string $msg 文本
     * @param string $open 打开
     * @param string $close 关闭
     * @return string
     */
    public static function formatRgb(
        int $r,
        int $g,
        int $b,
        string $msg,
        string $open,
        string $close
    ): string {
        return "\x1b[{$open};2;{$r};{$g};{$b}m{$msg}\x1b[{$close}m";
    }

    /**
     * rgb返回用前面格式化的ANSI转义编码的‘ msg ’
     * ‘ open ‘和随后的’ close ‘和提供的RGB颜色‘ r ’， ‘ g ’和‘ b ’。
     *
     * @param integer $r 红色
     * @param integer $g 绿色
     * @param integer $b 蓝色
     * @param string $msg 文本
     * @return string
     */
    public static function rgb(
        int $r,
        int $g,
        int $b,
        string $msg
    ): string {
        return self::formatRgb($r, $g, $b, $msg, "38", "49");
    }

    /**
     * bgRgb返回用前面格式化的ANSI转义编码的‘ msg ’
     * ‘ open ‘和随后的’ close ‘和提供的RGB颜色‘ r ’， ‘ g ’和‘ b ’。
     *
     * @param integer $r 红色
     * @param integer $g 绿色
     * @param integer $b 蓝色
     * @param string $msg 文本
     * @return string
     */
    public static function bgRgb(
        int $r,
        int $g,
        int $b,
        string $msg
    ): string {
        return self::formatRgb($r, $g, $b, $msg, "48", "49");
    }

    /**
     * 前景为指定的‘ hex ’颜色的字符串。
     * 例如，‘ rgb(255, ’hi‘) ’返回‘hi’ "字符串
     * 蓝色，RGB格式为‘(0,0,255)’。
     *
     * @param integer $hex
     * @param string $msg
     * @return string
     */
    public static function hex(int $hex, string $msg): string
    {
        return self::formatRgb(
            ($hex >> 16) & 0xFF,
            ($hex >> 8) & 0xFF,
            $hex & 0xFF,
            $msg,
            "38",
            "49"
        );
    }

    /**
     * bgHex返回用前面格式化的ANSI转义编码的‘ msg ’
     * ‘ open ‘和随后的’ close ‘和提供的RGB颜色‘ r ’， ‘ g ’和‘ b ’。
     *
     * @param integer $hex
     * @param string $msg
     * @return string
     */
    public static function bgHex(int $hex, string $msg): string
    {
        return self::formatRgb(
            ($hex >> 16) & 0xFF,
            ($hex >> 8) & 0xFF,
            $hex & 0xFF,
            $msg,
            "48",
            "49"
        );
    }

    /**
     * 重置所有属性
     *
     * @param string $msg
     * @return string
     */
    public static function reset(string $msg): string
    {
        return self::format($msg, "0", "0");
    }

    /**
     * 加粗文本
     *
     * @param string $msg
     * @return string
     */
    public static function blod(string $msg): string
    {
        return self::format($msg, "1", "22");
    }

    /**
     * 暗淡文本
     *
     * @param string $msg
     * @return string
     */
    public static function dim(string $msg): string
    {
        return self::format($msg, "2", "22");
    }

    /**
     * 斜体文本
     *
     * @param string $msg
     * @return string
     */
    public static function italic(string $msg): string
    {
        return self::format($msg, "3", "23");
    }

    /**
     * 下划线文本
     *
     * @param string $msg
     * @return string
     */
    public static function underline(string $msg): string
    {
        return self::format($msg, "4", "24");
    }

    /**
     * 慢速闪烁文本
     *
     * @param string $msg
     * @return string
     */
    public static function slowBlink(string $msg): string
    {
        return self::format($msg, "5", "25");
    }

    /**
     * 快速闪烁文本
     *
     * @param string $msg
     * @return string
     */
    public static function rapidBlink(string $msg): string
    {
        return self::format($msg, "6", "26");
    }

    /**
     * 反转文本
     *
     * @param string $msg
     * @return string
     */
    public static function inverse(string $msg): string
    {
        return self::format($msg, "7", "27");
    }

    /**
     * 隐藏文本
     *
     * @param string $msg
     * @return string
     */
    public static function hidden(string $msg): string
    {
        return self::format($msg, "8", "28");
    }

    /**
     * 中划线文本
     *
     * @param string $msg
     * @return string
     */
    public static function strikethrough(string $msg): string
    {
        return self::format($msg, "9", "29");
    }
}
