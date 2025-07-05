<?php

// 严格模式
declare(strict_types=1);

namespace Workbunny\Term;

/**
 * 键盘
 * 
 */
enum KeyboardKey
{
    case KeyA;
    case KeyB;
    case KeyC;
    case KeyD;
    case KeyE;
    case KeyF;
    case KeyG;
    case KeyH;
    case KeyI;
    case KeyJ;
    case KeyK;
    case KeyL;
    case KeyM;
    case KeyN;
    case KeyO;
    case KeyP;
    case KeyQ;
    case KeyR;
    case KeyS;
    case KeyT;
    case KeyU;
    case KeyV;
    case KeyW;
    case KeyX;
    case KeyY;
    case KeyZ;

    case KeyApostrophe; // '
    case KeyComma; // ,
    case KeyMinus; // -
    case KeyPeriod; // .
    case KeySlash; // /
    case KeyZero; // 0
    case KeyOne; // 1
    case KeyTwo; // 2
    case KeyThree; // 3
    case KeyFour; // 4
    case KeyFive; // 5
    case KeySix; // 6
    case KeySeven; // 7
    case KeyEight; // 8
    case KeyNine; // 9
    case KeySemicolon; // ;
    case KeyEqual; // =
    case KeyLeftBracket; // [
    case KeyBackslash; // \
    case KeyRightBracket; // ]
    case KeyGrave; // `

    case KeySpace; // 空格
    case KeyEsc; // 退出
    case KeyEnter; // 回车
    case KeyTab; // tab键
    case KeyBackspace; // 退格键
    case KeyInsrt; // 插入键
    case KeyDelete; // 删除键
    case KeyRight; // 右方向键
    case KeyDown; // 下方向键
    case KeyUp; // 上方向键
    case KeyLeft; // 左方向键
    case KeyPageUp; // 上翻页键
    case KeyPageDown; // 下翻页键
    case KeyHome; // 首页键
    case KeyEnd; // 尾页键
    case KeyCapsLock; // 大写锁定键
    case KeyScrollLock; // 滚动锁定键
    case KeyNumLock; // 数字锁定键
    case KeyPrintScreen; // 打印键
    case KeyPause; // 暂停键
    case KeyF1; // F1键
    case KeyF2; // F2键
    case KeyF3; // F3键
    case KeyF4; // F4键
    case KeyF5; // F5键
    case KeyF6; // F6键
    case KeyF7; // F7键
    case KeyF8; // F8键
    case KeyF9; // F9键
    case KeyF10; // F10键
    case KeyF11; // F11键
    case KeyF12; // F12键
    case KeyLeftShift; // 左Shift键
    case KeyLeftControl; // 左ctrl键
    case KeyLeftAlt; // 左alt键
    case KeyLeftSuper; // 左super键
    case KeyRightControl; // 右ctrl键
    case KeyRightAlt; // 右alt键
    case KeyRightSuper; // 右super键
    case KeyKBMenu; // 菜单键

    case KeyKP0; // 0键
    case KeyKP1; // 1键
    case KeyKP2; // 2键
    case KeyKP3; // 3键
    case KeyKP4; // 4键
    case KeyKP5; // 5键
    case KeyKP6; // 6键
    case KeyKP7; // 7键
    case KeyKP8; // 8键
    case KeyKP9; // 9键
    case KeyKPDecimal; // 小数点键
    case KeyKPDivide; // 除号键
    case KeyKPMultiply; // 乘号键
    case KeyKPSubtract; // 减号键
    case KeyKPAdd; // 加号键
    case KeyKPEnter; // 回车键
    case KeyKPEqual; // =键

