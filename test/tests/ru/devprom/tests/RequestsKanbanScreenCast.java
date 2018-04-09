/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package ru.devprom.tests;

import java.io.File;
import java.util.ArrayList;
import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;
import org.testng.annotations.BeforeClass;
import org.testng.annotations.Test;
import ru.devprom.helpers.DataProviders;
import ru.devprom.helpers.DateHelper;
import ru.devprom.items.KanbanTask;
import ru.devprom.items.Project;
import ru.devprom.items.Release;
import ru.devprom.items.Requirement;
import ru.devprom.items.Template;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.kanban.KanbanPageBase;
import ru.devprom.pages.kanban.KanbanTaskNewPage;
import ru.devprom.pages.kanban.KanbanTasksPage;
import ru.devprom.pages.project.ReleaseNewPage;
import ru.devprom.pages.project.requirements.RequirementNewPage;
import static ru.devprom.tests.TestBase.FILELOG;
import ru.devprom.helpers.Configuration;
import ru.devprom.items.TestScenario;
import ru.devprom.pages.kanban.KanbanBuildsPage;
import ru.devprom.pages.kanban.KanbanNewBuildPage;
import ru.devprom.pages.kanban.KanbanSubTaskEditPage;
import ru.devprom.pages.kanban.KanbanTaskBoardPage;
import ru.devprom.pages.kanban.KanbanTaskEditPage;
import ru.devprom.pages.kanban.KanbanTaskViewPage;
import ru.devprom.pages.project.requirements.RequirementChangesHistoryPage;
import ru.devprom.pages.project.requirements.RequirementEditPage;
import ru.devprom.pages.project.requirements.RequirementViewPage;
import ru.devprom.pages.project.requirements.RequirementsPage;
import ru.devprom.pages.project.requirements.RequirementsTracePage;
import ru.devprom.pages.project.tasks.TaskEditPage;
import ru.devprom.pages.project.tasks.TasksBoardPage;
import ru.devprom.pages.project.testscenarios.TestScenarioEditPage;
import ru.devprom.pages.project.testscenarios.TestScenarioNewPage;
import ru.devprom.pages.project.testscenarios.TestScenarioTestingPage;
import ru.devprom.pages.project.testscenarios.TestScenarioViewPage;
import ru.devprom.pages.project.testscenarios.TestScenariosPage;
import ru.devprom.pages.project.testscenarios.TestSpecificationsPage;

/**
 *
 * @author лена
 */
public class RequestsKanbanScreenCast extends ProjectTestBase{
    Project kanbanProject;
    int timeOut = 1800;
    int bigTimeOut = 3000;
    KanbanTask wish1;
    KanbanTask wish2;
    Requirement requirement1;
    Requirement requirement2;
    String taskName1 = "Реализовать по требованиям";
    String taskName2 = "Покрыть автоматическими тестами";
    TestScenario test1;
    TestScenario test2;
	
    /**
	 * Сценарий обучающих роликов: легковесные требования в Kanban
	 */
    @Test(description="S-3316")
	public void createKanbanProject() throws InterruptedException{
            KanbanPageBase kanbanPage = priorityStage();
            KanbanTaskViewPage viewTaskPage = designStage(kanbanPage);
            developmentStage(viewTaskPage, kanbanPage);
            testingStage(kanbanPage);
            editRequirementsStage(viewTaskPage, kanbanPage);
            controlStage(viewTaskPage);
	}
        
