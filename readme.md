## Введение
Процесс разработки с использованием Devprom SDK оптимизирован под Windows.
Для разработки рекомендуем использовать PHP Storm.   
Перед началом работы с SDK необходимо установить GIT клиент.

## Начальная установка SDK
1. Установить SDK командой setup.bat
2. Отредактировать windows/system32/drivers/etc/hosts и добавить туда содержимое файла hosts
3. Открыть приложение http://devprom.local и дождаться завершения установки
4. По окончании установки автоматически создается пользователь: admin/admin

## Настройка запуска фоновых задач (отправка почты, рассчет метрик и исторических данных)
1. Отредактировать файл dev/pycron/crontab.txt, внизу заменить <specify-working-dir-here> на путь, где установлен PyCron (например, c:\sdk\dev\pycron)
2. Установить сервис dev/pycron/pycron -install (нужны права администратора)
3. Запустить сервис net start pycron

## Обновление версии SDK
    #> upgrade.bat

## Примеры плагинов
### app/plugins/example1
Пример создания триггеров на изменение данных: изменение задач, создание пользователя.
Пример триггера, изменяющего состояние пожелания при создании по нему задачи.
### app/plugins/example2 
Пример расширения модели данных путем добавления нового поля в таблице и соответствующего атрибута на форме.
### app/plugins/customs
Пример создания собственого отчета "Детализация времени цикла".

## Разработка плагинов
<<<<<<< HEAD
Команда для создания нового плагина:   
    #> dev\php\php lib/app/console new-plugin mypluginname   
Расположение кода плагина:   
    #> cd app/plugins/mypluginname   
Подготовка дистрибутива для установки плагина:   
    #> dev\php\php lib/app/console build-plugin mypluginname   
       
Расположение дистрибутива плагина:    
    build/plugin.mypluginname.zip   
Команда для создания нового плагина:   
    #> dev\php\php lib/app/console new-plugin mypluginname   
Расположение кода плагина:   
    #> cd app/plugins/mypluginname   
Подготовка дистрибутива для установки плагина:   
    #> dev\php\php lib/app/console build-plugin mypluginname   
       
Расположение дистрибутива плагина:   
    build/plugin.mypluginname.zip


