var resource = {
    "kanban-intro": "Принципы <strong>Lean</strong> нашли отражение в подходе к разработке под названием <a href=\"http://devprom.ru/news/tag/Kanban\">Software Kanban</a>&nbsp;-&nbsp;максимизировать <strong>продуктивность команды</strong> за счет непрерывного совершенствования процесса и сокращения потерь внутри производственного цикла.</p><p><br />По аналогии с Waterfall каждое требование проходит ряд <strong>этапов обработки</strong>: анализ, проектирование, разработку, тестирование, документирование и т.д.&nbsp;Основное отличие в том, что любое требование может быть перенесено на следующий этап сразу, как только закончен предыдущий.</p><p><br />Kanban эффективно справляется с потоком <strong>незапланированной работы</strong>, то есть когда сложно оценить какие изменения и сколько их будет завтра.</p><p>&nbsp;</p><p><strong>Время цикла</strong> (с момента начала работы над требованием до момента завершения всех работ по нему)&nbsp;является одной из основных метрик и непрерывно уменьшается за счет внедрения эффективных практик разработки.</p><p>&nbsp;</p><p>Kanban опирается на <strong>самоорганизацию</strong> членов&nbsp;команд, активное взаимодействие между ними, интенсивный обмен опытом и знаниями.",
    "kanban-backlog": "<strong>Визуализируйте процесс</strong> производства в виде столбцов на Kanban-доске, отражающих этапы обработки требований в вашем проекте. Удаляйте и добавляйте столбцы по мере выявления этапов производства.</p><p>&nbsp;</p><p>Задайте ограничения для количества <strong>незавершенной работы</strong>&nbsp;(Work In Progress) на каждом из этапов производства. Установите ограничение из рассчета количества задач, которые переносятся в следующий этап в течение одного дня. Перед <strong>узким местом</strong> в вашем процессе выстроится очередь задач.</p><p>&nbsp;</p><p>Пользовательские требования&nbsp;попадают в <strong>бэклог</strong> и располагаются в нем в порядке приоритета их реализации. Для документирования требований вы можете воспользоваться форматом&nbsp;<a href=\"http://devprom.ru/glossary/%D0%98%D1%81%D1%82%D0%BE%D1%80%D0%B8%D1%8F-%D0%BF%D0%BE%D0%BB%D1%8C%D0%B7%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8F\">пользовательских&nbsp;историй</a>&nbsp;или описывать более подробные требования в формате <strong>вариантов использования</strong> и т.п.",
    "kanban-taskboard": "Электронная <strong>Kanban доска</strong>&nbsp;в режиме реального времени&nbsp;показывает кто из участников команды какими задачами занят. В&nbsp;отличии от <strong>физической доски</strong> вам не нужно тратить время на запись и расшифровку текста задачи, карточки задач не окажутся случайно на полу, можно работать с распределенными командами и удаленными сотрудниками.</p><p>&nbsp;</p><p>Участники команды берут в работу наиболее <strong>приоритетную&nbsp;карточку</strong>&nbsp;с описанием требований, обработанную&nbsp;на предыдущем этапе. Затем выполняют необходимую работу и переносят карточку&nbsp;в&nbsp;столбец с карточками,&nbsp;готовыми к очередному <strong>этапу обработки</strong>.</p><p>&nbsp;</p><p>Если в работе над требованием необходимо задействовать <strong>более одного участника</strong>, то можно создать дополнительные задачи из контекстного меню для карточки.</p><p>&nbsp;</p><p>Если для какого-то из этапов постоянно требуется создавать задачи определенного типа, то в справочнике типов задач определите для них&nbsp;характерные состояния&nbsp;пожелания. На форме&nbsp;настройки&nbsp;этапа (столбца) в&nbsp;поле \"Настройка полей формы\" добавьте атрибут \"Задачи\", после этого система сама предложит создать задачи, когда вы перенесете карточку на данный этап.",
    "kanban-leadtime": "Время решения пожелания или <strong>время цикла</strong>, то есть от момента начала работы над требованием, до момента завершения всех работ по нему, является основной характеристикой вашего процесса разработки.&nbsp;</p><p>&nbsp;</p><p>При помощи графика <strong>среднего времени решения</strong> следите за тем, чтобы скользящее среднее (среднее значение за предыдущую неделю) непрерывно <strong>снижалось</strong>. Это будет означать, что команда работает над своей эффективностью и повышает продуктивность.</p><p>&nbsp;</p><p>Для реализации нового требования и исправления ошибки часто требуется различное время. Изучайте показатели среднего времени цикла для <strong>разных типов</strong> пожеланий (требований) при помощи фильтров&nbsp;\"Тип\" и \"Приоритет\".",
    "kanban-leadtime-details": "Для <strong>детального анализа</strong> этапов обработки, которые занимают основное время, либо занимают времени больше, чем должны, используйте отчет \"Детализация времени цикла\".</p><p>&nbsp;</p><p>В списке отображаются пожелания (пользовательские&nbsp;требования) с указанием времени нахождения в <strong>каждом из состояний</strong>.&nbsp;Используйте <strong>фильтры</strong> для выбора пожеланий определенного типа, приоритета или используя другие критерии выбора.</p><p>&nbsp;</p><p>Вы можете открыть <strong>форму пожелания</strong> и на вкладке \"Жизненный цикл\" увидеть полную картину перехода пожелания между состояниями, с указанием даты, пользователя и продолжительности нахождения в каждом из состояний.",
    "kanban-retro": "После очередного релиза возьмите за правило&nbsp;проводить&nbsp;<strong>ретроспективу</strong>.</p><p>&nbsp;</p><p>Ретроспектива - это действенный способ постепенно улучшать команду, процесс и качество продукта.&nbsp;На ретроспективах анализируются результаты предыдущих шагов,&nbsp;выявляются текущие проблемы и&nbsp;вырабатываются новые шаги по их решению.</p><p>&nbsp;</p><p><strong>Результаты</strong> ретроспективы в форме записей и фотографий сохраняйте в <strong>базе знаний проекта</strong> для напоминания всем участникам о договоренностях, а также для анализа эффективности ваших действий по решению проблем.",
    "kanban-modelling": "В процессе реализации пользовательских требований&nbsp;<strong>документируйте</strong> важные технические решения в форме фотографий, скриншотов,&nbsp;UML-диаграмм, формул, алгоритмов или в произвольной текстовой форме.</p><p>&nbsp;</p><p>Это позволит любому члену команды освежить в памяти детали <strong>технического решения</strong>, а новым членам команды быстрее войти в курс дела. Из контекстного меню для карточки <strong>создайте требование&nbsp;</strong>и запишите в нем все важные технические решения.</p><p>&nbsp;</p><p>Требование удобно <strong>обсудить</strong> со всеми заинтересованными лицами при помощи комментариев.",
    "kanban-testing": "Разрабатывайте <strong>тестовую документацию</strong> по ходу реализации пожеланий.&nbsp;Затем она будет использована для <strong>ручного тестирования</strong>, например, для смоук, регрессионного или исследовательского тестирования перед выпуском релиза.</p><p>&nbsp;</p><p>Просто создайте <strong>тестовый сценарий</strong>, связанный с историей, и включите его в подходящий&nbsp;<strong>тест-план</strong>.&nbsp;Особенно востребованные тестовые&nbsp;сцеарии&nbsp;можно включить в несколько тест-планов.</p><p>&nbsp;</p><p><strong>Выполняйте тестирование</strong> пожеланий или тест-плана, отчет будет доступен&nbsp;всей команде. Если <strong>обнаружатся ошибки</strong>, то отклоняйте пожелание или добавляйте новые ошибки при тестировании тест-плана.</p><p>&nbsp;</p><p>Тестовые сценарии будут <strong>вязаны</strong> с исходными требованиями&nbsp;автоматически. Когда требование изменится приложение сообщит вам о том, что необходимо <strong>обновить </strong>и тестовую документацию.",
    "kanban-code": "Подключите репозиторий <strong>исходного кода</strong>&nbsp;к&nbsp;проекту, чтобы видеть все <strong>коммиты</strong> и собирать статистику по изменениям в исходном коде.&nbsp;Организуйте легковесное <strong>ревью кода</strong>.</p><p>&nbsp;</p><p><strong>Связывайте</strong> задачи или пожелания с коммитами исходного кода, это позволит&nbsp;выяснить как именно была реализована история, а также упростит разработчикам изменение статуса задачи или списание времени."
};
var tc = underi18n.MessageFactory(resource);

