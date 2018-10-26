var resource = {
    "kanban-intro": "Чтобы получить нужный результат вашей команде необходимо выполнять определенные последовательные действия над элементами бэклога, которые называются этапы производства. За каждый этап производства может отвечать один или несколько человек.</p><p> </p><p>Чтобы получать результат оперативно, каждый элемент бэклога должен проходить все этапы производства максимально быстро. Kanban позволяет эффективно справляться с потоком незапланированной работы.</p><p> </p><p>Ограничение незавершенной работы на каждом этапе производства позволяет выявлять узкие места в процессе. Регулярные ретроспективы позволяют вырабатывать шаги по устранению узких мест и повышению эффективности в работе команды.",
    "kanban-taskboard": "Электронная Kanban доска в показывает какие элементы бэклога на каком этапе производства находятся. Настройте названия столбцов (этапов), добавьте дополнительные или удалите лишние.</p><p><br>Участники команды самостоятельно берут в работу наиболее приоритетную карточку, обработанную на предыдущем этапе. Затем выполняют необходимую работу и переносят карточку в следующий столбец.</p><p> </p><p>Если в работе над элементом бэклога необходимо задействовать более одного участника, то можно создать дополнительные задачи из контекстного меню для карточки.",
    "kanban-leadtime": "Время цикла, то есть от момента начала работы над элементом бэклога, до момента завершения всех работ по нему, является основной метрикой вашего процесса. </p><p> </p><p>При помощи графика следите за тем, чтобы скользящее среднее (среднее значение за предыдущую неделю) со временем снижалось. Это будет означать, что команда работает над своей эффективностью и повышает продуктивность.</p><p> </p><p>Если в вашей работе встречаются задачи различной сложности, то используйте для них разные типы элементов бэклога и фильтруйте график по типам, чтобы увидеть разные показатели для элементов бэклога разных типов.",
    "kanban-retro": "Возьмите за правило - периодически проводить ретроспективу.</p><p> </p><p>Ретроспектива - это действенный способ постепенно улучшать команду, процесс и качество продукта. На ретроспективах анализируются результаты предыдущих шагов, выявляются текущие проблемы и вырабатываются новые шаги по их решению.</p><p> </p><p>Результаты ретроспективы в форме записей и фотографий сохраняйте в базе знаний для напоминания всем участникам о договоренностях, а также для анализа эффективности ваших действий по решению проблем."
};
var tc = underi18n.MessageFactory(resource);

var kanbanTourTitle = 'Kanban';
var kanbanTourId = 'KanbanTour';
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
        element: "table.board-table tr:eq(2) td.board-column:eq(2)",
        content: tc('kanban-taskboard'),
        placement: 'right',
        path: '/pm/%project%/module/kanban/requests/kanbanboard?tour='+kanbanTourId,
        title: kanbanTourTitle
    },
    {
        element: ".table-header a[uid='type']",
        content: tc('kanban-leadtime'),
        placement: 'right',
        path: '/pm/%project%/module/kanban/avgleadtime/avgleadtime?report=avgleadtime&tour='+kanbanTourId,
        title: kanbanTourTitle
    },
    {
        element: "h4.title-cell",
        content: tc('kanban-retro'),
        placement: 'bottom',
        path: '/pm/%project%/knowledgebase/tree?area=favs&tour='+kanbanTourId,
        title: kanbanTourTitle
    },
    {
        orphan: true,
        duration: 1,
        path: '/pm/%project%/module/kanban/requests/kanbanboard?tour='+kanbanTourId,
    }
];

if ( cookies.get(kanbanTourId+'Skip') == null ) {
    toursQueue.unshift(new Tour({
        backdrop: true,
        backdropPadding: 20,
        steps: kanbanSteps,
        name: kanbanTourId,
        duration: 1000 * 120,
        template: kanbanTourTemplate,
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