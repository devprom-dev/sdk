var tc = underi18n.MessageFactory({
    "scrum-intro": "<strong>Scrum</strong> предлагает лучший способ организации команды для поиска наиболее ожидаемого результата, путем ритмичного выполнения понятных порций задач и учета обратной связи от заказчика (владельца продукта).</p><p> </p><p>1. Владелец продукта формулирует ожидаемый результат при помощи элементов бэклога, устанавливает им приоритет и отвечает на уточняющие вопросы команды. Команда изучает и оценивает элементы бэклога. Приоритетные и понятные элементы бэклога планируются в ближайший спринт (итерацию).</p><p> </p><p>2. На этапе планирования команда распределяет внутри себя задачи, которые позволяют выполнить (реализовать) элементы бэклога. О возникающих трудностях, а также о текущем состоянии дел члены команды обмениваются на Scrum-митингах ежедневно. Владелец продукта должен быть всегда доступен для команды, чтобы дать важные уточнения, на возникающие вопросы.</p><p> </p><p>3. По завершении спринта команда демонстрирует результат владельцу продукта. Элементы бэклога требующие дополнительной работы возвращаются в бэклог. Команда старается непрерывно улучшать свои результаты путем проведения ретроспектив. На ретроспективах открыто говорят о проблемах в работе команды, способах их решения и конкретных шагах на этом пути.</p><p> </p><p>4. Производительность команды измеряется ее скоростью. Скорость команды позволяет прогнозировать сроки, в которые будут решены элементы бэклога.",
    "scrum-backlog": "Все элементы бэклога располагаются в нем в порядке приоритета их выполнения.</p><p> </p><p>Крупные элементы бэклога владелец продукта преобразует в эпики, которые затем декомпозирует на элементы поменьше.</p><p> </p><p>Эпики позволяют отслеживать прогресс в достижении результата, а также связывать элементы бэклога с бизнес-целями.",
    "scrum-planning": "В начале каждого спринта команда собирается на сессию планирования. Чтобы команда была сфокусирована на целях спринта, запишите их в описании спринта.</p><p> </p><p>Из бэклога выбираются самые приоритетные элементы, уточняется описание, согласуются критерии завершенности, критерии приемки и фиксируются в тексте истории.</p><p> </p><p>Нужно перетащить карточку в состояние \"Запланировано в спринт\" и распределить задачи среди членов команды.</p><p> </p><p>В спринт можно включить столько элементов бэклога, сколько позволяет его емкость, определяемая начальной скоростью команды и продолжительностью спринта.",
    "scrum-grooming": "Груминг позволяет подготовить элементы бэклога к планированию и сформировать план на несколько ближайших спринтов.</p><p> </p><p>В течение текущего спринта участники команды изучают новые приоритетные элементы бэклога, стараются их оценить и задают уточняющие вопросы в комментариях.</p><p> </p><p>Команда оценивает элементы бэклога в относительных единицах, например, Story Points или в размерах одежды (X, L, M, S).</p><p> </p><p>На очередной сессии планирования команда работает с уже более понятными элементами бэклога, за счет чего сессии становятся короче и менее изнурительны.",
    "scrum-taskboard": "Электронная доска задач в режиме реального времени показывает в каких статусах находятся задачи, кто из участников команды какими задачами занят.</p><p> </p><p>Участники команды берут себе свободные задачи, затем выполняют их отмечая эти действия простым перетаскиванием карточек между столбцами.</p><p> </p><p>В отличии от физической доски вам не нужно тратить время на запись и расшифровку текста задачи, карточки задач не окажутся случайно на полу, можно работать с распределенными командами и удаленными сотрудниками.",
    "scrum-burndown": "Для контроля за сроками спринта команда использует простой и наглядный инструмент - график Burndown (сжигания работ).</p><p> </p><p>Красная линия показывает какой должен оставаться объем незавершенной работы на каждый день спринта. Зеленая линия указывает на фактический объем незавершенной работы.</p><p> </p><p>Все что нужно делать команде - следить за тем, чтобы зеленая линия находилась под красной.",
    "scrum-velocity": "В течение проекта команда следит за графиком изменения ее скорости.</p><p> </p><p>Скорость команды постепенно должна увеличиваться за счет более качественной оценки элементов бэклога и устранения проблем, мешающих эффективной работе команды.",
    "scrum-retro": "После каждого спринта команда проводит ретроспективу.</p><p> </p><p>Ретроспектива - это действенный способ постепенно улучшать команду, процесс и качество результата. На ретроспективах анализируются результаты предыдущих шагов, выявляются текущие проблемы и вырабатываются новые шаги по их решению.</p><p> </p><p>Результаты ретроспективы в форме записей и фотографий сохраняйте в базе знаний проекта для напоминания всем участникам о договоренностях, а также для анализа эффективности ваших действий по решению проблем."
});

var scrumTourTitle = 'Scrum';
var scrumTourId = 'ScrumTour';
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

var scrumSteps = [
    {
        orphan: true,
        content: tc('scrum-intro'),
        template: scrumTourTemplate.replace('550px', '650px'),
        title: scrumTourTitle
    },
    {
        element: "table.table-inner tr:eq(2)",
        content: tc('scrum-backlog'),
        placement: 'bottom',
        path: '/pm/%project%/issues/list/productbacklog?report=productbacklog&basemodule=issues-backlog&&area=favs&tour='+scrumTourId,
        title: scrumTourTitle
    },
    {
        element: "tr.row-cards td:eq(1)",
        content: tc('scrum-grooming'),
        placement: 'bottom',
        path: '/pm/%project%/issues/board/iterationplanningboard?report=iterationplanningboard&basemodule=issues-board&tour='+scrumTourId,
        title: scrumTourTitle
    },
    {
        element: "tr.row-cards td:eq(1)",
        content: tc('scrum-planning'),
        placement: 'bottom',
        path: '/pm/%project%/issues/board?area=favs&tour='+scrumTourId,
        title: scrumTourTitle
    },
    {
        element: "table.board-table tr:eq(5) td.board-column:eq(0)",
        content: tc('scrum-taskboard'),
        placement: 'right',
        path: '/pm/%project%/tasks/board/tasksboardforissues?report=tasksboardforissues&basemodule=tasks-board&area=favs&tour='+scrumTourId,
        title: scrumTourTitle
    },
    {
        orphan: true,
        content: tc('scrum-burndown'),
        placement: 'right',
        path: '/pm/%project%/tasks/chart/iterationburndown?report=iterationburndown&basemodule=tasks-chart&&area=favs&tour='+scrumTourId,
        title: scrumTourTitle
    },
    {
        orphan: true,
        content: tc('scrum-velocity'),
        placement: 'right',
        path: '/pm/%project%/module/scrum/velocitychart/velocitychart?report=velocitychart&basemodule=scrum/velocitychart&&area=favs&tour='+scrumTourId,
        title: scrumTourTitle
    },
    {
        element: "h4.title-cell",
        content: tc('scrum-retro'),
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
        steps: scrumSteps,
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