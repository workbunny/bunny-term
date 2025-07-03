#include <stdbool.h>
#include <windows.h>

#define EXPORT __declspec(dllexport)

EXPORT bool isKeyPressed(int key)
{
    return GetAsyncKeyState(key) & 0x8000;
}