	//создает новый проект
        private KanbanPageBase createNewKanbanProject(boolean isGlobal) throws InterruptedException{
            try{
            Thread.sleep(timeOut);
            PageBase basePage = new PageBase(driver);
            basePage.clickLink();
            ProjectNewPage newProjectPage = basePage.clickNewProject();
            Thread.sleep(timeOut);
            Template kanbanTemplate = new Template(this.kanbanTemplateName);
            String p = DataProviders.getUniqueString();
            Project project = new Project("Разработка по Kanban", "kanban" + p, kanbanTemplate);
            project.setDemoData(true);
            if (isGlobal) this.kanbanProject = project;
            KanbanPageBase kanbanPage = (KanbanPageBase) newProjectPage.createNew(project);
            Thread.sleep(timeOut);
            FILELOG.debug("Created new project " + project.getName());
            return kanbanPage;
        }
            catch(InterruptedException e)
            {
                FILELOG.debug("Error in creating new project" + e);
                return null;
            }
        }
//создает новое пожелание
    private void createWish1(KanbanPageBase kanbanPage) throws InterruptedException {
        try
        {
            KanbanTaskNewPage kanbanNewWishPage = kanbanPage.goToAddWish();
            Thread.sleep(timeOut);
            wish1 = new KanbanTask("Обеспечить авторизованный доступ к приложению");
            wish1.setDescription("Общие ресурсы доступны пользователям и группам помимо владельца ресурса, и они должны быть защищены от несанкционированного использования. В модели управления доступом Windows Server 2012 и Windows 8 пользователи и группы (субъекты безопасности) представлены уникальными идентификаторами безопасности (SID), и им выдаются права и разрешения, информирующие операционную систему о том, что может делать каждый из них. У каждого ресурса есть свой владелец, выдающий разрешения субъектам безопасности. В ходе проверки управления доступом эти разрешения рассматриваются для определения того, какие субъекты безопасности могут получать доступ к ресурсу и каким образом.");
            wish1.setPriority("Высокий");
            kanbanNewWishPage.addName(wish1.getName());
            kanbanNewWishPage.addDescription(wish1.getDescription());
            kanbanNewWishPage.selectPriority(wish1.getPriority());
            kanbanNewWishPage.saveTaskFromBoard(wish1);
            //idWish1 = newWish.getId();
            FILELOG.debug("Created wish1 ");
    }
            catch(InterruptedException e)
            {
               FILELOG.debug("Error in creating wish1" + e); 
            }
    }
//создает новое пожелание
    private void createWish2(KanbanPageBase kanbanPage) {
         try
        {
            KanbanTaskNewPage kanbanNewWishPage = kanbanPage.goToAddWish();
            Thread.sleep(timeOut);
            wish2 = new KanbanTask("Предоставить пользователю возможность изменить параметры доступа к приложению");
            wish2.setDescription("Владельцы объектов обычно предоставляют разрешения группам безопасности, а не отдельным пользователям. Пользователи и компьютеры, добавляемые к существующим группам, пользуются разрешениями соответствующей группы. Если объект (скажем, папка) может содержать другие объекты (скажем, вложенные папки и файлы), он именуется контейнером. Отношения между контейнером и его содержимым в иерархии объектов представляют из себя контейнер в роли родительского объекта и объект в контейнере в роли дочернего, наследующего настройки управления доступом родительского. Владельцы объектов часто определяют разрешения для объектов контейнеров вместо отдельных дочерних объектов, чтобы упростить управление доступом.");
             kanbanNewWishPage.addName(wish2.getName());
            kanbanNewWishPage.addDescription(wish2.getDescription());
            kanbanNewWishPage.saveTaskFromBoard(wish2);
            //idWish2 = newWish.getId();
            FILELOG.debug("Created wish2 " + wish2.getName());
    }
            catch(InterruptedException e)
            {
               FILELOG.debug("Error in creating wish2" + e); 
            }
    }
//создает новый релиз
    private void createRelease() {
        ReleaseNewPage releaseNewPage = (new KanbanPageBase(driver)).clickNewRelease();
        Release release = new Release("2", "Цели релиза: завершить реализацию базового варианта фукнциональности авторизации пользователей" , DateHelper.getDayAfter(7), DateHelper.getDayAfter(37));
        releaseNewPage.createRelease(release);
        FILELOG.debug("Created new release " + release.getNumber());
    }

