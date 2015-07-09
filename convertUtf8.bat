echo off

for /F "delims=" %%a in ('dir /ad /b "%~1"') do (
   	if NOT "%%a"=="ext" (
		for /F "delims=" %%f in ('dir /b "%~1\%%a"') do (
			for %%i in ("%%f") do (
				if  "%%~xi"==".php" (
					call win_iconv -f cp1251 -t utf8 %~1\%%a\%%f > tmp
					copy tmp %~1\%%a\%%f
					del tmp
				)
				if  "%%~xi"==".html" (
					call win_iconv -f cp1251 -t utf8 %~1\%%a\%%f > tmp
					copy tmp %~1\%%a\%%f
					del tmp
				)
			)
		)
   		call convertUtf8.bat "%~1\%%a"
	)
)