var kanbanTourTitle = 'Kanban';
var kanbanTourTemplate = "<div class='popover tour' style='max-width:550px;'>"+
    "<div class='arrow'></div>"+
    "<h3 class='popover-title' style='color:#fff;background-color: #428bca;border: 2px solid #428bca;'></h3>"+
    "<div class='popover-content'></div>"+
    "<div class='popover-navigation text-center' style='padding: 9px 14px;'>"+
    "<button class='btn btn-default pull-left' data-role='prev'><span class='ui-button-text'><i class='icon-backward'></i></span></button>"+
    "<button class='btn btn-default' data-role='stop' title='"+ptt('end-tour')+"'><span class='ui-button-text'><i class='icon-stop'></i></span></button>"+
    "<button class='btn btn-default pull-right' data-role='next'><span class='ui-button-text'><i class='icon-forward'></i></span></button>"+
    "<div class='clearfix'></div>"+
    "</div>"+
    "</div>";

var kanbanSteps = [
    {
        orphan: true,
        content: tc('kanban-intro'),
        template: kanbanTourTemplate.replace('550px', '650px'),
        title: kanbanTourTitle
    },
    {
        element: "table.board-table tr:eq(0) th:eq(2) .btn-group",
        content: tc('kanban-backlog'),
        placement: 'bottom',
        path: '/pm/%project%/module/kanban/requests/kanbanboard',
        title: kanbanTourTitle
    },
    {
        element: "table.board-table tr:eq(1) td.board-column:eq(2)",
        content: tc('kanban-taskboard'),
        placement: 'right',
        path: '/pm/%project%/module/kanban/requests/kanbanboard',
        title: kanbanTourTitle
    },
    {
        element: ".table-header a[uid='type']",
        content: tc('kanban-leadtime'),
        placement: 'right',
        path: '/pm/%project%/module/kanban/avgleadtime/avgleadtime?report=avgleadtime',
        title: kanbanTourTitle
    },
    {
        element: "ul#menu_favs a[uid='workflowanalysis']",
        content: tc('kanban-leadtime-details'),
        placement: 'right',
        path: '/pm/%project%/module/customs/workflowanalysis/workflowanalysis?report=workflowanalysis',
        title: kanbanTourTitle
    },
    {
        element: "div.wysiwyg-text[attributename='Caption']",
        content: tc('kanban-retro'),
        placement: 'bottom',
        path: '/pm/%project%/knowledgebase/tree?area=favs',
        title: kanbanTourTitle
    }
];