    //создает новое требование
    private void createNewRequirement1(KanbanPageBase kanbanPage) {
        try {
			Thread.sleep(timeOut * 4);
		} catch (InterruptedException e) {
		}
        RequirementNewPage requirementNewPage = kanbanPage.clickToContextSubMenuItem(wish1.getId(), "Создать", "Требование");
        Requirement parentRequirement = new Requirement("Варианты использования");
        requirement1 = new Requirement("Авторизованный доступ");
        requirement1.setTemplateName("OpenUP");
        requirement1.setParentPage(parentRequirement);
        requirement1.setTemplateName("Вариант использования (OpenUP)");
        requirementNewPage.createFromBoard(requirement1, new File(Configuration.getPathToRequestKanbanImage()));
        FILELOG.debug("Created new requirement " + requirement1.getName());
    }

    //создает новое требование c изображением
    private void createNewRequirement2(KanbanPageBase kanbanPage) {
        try {
			Thread.sleep(timeOut * 4);
		} catch (InterruptedException e) {
		}
        RequirementNewPage requirementNewPage = kanbanPage.clickToContextSubMenuItem(wish1.getId(), "Создать", "Требование");
        Requirement parentRequirement = new Requirement("Варианты использования");
        requirement2 = new Requirement("Алгоритм авторизации");
        requirement2.setParentPage(parentRequirement);
        String uml = "Alice -> Bob: Authentication Request\n" +
                "Bob --> Alice: Authentication Response\n" +
                "\n" +
                "Alice -> Bob: Another authentication Request\n" +
                "Alice <-- Bob: another authentication Response";
     //   File file = new File(Configuration.getPathToRequestKanbanImage());
        requirementNewPage.createWithUML(requirement2,uml);
        FILELOG.debug("Created new requirement " + requirement2.getName());
    }
    
    //перевод пожелания в разработку и создание задач
    private void doDevelopment(KanbanTaskViewPage viewTaskPage) {
        KanbanTask task1 = new KanbanTask(taskName1);
        KanbanTask task2 = new KanbanTask(taskName2);
        viewTaskPage.doDevelopment(task1, task2);
        FILELOG.debug("Created two new tasks: " + task1.getName()+ " and " + task2.getName());
    }
    
    ///создает новый тестовый сценарий  для пожелания 1
    private void createTestScenario1(RequirementsTracePage requirementTracePage) {
        requirement1.setId(requirementTracePage.getIdByName(requirement1.getName()));
        TestScenarioNewPage tnp = requirementTracePage.gotoCreateScenario(requirement1.getClearId());
        TestScenario parentTestScenary = new TestScenario("Смоук тестирование");
        test1 = new TestScenario("Авторизованный доступ");
        test1.setParentPage(parentTestScenary);
        test1.setTemplate("Приемочный сценарий");
        tnp.createNewScenarioWithTemplate(test1);
        FILELOG.debug("Created new test scenario" + test1.getName());
    }
    
    //создает новый тестовый сценарий  для пожелания 1
     private void createTestScenario2(RequirementsTracePage requirementTracePage) {
          requirement2.setId(requirementTracePage.getIdByName(requirement2.getName()));
          //  kanbanPage.clickToContextSubMenuItem(wish1.getId(), "Создать", "Тестовый сценарий");
            requirementTracePage.gotoCreateScenario(requirement2.getClearId());   
            TestScenario parentTestScenary = new TestScenario("Функциональные тесты");
            TestScenario test2 = new TestScenario("Авторизованный доступ");
            test2.setParentPage(parentTestScenary);
            (new TestScenarioNewPage(driver)).createNewScenario(test2);
            FILELOG.debug("Created new test scenario" + test2.getName());
    }

    private KanbanPageBase priorityStage() throws InterruptedException {
         KanbanPageBase kanbanPage = createNewKanbanProject(true);
         createWish1(kanbanPage);
         createWish2(kanbanPage);
         return kanbanPage;
    }

