package ru.devprom.tests;

import org.testng.Assert;
import org.testng.annotations.Test;
import ru.devprom.helpers.DataProviders;
import ru.devprom.items.KanbanTask;
import ru.devprom.items.Project;
import ru.devprom.items.Template;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.kanban.KanbanPageBase;
import ru.devprom.pages.kanban.KanbanTaskBoardPage;
import ru.devprom.pages.kanban.KanbanTaskNewPage;
import ru.devprom.pages.kanban.KanbanTaskViewPage;
import ru.devprom.pages.project.logs.LogPage;

@Test(description = "S-5014 Отмена изменений")
public class ChangesCancelTest extends ProjectTestBase
{
    public void changesCancel()throws InterruptedException {
        Project webTest = new Project("AutoActionTest" + DataProviders.getUniqueString(),
                "AutoActionTest" + DataProviders.getUniqueStringAlphaNum(),
                new Template(this.kanbanTemplateName));

        // создаем проект для изоляции теста
        PageBase page = new PageBase(driver);
        ProjectNewPage pnp = page.createNewProject();

        KanbanPageBase firstPage = (KanbanPageBase) pnp.createNew(webTest);

        // переходим на страницу создания пожеланий в проекте Канбан и создаем пожелание
        KanbanTask wish = new KanbanTask("CheckIssue"+DataProviders.getUniqueString());
        KanbanTaskNewPage checkTask = firstPage.gotoKanbanBoard().goToAddWish();
        checkTask.addName(wish.getName());
        checkTask.addDescription(DataProviders.getUniqueString());
        checkTask.selectState("Разработка");

        //создаем связанную задачу
        checkTask.setAddTasks("X"+DataProviders.getUniqueStringAlphaNum(),"","Тестирование");
        checkTask.save();

        //устанавливаем ID для дальнейших манипуляций
        KanbanTaskBoardPage ktbp = firstPage.gotoKanbanBoard();
        wish.setId(ktbp.getIDTaskByName(wish.getName()));

        //переходим к пожеланию и удаляем его
        KanbanTaskViewPage ktvp = checkTask.openTask(wish.getId());
        ktvp.deleteIssue();
        Thread.sleep(6000);

        //переходим в Журнал изменений и отменяем удаление
        LogPage lp = firstPage.gotoLogs();
        Thread.sleep(6000);
        lp.changeCancel();

        //переходим на доску и смотрим, что наше пожелание восстановилось
        Thread.sleep(6000);
        KanbanTaskBoardPage ktbp1 = firstPage.gotoKanbanBoard();
        Assert.assertTrue(ktbp.isTextPresent(ktbp.getIDTaskByName(wish.getName())));
    }
}
