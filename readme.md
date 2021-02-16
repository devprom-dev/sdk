## Введение
При помощи Devprom SDK вы можете создавать расширения (плагины) к Devprom. Мы уже подготовили несколько примеров, изучив которые вы сможете создать ваши плагины. Перед началом работы с SDK необходимо настроить рабочее место разработчика.

## Примеры плагинов
#### app/plugins/example1
Пример создания триггеров на изменение данных: изменение задач, создание пользователя.
Пример триггера, изменяющего состояние пожелания при создании задачи.
#### app/plugins/example2 
Пример расширения модели данных путем добавления нового поля в таблице и соответствующего атрибута на форме.
#### app/plugins/example3
Пример добавления дополнительного поля на форму списания времени, для указания типа задачи (активности) на что списано время.
![Image of Example3](https://raw.githubusercontent.com/devprom-dev/sdk/master/images/example3.png)
#### app/plugins/example4
Пример реализации модуля, отображающего все заявки от пользователей.
![Image of Example4](https://raw.githubusercontent.com/devprom-dev/sdk/master/images/example4.png)
#### app/plugins/customs
Пример создания собственого отчета "Детализация времени цикла".

## Разработка под Windows
Для разработки рекомендуем использовать PHP Storm. Перед началом работы с SDK необходимо установить GIT клиент.

### Начальная установка SDK
1. Установить SDK командой setup.bat
2. Отредактировать windows/system32/drivers/etc/hosts и добавить туда содержимое файла hosts
3. Открыть приложение http://devprom.local и дождаться завершения установки
4. По окончании установки автоматически создается пользователь: admin/admin

### Настройка запуска фоновых задач
Фоновые задачи используются для отправки почты, рассчета метрик и исторических данных.
1. Отредактировать файл dev/pycron/crontab.txt, внизу заменить <specify-working-dir-here> на путь, где установлен PyCron (например, c:\sdk\dev\pycron)
2. Установить сервис dev/pycron/pycron -install (нужны права администратора)
3. Запустить сервис net start pycron

## Разработка плагинов
Команда для создания нового плагина:   

    > php /projects/sdk/lib/app/console new-plugin mypluginname   
    
Расположение кода плагина:   

    > cd /projects/sdk/app/plugins/mypluginname   
    
Подготовка дистрибутива для установки плагина:   

    > php /projects/sdk/lib/app/console build-plugin mypluginname   
         
Расположение дистрибутива плагина:   

    > ls -l /projects/sdk/build/plugin.mypluginname.zip

## Основные команды
Команда для создания нового плагина:   

    > dev\php\php lib/app/console new-plugin mypluginname   
    
Расположение кода плагина:   

    > cd app/plugins/mypluginname   
    
Подготовка дистрибутива для установки плагина:   

    > dev\php\php lib/app/console build-plugin mypluginname   
         
Расположение дистрибутива плагина:   

    build/plugin.mypluginname.zip