    private KanbanTaskViewPage designStage(KanbanPageBase kanbanPage) throws InterruptedException {
        	kanbanPage.moveToAnotherRelease(wish1.getNumericId(), 0, "Анализ");
            createNewRequirement1(kanbanPage);
            createNewRequirement2(kanbanPage);
            Thread.sleep(timeOut);
            KanbanTaskViewPage viewTaskPage = kanbanPage.openTask(wish1.getId());
            String currentURL = driver.getCurrentUrl();
            RequirementViewPage requirementViewPage = viewTaskPage.openRequirement(requirement1.getName());
            Thread.sleep(bigTimeOut);
            requirementViewPage.openRootRequirement();
            driver.get(currentURL);
            Thread.sleep(timeOut);
            viewTaskPage.doAnalyseComplete("2ч");
            Thread.sleep(timeOut);
            return viewTaskPage;
    }

    private void developmentStage(KanbanTaskViewPage viewTaskPage, KanbanPageBase kanbanPage) throws InterruptedException {
            doDevelopment(viewTaskPage);
            KanbanTaskBoardPage taskBoardPage = kanbanPage.gotoTaskBoard();
            Thread.sleep(timeOut);
            String taskID1 = taskBoardPage.getIDTaskByName(taskName1);
            String taskID2 = taskBoardPage.getIDTaskByName(taskName2);
            taskBoardPage.clickToContextMenuItem(taskID1, "Взять в работу");
            KanbanSubTaskEditPage taskEditPage = taskBoardPage.doubleClickOnTask(taskName1);
            taskEditPage.openTabWishes();
            Thread.sleep(bigTimeOut);
            taskEditPage.cancel();
            kanbanPage.gotoCommits();
            Thread.sleep(bigTimeOut);
            kanbanPage.gotoTaskBoard();
            taskBoardPage.clickToContextMenuItem(taskID1, "Изменить");
            (new KanbanSubTaskEditPage(driver)).addSourceCode("60010c8");
            (new KanbanSubTaskEditPage(driver)).saveChanges();
            taskBoardPage.clickToContextMenuItem(taskID1, "Выполнить");
            taskBoardPage.setTime("2ч");
            taskBoardPage.clickToContextMenuItem(taskID2, "Выполнить");
            taskBoardPage.setTime("1ч");
            KanbanBuildsPage buildsPage = kanbanPage.gotoBuilds();
            KanbanNewBuildPage newBuildPage = buildsPage.clickNewBuild();
            newBuildPage.createNewBuild("3.4.1", "В сборку вошли изменения по всем основным веткам, по которым прошли тесты", "");
            viewTaskPage.gotoKanbanBoard();
            kanbanPage.moveToAnotherRelease(wish1.getNumericId(), 0, "Тестирование (");
            Thread.sleep(timeOut);
            kanbanPage.selectWish(wish1.getId());
            RequirementsTracePage requirementTracePage = kanbanPage.massRequirements();
            //здесь остановились
            createTestScenario1(requirementTracePage);
            createTestScenario2(requirementTracePage);
            Thread.sleep(timeOut);
            newBuildPage.gotoKanbanBoardFromBuildPage();
            kanbanPage.clickToContextMenuItem(wish1.getId(), "Начать тестирование");
            TestScenarioTestingPage testingPage = kanbanPage.startTesting("3.4.1");
            testingPage.passTest(test1);
            Thread.sleep(bigTimeOut);
            testingPage.failTest(test2);
            Thread.sleep(bigTimeOut);
            testingPage.rejectWish("Не прошел тест");
            Thread.sleep(timeOut);
             newBuildPage.gotoKanbanBoardFromBuildPage();
            Thread.sleep(timeOut);
            kanbanPage.openTask(wish1.getId());
            viewTaskPage.doDevelopmentComplete();
            Thread.sleep(timeOut);
            newBuildPage.gotoKanbanBoardFromBuildPage();
        
    }

