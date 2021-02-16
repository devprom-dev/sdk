var tc = underi18n.MessageFactory({
    "scrumban-backlog": "Все элементы бэклога располагаются в нем в порядке приоритета их выполнения.</p><p> </p><p>Крупные элементы бэклога владелец продукта преобразует в эпики, которые затем декомпозирует на элементы поменьше.</p><p> </p><p>Эпики позволяют отслеживать прогресс в достижении результата, а также связывать элементы бэклога с бизнес-целями.",
    "scrumban-planning": "Срочные элементы бэклога сразу идут в работу, уточняются и выполняются. Элементы бэклога, детализирующие эпики, планируются и выполняются в спринтах.</p><p> </p><p>В начале каждого спринта команда собирается на сессию планирования. Чтобы команда была сфокусирована на целях спринта, запишите их в описании спринта.</p><p> </p><p>Из перечня готовых к выполнению выбираются самые приоритетные элементы, уточняется описание, согласуются критерии завершенности, критерии приемки и фиксируются в тексте истории.</p><p> </p><p>Элементы бэклога декомпозируются на задачи, чтобы распределить работу среди членов команды.</p><p> </p><p>В спринт можно включить столько элементов бэклога, сколько позволяет его емкость, определяемая начальной скоростью команды и продолжительностью спринта.",
    "scrumban-grooming": "Груминг позволяет подготовить элементы бэклога к планированию и сформировать план на несколько ближайших спринтов.</p><p> </p><p>В течение текущего спринта участники команды изучают новые приоритетные элементы бэклога, стараются их оценить и задают уточняющие вопросы в комментариях.</p><p> </p><p>Команда оценивает элементы бэклога в относительных единицах, например, в размерах одежды (X, L, M, S).</p><p> </p><p>На очередной сессии планирования команда работает с уже более понятными элементами бэклога, за счет чего сессии становятся короче и менее изнурительны.",
    "scrumban-taskboard": "Электронная доска задач в режиме реального времени показывает в каком состоянии находятся задачи, кто из участников команды какими задачами занят.</p><p> </p><p>Участники команды берут себе свободные задачи, затем выполняют их отмечая эти действия простым перетаскиванием карточек между столбцами.</p><p> </p><p>В отличии от физической доски вам не нужно тратить время на запись и расшифровку текста задачи, карточки задач не окажутся случайно на полу, можно работать с распределенными командами и удаленными сотрудниками.",
    "scrumban-burndown": "Для контроля за сроками спринта команда использует простой и наглядный инструмент - график Burndown (сжигания работ).</p><p> </p><p>Красная линия показывает какой должен оставаться объем незавершенной работы на каждый день спринта. Зеленая линия указывает на фактический объем незавершенной работы.</p><p> </p><p>Все что нужно делать команде - следить за тем, чтобы зеленая линия находилась под красной.",
    "scrumban-velocity": "В течение проекта команда следит за графиком изменения ее скорости.</p><p> </p><p>Скорость команды постепенно должна увеличиваться за счет более качественной оценки элементов бэклога и устранения проблем, мешающих эффективной работе команды.",
    "scrumban-leadtime": "Время цикла, то есть от момента начала работы над элементом бэклога, до момента завершения всех работ по нему, является дополнительной метрикой процесса.</p><p> </p><p>При помощи графика следите за тем, чтобы скользящее среднее (среднее значение за предыдущую неделю) со временем снижалось. Это будет означать, что команда работает над своей эффективностью и повышает продуктивность.</p><p> </p><p>Используйте график только для срочных элементов бэклога, не связанных с эпиками, которые реализуются в спринтах.",
    "scrumban-retro": "После каждого спринта команда проводит ретроспективу.</p><p> </p><p>Ретроспектива - это действенный способ постепенно улучшать команду, процесс и качество результата. На ретроспективах анализируются результаты предыдущих шагов, выявляются текущие проблемы и вырабатываются новые шаги по их решению.</p><p> </p><p>Результаты ретроспективы в форме записей и фотографий сохраняйте в базе знаний проекта для напоминания всем участникам о договоренностях, а также для анализа эффективности ваших действий по решению проблем."
});

var scrumTourTitle = 'Scrumban';
var scrumTourId = 'ScrumbanTour';
var scrumTourTemplate = "<div class='popover tour' style='max-width:550px;'>"+
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

var scrumbanSteps = [
    {
        element: "table.table-inner tr:last",
        content: tc('scrumban-backlog'),
        placement: 'bottom',
        path: '/pm/%project%/issues/list/productbacklog?report=productbacklog&basemodule=issues-backlog&&area=favs&tour='+scrumTourId,
        title: scrumTourTitle
    },
    {
        element: "table[uid=kanbanboard] tr.row-cards:eq(1) td:eq(1)",
        content: tc('scrumban-planning'),
        placement: 'right',
        path: '/pm/%project%/module/kanban/requests/kanbanboard?report=kanbanboard&basemodule=kanban/requests&area=favs&tour='+scrumTourId,
        title: scrumTourTitle
    },
    {
        element: "tr.row-cards td:eq(1)",
        content: tc('scrumban-grooming'),
        placement: 'bottom',
        path: '/pm/%project%/issues/board/iterationplanningboard?report=iterationplanningboard&basemodule=issues-board&tour='+scrumTourId,
        title: scrumTourTitle
    },
    {
        element: "table.board-table tr.row-cards:eq(0) td.board-column:eq(0)",
        content: tc('scrumban-taskboard'),
        placement: 'right',
        path: '/pm/%project%/tasks/board/tasksboardforissues?report=tasksboardforissues&basemodule=tasks-board&area=favs&tour='+scrumTourId,
        title: scrumTourTitle
    },
    {
        orphan: true,
        content: tc('scrumban-burndown'),
        placement: 'right',
        path: '/pm/%project%/tasks/chart/iterationburndown?report=iterationburndown&basemodule=tasks-chart&&area=favs&tour='+scrumTourId,
        title: scrumTourTitle
    },
    {
        orphan: true,
        content: tc('scrumban-velocity'),
        path: '/pm/%project%/module/scrum/velocitychart/velocitychart?report=velocitychart&basemodule=scrum/velocitychart&&area=favs&tour='+scrumTourId,
        title: scrumTourTitle
    },
    {
        orphan: true,
        content: tc('scrumban-leadtime'),
        path: '/pm/%project%/module/kanban/avgleadtime/avgleadtime?report=avgleadtime&tour='+scrumTourId,
        title: scrumTourTitle
    },
    {
        element: "h4.title-cell",
        content: tc('scrumban-retro'),
        placement: 'bottom',
        path: '/pm/%project%/knowledgebase/tree?area=favs&tour='+scrumTourId,
        title: scrumTourTitle
    },
    {
        orphan: true,
        duration: 1,
        path: '/pm/%project%/issues/board?area=favs&tour='+scrumTourId
    }
];

if ( cookies.get(scrumTourId+'Skip') == null ) {
    toursQueue.unshift(new Tour({
        backdrop: true,
        backdropPadding: 20,
        steps: scrumbanSteps,
        name: scrumTourId,
        duration: 1000 * 120,
        template: scrumTourTemplate,
        onShow: function(tour) {
            $('.with-tooltip').popover('disable');
        },
        onEnd: function(tour) {
            $('.with-tooltip').popover('enable');
            startNextTour();
        },
        onShown: function(tour) {
            $('[data-role=stop]').click( function() {
                tour.end();
            });
        }
    }));
}