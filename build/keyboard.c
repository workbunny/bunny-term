#include <stdbool.h>
#include <stdlib.h>

// 平台定义
#ifdef _WIN32
#include <windows.h>
#define EXPORT __declspec(dllexport)
#else
#include <termios.h>
#include <unistd.h>
#include <fcntl.h>
#define EXPORT
#endif

#ifdef _WIN32

EXPORT bool isKeyPressed(int key)
{
    // 键码有效性检查
    if (key < 1 || key > 254)
    {
        return false;
    }
    return GetAsyncKeyState(key) & 0x8000;
}

#else

// Linux下的终端配置状态
static struct termios orig_termios;
static bool is_term_configured = false;

// 配置终端为非阻塞模式
static void configureTerminal()
{
    tcgetattr(STDIN_FILENO, &orig_termios);

    struct termios new_termios = orig_termios;
    // 禁用行缓冲和回显
    new_termios.c_lflag &= ~(ICANON | ECHO);
    // 设置非阻塞读取
    new_termios.c_cc[VMIN] = 0;
    new_termios.c_cc[VTIME] = 0;

    tcsetattr(STDIN_FILENO, TCSANOW, &new_termios);
    is_term_configured = true;
}

// 恢复终端原始配置
static void restoreTerminal()
{
    if (is_term_configured)
    {
        tcsetattr(STDIN_FILENO, TCSANOW, &orig_termios);
    }
}

EXPORT bool isKeyPressed(int key)
{
    // Linux下键码有效性检查
    if (key < 1 || key > 127)
    {
        return false;
    }

    // 配置终端（首次调用时）
    if (!is_term_configured)
    {
        configureTerminal();
        atexit(restoreTerminal); // 程序退出时自动恢复
    }

    // 使用非阻塞读取检查按键
    unsigned char ch;
    int bytes_read = read(STDIN_FILENO, &ch, 1);

    // 如果读到按键且匹配请求的键码
    if (bytes_read > 0 && (unsigned char)ch == (unsigned char)key)
    {
        return true;
    }
    return false;
}

#endif