    /**
     * 获取键值
     *
     * @return integer
     */
    public function key(): int
    {
        $isWin = PHP_OS_FAMILY === 'Windows';
        return match ($this) {
            // 字母键 (Windows: ASCII大写, Linux: ASCII小写)
            self::KeyA => $isWin ? 65 : 97,
            self::KeyB => $isWin ? 66 : 98,
            self::KeyC => $isWin ? 67 : 99,
            self::KeyD => $isWin ? 68 : 100,
            self::KeyE => $isWin ? 69 : 101,
            self::KeyF => $isWin ? 70 : 102,
            self::KeyG => $isWin ? 71 : 103,
            self::KeyH => $isWin ? 72 : 104,
            self::KeyI => $isWin ? 73 : 105,
            self::KeyJ => $isWin ? 74 : 106,
            self::KeyK => $isWin ? 75 : 107,
            self::KeyL => $isWin ? 76 : 108,
            self::KeyM => $isWin ? 77 : 109,
            self::KeyN => $isWin ? 78 : 110,
            self::KeyO => $isWin ? 79 : 111,
            self::KeyP => $isWin ? 80 : 112,
            self::KeyQ => $isWin ? 81 : 113,
            self::KeyR => $isWin ? 82 : 114,
            self::KeyS => $isWin ? 83 : 115,
            self::KeyT => $isWin ? 84 : 116,
            self::KeyU => $isWin ? 85 : 117,
            self::KeyV => $isWin ? 86 : 118,
            self::KeyW => $isWin ? 87 : 119,
            self::KeyX => $isWin ? 88 : 120,
            self::KeyY => $isWin ? 89 : 121,
            self::KeyZ => $isWin ? 90 : 122,

            // 符号键 (统一使用ASCII码)
            self::KeyApostrophe => 39,     // '
            self::KeyComma => 44,          // ,
            self::KeyMinus => 45,          // -
            self::KeyPeriod => 46,         // .
            self::KeySlash => 47,          // /
            self::KeySemicolon => 59,      // ;
            self::KeyEqual => 61,          // =
            self::KeyLeftBracket => 91,    // [
            self::KeyBackslash => 92,      // \
            self::KeyRightBracket => 93,   // ]
            self::KeyGrave => 96,          // `

            // 数字键 (统一使用ASCII码)
            self::KeyZero => 48,
            self::KeyOne => 49,
            self::KeyTwo => 50,
            self::KeyThree => 51,
            self::KeyFour => 52,
            self::KeyFive => 53,
            self::KeySix => 54,
            self::KeySeven => 55,
            self::KeyEight => 56,
            self::KeyNine => 57,

            // 控制键 (跨平台统一值)
            self::KeySpace => 32,          // 空格
            self::KeyEsc => 27,            // 退出
            self::KeyEnter => 13,          // 回车
            self::KeyTab => 9,             // Tab
            self::KeyBackspace => 8,       // 退格
            self::KeyInsrt => $isWin ? 45 : 999,    // 插入(Windows:45, Linux自定义)
            self::KeyDelete => $isWin ? 46 : 127,   // 删除(Windows:46, Linux:DEL)
            self::KeyRight => $isWin ? 39 : 67,     // 右箭头
            self::KeyDown => $isWin ? 40 : 66,      // 下箭头
            self::KeyUp => $isWin ? 38 : 65,        // 上箭头
            self::KeyLeft => $isWin ? 37 : 68,      // 左箭头
            self::KeyPageUp => $isWin ? 33 : 73,    // 上翻页
            self::KeyPageDown => $isWin ? 34 : 81,  // 下翻页
            self::KeyHome => $isWin ? 36 : 74,      // 首页
            self::KeyEnd => $isWin ? 35 : 79,       // 尾页
            self::KeyCapsLock => $isWin ? 20 : 58,  // 大写锁定
            self::KeyScrollLock => $isWin ? 145 : 70, // 滚动锁定
            self::KeyNumLock => $isWin ? 144 : 77,  // 数字锁定
            self::KeyPrintScreen => $isWin ? 44 : 111, // 打印
            self::KeyPause => $isWin ? 19 : 110,    // 暂停

            // 功能键 (统一值)
            self::KeyF1 => 112,
            self::KeyF2 => 113,
            self::KeyF3 => 114,
            self::KeyF4 => 115,
            self::KeyF5 => 116,
            self::KeyF6 => 117,
            self::KeyF7 => 118,
            self::KeyF8 => 119,
            self::KeyF9 => 120,
            self::KeyF10 => 121,
            self::KeyF11 => 122,
            self::KeyF12 => 123,

            // 修饰键 (使用扩展码区分左右)
            self::KeyLeftShift => $isWin ? 160 : 999,   // 左Shift
            self::KeyLeftControl => $isWin ? 162 : 999, // 左Ctrl
            self::KeyLeftAlt => $isWin ? 164 : 999,     // 左Alt
            self::KeyLeftSuper => $isWin ? 91 : 115,    // 左Super(Windows:91, Linux:115)
            self::KeyRightControl => $isWin ? 163 : 999, // 右Ctrl
            self::KeyRightAlt => $isWin ? 165 : 999,    // 右Alt
            self::KeyRightSuper => $isWin ? 92 : 116,   // 右Super(Windows:92, Linux:116)
            self::KeyKBMenu => $isWin ? 93 : 117,       // 菜单键

            // 小键盘 (统一使用扩展码)
            self::KeyKP0 => 96,
            self::KeyKP1 => 97,
            self::KeyKP2 => 98,
            self::KeyKP3 => 99,
            self::KeyKP4 => 100,
            self::KeyKP5 => 101,
            self::KeyKP6 => 102,
            self::KeyKP7 => 103,
            self::KeyKP8 => 104,
            self::KeyKP9 => 105,
            self::KeyKPDecimal => 110,    // 小数点
            self::KeyKPDivide => 111,      // 除号
            self::KeyKPMultiply => 106,    // 乘号
            self::KeyKPSubtract => 109,    // 减号
            self::KeyKPAdd => 107,         // 加号
            self::KeyKPEnter => 13,        // 回车
            self::KeyKPEqual => 61        // 等号
        };
    }
}
