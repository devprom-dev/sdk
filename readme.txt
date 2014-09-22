-- Первоначальная установка SDK

0. Установить Git-клиент, прописать путь к нему в переменной PATH (нужно для работы composer)
1. Установить SDK командой setup.bat
2. Отредактировать windows/system32/drivers/etc/hosts и добавить туда содержимое файла hosts
3. Открыть приложение http://devprom.local и дождаться завершения установки
4. По окончании установки автоматически создается пользователь: admin/admin

-- Разработка плагинов

Команда для создания нового плагина: dev\php\php lib/app/console new-plugin mypluginname
Расположение кода плагина: app/plugins/mypluginname

Подготовка дистрибутива для установки плагина: dev\php\php lib/app/console build-plugin mypluginname
Расположение дистрибутива плагина: build/plugin.mypluginname.zip