    private void editRequirementsStage(KanbanTaskViewPage viewTaskPage, KanbanPageBase kanbanPage) throws InterruptedException {
        	kanbanPage.moveToAnotherRelease(wish2.getNumericId(), 1, "Анализ (");
        	Thread.sleep(timeOut);
            RequirementsPage requirementPage = kanbanPage.goRequirementReestr();
            requirement1.setId(requirementPage.getIdByName(requirement1.getName()));
            RequirementViewPage requirementViewPage = requirementPage.clickToRequirement(requirement1.getId());
            requirementViewPage.addContent(requirement1.getClearId(), "\nПоявился новый шаг основного сценария");
            FILELOG.debug("Text edited");
            requirementViewPage.clickLink();
            RequirementEditPage requirementEditPage = requirementViewPage.editRequirement(requirement1.getId());
            ArrayList<String> recList = new ArrayList<String>();
            recList.add(wish2.getId());
            requirement1.setRequests(recList);
            requirementEditPage.addSourseWish(requirement1);
            viewTaskPage.gotoKanbanBoard();
            kanbanPage.moveToAnotherRelease(wish2.getNumericId(), 1, "Анализ: готово");
            (new KanbanTaskBoardPage(driver)).setTimeRequirement("2ч");
            Thread.sleep(3000);
            kanbanPage.moveToAnotherRelease(wish2.getNumericId(), 1, "Разработка (");
            kanbanPage.clickSubmit();
            Thread.sleep(1000);
            kanbanPage.openTask(wish2.getId());
	            viewTaskPage.openRequirement(requirement1.getName());
            requirementViewPage.seeChanges(requirement1.getId());
            viewTaskPage.gotoKanbanBoard();
            kanbanPage.moveToAnotherRelease(wish2.getNumericId(), 1, "Разработка: готово");
            (new KanbanTaskBoardPage(driver)).setTimeRequirement("3ч");
            kanbanPage.moveToAnotherRelease(wish2.getNumericId(), 1, "Тестирование (");
            Thread.sleep(timeOut);
            kanbanPage.openTask(wish2.getId());
            viewTaskPage.openRequirement(requirement1.getName());
            TestScenariosPage testSuitsPage = requirementViewPage.menuTestSuit(requirement1.getId());
            TestSpecificationsPage testSuitViewPage = testSuitsPage.clickAttention();
            testSuitViewPage.addContent("\nДобавляем новые тестовые шаги для покрытия дополнительного шага сценария");
            TestScenarioEditPage testScenarioeditPage = testSuitViewPage.edit();
            testScenarioeditPage.addTrace(wish2.getId());
            testSuitViewPage.clickRepair();
            driver.navigate().refresh();
            viewTaskPage.gotoKanbanBoard();
            kanbanPage.openTask(wish2.getId());
            TestScenarioTestingPage testingPage = viewTaskPage.doStartTesting("3.4.1");
            testingPage.passTest(test1);
            Thread.sleep(3000);
            testingPage.readyWish(wish2.getId(),"1ч");
            viewTaskPage.gotoKanbanBoard();
    }

    private void controlStage(KanbanTaskViewPage viewTaskPage) throws InterruptedException {
        KanbanBuildsPage buildsPage = viewTaskPage.gotoBuilds();
        buildsPage.checkAll();
        buildsPage.clickRealized();
        Thread.sleep(10000);  
    }

    private void testingStage(KanbanPageBase kanbanPage) throws InterruptedException {
		kanbanPage.moveToAnotherRelease(wish1.getNumericId(), 0, "Тестирование (");
		Thread.sleep(timeOut);
		kanbanPage.moveToAnotherRelease(wish1.getNumericId(), 0, "Тестирование: готово");
		kanbanPage.clickSubmit();
		Thread.sleep(timeOut);
		kanbanPage.moveToAnotherRelease(wish1.getNumericId(), 0, "Документирование (");
		Thread.sleep(timeOut);
		kanbanPage.moveToAnotherRelease(wish1.getNumericId(), 0, "Готово (");
		kanbanPage.clickSubmit();
		Thread.sleep(timeOut);
    }
}
