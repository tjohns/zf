@echo off
REM Zend Framework
REM
REM LICENSE
REM
REM This source file is subject to the new BSD license that is bundled
REM with this package in the file LICENSE.txt.
REM It is also available through the world-wide-web at this URL:
REM http://framework.zend.com/license/new-bsd
REM If you did not receive a copy of the license and are unable to
REM obtain it through the world-wide-web, please send an email
REM to license@zend.com so we can send you a copy immediately.
REM
REM Zend
REM Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
REM http://framework.zend.com/license/new-bsd     New BSD License

BREAK=ON
set PHP_BIN="php.exe"

REM %~dp0 is name of current script dir under NT
set SCRIPT_DIR=%~dp0

set ZF_SCRIPT=%SCRIPT_DIR%zf.php

REM Insert the name of this script as the first argument
"%PHP_BIN%" -d safe_mode=Off -f "%ZF_SCRIPT%" %*