if ( mode_reqs ) {
    kanbanSteps.push(
        {
            element: "table.table-inner tr:eq(1) td:eq(3)",
            content: tc('kanban-modelling'),
            placement: 'bottom',
            path: '/pm/%project%/module/requirements/docs?area=reqs',
            title: kanbanTourTitle
        }
    );
}
if ( mode_qa ) {
    kanbanSteps.push(
        {
            element: "table.table-inner tr:eq(2) td:eq(3)",
            content: tc('kanban-testing'),
            placement: 'bottom',
            path: '/pm/%project%/module/testing/results/testsofreleasereport?report=testsofreleasereport&basemodule=testing/results&&area=qa',
            title: kanbanTourTitle
        }
    );
}
if ( mode_code ) {
    kanbanSteps.push(
        {
            element: "table.table-inner tr:eq(1) td:eq(3)",
            content: tc('kanban-code'),
            placement: 'bottom',
            path: '/pm/%project%/module/sourcecontrol/revision?area=dev',
            title: kanbanTourTitle
        }
    );
}

kanbanSteps.push(
    {
        orphan: true,
        duration: 1,
        path: '/pm/%project%/module/kanban/requests/kanbanboard',
    }
);

toursQueue.unshift(new Tour({
    steps: kanbanSteps,
    name: "KanbanTour",
    duration: 1000 * 120,
    template: kanbanTourTemplate,
    onShow: function(tour) {
        $('.with-tooltip').popover('disable');
    },
    onEnd: function(tour) {
        $('.with-tooltip').popover('enable');
        startTour();
    },
    onShown: function(tour) {
        $('[data-role=stop]').click( function() {
            tour.end();
        });
    }
}));