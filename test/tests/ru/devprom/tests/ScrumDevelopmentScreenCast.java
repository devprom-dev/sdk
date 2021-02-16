/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package ru.devprom.tests;

import java.io.IOException;
import org.testng.annotations.Test;
import ru.devprom.helpers.Configuration;
import ru.devprom.helpers.CopyData;
import ru.devprom.helpers.DataProviders;
import ru.devprom.items.ProductFunction;
import ru.devprom.items.Project;
import ru.devprom.items.Release;
import ru.devprom.items.Requirement;
import ru.devprom.items.ScrumIssue;
import ru.devprom.items.ScrumTask;
import ru.devprom.items.Template;
import ru.devprom.items.TestScenario;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.project.IterationNewPage;
import ru.devprom.pages.project.functions.FunctionNewPage;
import ru.devprom.pages.project.kb.KnowledgeBasePage;
import ru.devprom.pages.project.requests.RequestPlanningPage;
import ru.devprom.pages.project.requests.RequestsBoardPage;
import ru.devprom.pages.project.requirements.RequirementNewPage;
import ru.devprom.pages.project.tasks.TaskCompletePage;
import ru.devprom.pages.project.tasks.TaskViewPage;
import ru.devprom.pages.project.tasks.WriteOfTimePage;
import ru.devprom.pages.project.testscenarios.StartTestingPage;
import ru.devprom.pages.project.testscenarios.TestScenarioNewPage;
import ru.devprom.pages.project.testscenarios.TestScenarioTestingPage;
import ru.devprom.pages.scrum.TasksBoardPage;
import ru.devprom.pages.scrum.BackLogPage;
import ru.devprom.pages.scrum.ScrumIssueNewPage;
import ru.devprom.pages.scrum.ScrumIssueViewPage;
import ru.devprom.pages.scrum.ScrumPageBase;
import ru.devprom.pages.scrum.ScrumTaskNewPage;

/**
 *
 * @author лена
 */
public class ScrumDevelopmentScreenCast extends ProjectTestBase{
     Project newProject;
    int timeOut = 1000;
    int bigTimeOut = 2000;
    
    ScrumIssue userStory1;
    ScrumIssue userStory2;
    ScrumIssue userStory3;
    
    ScrumTask task1;
    ScrumTask task2;
    ScrumTask task3;
    /**
	 * Сценарий обучающих роликов: разработка по Scrum
	 */
    @Test(description="S-3873")
	public void runScrumDevelopmentScreenCast() throws InterruptedException, IOException{
            requirementStage();
            planingStage();
            testingStage();
            metricsStage();
	}

    private void requirementStage() throws InterruptedException {
        ScrumPageBase scrumBasePage = createNewProject(true);
        BackLogPage backlogPage = scrumBasePage.gotoBackLog();
        ScrumIssueNewPage newIssuePage = backlogPage.addUserStory();
        userStory1 = new ScrumIssue(
        		"Работать с папками", 
        		"Высокий", 
        		"Как рекламодатель, я хочу, чтобы система позволяла создавать мне папки, чтобы я мог быстрее работать с большими списками объявлений.\n\nПользователь может создавать папки с тремя уровнями вложенности.");
        userStory1.setEpic("Классификация объявлений");
        newIssuePage.createIssueWithNewEpic(userStory1);
        driver.navigate().refresh();
        userStory1.setId(backlogPage.getIDByName(userStory1.getName()));
        
        backlogPage.addUserStory();
        userStory2 = new ScrumIssue("Регистрация пользователя", "Высокий", "Как гость я могу зарегистрироваться в системе для получения пользовательской учетной записи и последующей работы");
        newIssuePage.createIssueWithNewEpic(userStory2);
        driver.navigate().refresh();
        userStory2.setId(backlogPage.getIDByName(userStory2.getName()));
        
        backlogPage.addUserStory();
        userStory3 = new ScrumIssue("Срок действия паролей", "Высокий", "Как администратор я могу установить период истечения действия пароля, таким образом пользователи будут обязаны периодически менять пароли");
        userStory3.setEpic("Классификация объявлений");
        newIssuePage.createIssue(userStory3);
        driver.navigate().refresh();
        userStory3.setId(backlogPage.getIDByName(userStory3.getName()));
        RequestsBoardPage boardPage = scrumBasePage.gotoHistoryBoard();
        Release sprint = new Release("0", "1. Закончить инсталляцию окружения и получить первые результаты.\n2. Получить первые результаты по регистрации и настройкам.\n3. Увидеть на новые идеи по дизайну", "", "");
        IterationNewPage releaseNewPage = boardPage.versionChange("Спринт: 0");
        releaseNewPage.addNumber(sprint.getNumber());
        releaseNewPage.addDescription(sprint.getDescription());
        releaseNewPage.addVelocity("80");
        releaseNewPage.openBurnDown();
        Thread.sleep(3000);
        releaseNewPage.save();
        
        boardPage.moveToAnotherSection(userStory1.getNumericId(), 1, 1);
        boardPage.moveToAnotherSection(userStory3.getNumericId(), 2, 1);
        
        boardPage.clickToRequest(userStory2.getId());
        ScrumIssueViewPage viewIssuePage = new ScrumIssueViewPage(driver);
        FunctionNewPage newEpicPage = viewIssuePage.convertToEpic();
        ProductFunction epic = new ProductFunction("Регистрация пользователей");
        newEpicPage.createFunction(epic);
    }

    private ScrumPageBase createNewProject(boolean isGlobal) {
        try{
            Thread.sleep(timeOut);
            PageBase basePage = new PageBase(driver);
            basePage.clickLink();
            ProjectNewPage newProjectPage = basePage.clickNewProject();
            Thread.sleep(timeOut);
            Template requirementTemplate = new Template(this.scrumTemplateName);
            String p = DataProviders.getUniqueString();
            Project project = new Project("Разработка по Scrum", "scrum" + DataProviders.getUniqueStringAlphaNum(), requirementTemplate);
            if (isGlobal) this.newProject = project;
            ScrumPageBase scrumBasePage = (ScrumPageBase) newProjectPage.createNew(project);
            Thread.sleep(timeOut);
            FILELOG.debug("Created new project " + project.getName());
            return scrumBasePage;
        }
            catch(InterruptedException e)
            {
                FILELOG.debug("Error in creating new project" + e);
                return null;
            }
    }

    private void planingStage() throws InterruptedException {
    	RequestsBoardPage historyBoardPage = (new RequestsBoardPage(driver));
        historyBoardPage.moveToAnotherSection(userStory1.getNumericId(), "0", "Запланировано в спринт");
        RequestPlanningPage pp = (new RequestPlanningPage(driver)); 
        pp.addContent("Пользователь не может ввести имя меньше 3 и больше 20 символов");
        pp.addRate("20");
        task1 = new ScrumTask("Отрефакторить механизм добавления папок, чтобы позволять создавать вложенные папки вплоть до 3 уровня вложенности");
        task1.setType("Проектирование");
        task2 = new ScrumTask("Проапдейтить версию SDK для использования нового механизма локального хранения данных на устройстве");
        task2.setType("Разработка");
        task3 = new ScrumTask("Проверить историю");
        task3.setType("Тестирование");
        (new RequestPlanningPage(driver)).fillTask(1, task1.getName(), task1.getType(), "", 1.0);
        (new RequestPlanningPage(driver)).fillTask(2, task2.getName(), task2.getType(), "", 1.3);
        (new RequestPlanningPage(driver)).fillTask(3, task3.getName(), task3.getType(), "", 1.5);
        (new RequestPlanningPage(driver)).savePlannedOnBoard();
        
        TasksBoardPage taskBoard = (new ScrumPageBase(driver)).gotoTasksBoard();
        task1.setId(taskBoard.getIdByName(task1.getName()));
        taskBoard.clickToContextMenuItem(task1.getId(), "Взять в работу");
        taskBoard.clickToContextSubMenuItem(task1.getId(), "Создать", "Требование");
        RequirementNewPage createReqPage = new RequirementNewPage(driver);
        Requirement requirement = new Requirement("Алгоритм взаимодействия с подсистемой авторизации Facebook");
        Requirement parentReq = new Requirement("Модели данных");
        requirement.setParentPage(parentReq);
        String uml = "Alice -> Bob: Authentication Request\n" +
                "Bob --> Alice: Authentication Response\n" +
                "\n" +
                "Alice -> Bob: Another authentication Request\n" +
                "Alice <-- Bob: another authentication Response";
        createReqPage.createWithUMLWithNewParent(requirement, uml);
        Thread.sleep(3000);
        taskBoard.clickToContextMenuItem(task1.getId(), "Выполнить");
        (new TaskCompletePage(driver)).complete(2, "Задача выполнена");
        
        task2.setId(taskBoard.getIdByName(task2.getName()));
        taskBoard.clickToContextMenuItem(task2.getId(), "Взять в работу");
        taskBoard.clickToContextMenuItem(task2.getId(), "Выполнить");
        (new TaskCompletePage(driver)).complete(2, "Задача выполнена");
        Thread.sleep(3000);
    }

    private void testingStage() throws IOException, InterruptedException {
        TasksBoardPage taskBoard = new TasksBoardPage(driver);
        task3.setId(taskBoard.getIdByName(task3.getName()));
        taskBoard.clickToContextMenuItem(task3.getId(), "Взять в работу");
        taskBoard.clickToContextSubMenuItem(task3.getId(), "Создать", "Тестовый сценарий");
        TestScenario parentTestScenary = new TestScenario("Тестовые сценарии");
        TestScenario scenario = new TestScenario("Проверка условий авторизации");
        scenario.setParentPage(parentTestScenary);
        (new TestScenarioNewPage(driver)).createScenarioWithNewTestPlan(scenario);
        driver.navigate().refresh();
        taskBoard.clickToContextMenuItem(task3.getId(), "Начать тестирование");
        TestScenarioTestingPage testingPage = (new StartTestingPage(driver)).startTestWithNewData("1.3", "");
        scenario.setId(testingPage.getTestRunId());
        testingPage.fillCell("1", "4", "Ok");
        testingPage.fillCell("2", "4", "Ok");
        (new CopyData()).copyImage(Configuration.getPathToBugImage());
        testingPage.pasteToCell("3", "4");
        driver.navigate().refresh();
        testingPage.failTest(scenario);
        Thread.sleep(4000);
        ScrumTaskNewPage taskNewPage = testingPage.createTask();
        ScrumTask task4 = new ScrumTask("Исправить ошибку");
        task4.setType("Разработка");
        taskNewPage.createTask(task4);
        WriteOfTimePage writeofTimePage = testingPage.writeOfTime();
        writeofTimePage.writeOfTime("3ч", "Тестирование");
        (new ScrumPageBase(driver)).gotoTasksBoard();
        task4.setId(taskBoard.getIdByName(task4.getName()));
        taskBoard.clickToContextMenuItem(task4.getId(), "Взять в работу");
        TaskViewPage taskViewPage = taskBoard.openTask(task4.getId());
        taskViewPage.clickOnTestScenario(scenario.getId());
        Thread.sleep(3000);
        (new ScrumPageBase(driver)).gotoTasksBoard();
        taskBoard.clickToContextMenuItem(task4.getId(), "Выполнить");
        (new TaskCompletePage(driver)).complete(1, "Задача выполнена");
        Thread.sleep(3000);
        taskBoard.clickToContextMenuItem(task3.getId(), "Начать тестирование");
        (new StartTestingPage(driver)).startTestWithNewData("1.4", "");
        testingPage.passTest(scenario);
        Thread.sleep(3000);
        testingPage.openTask(task3.getId());
        TaskCompletePage taskCompletePage = taskViewPage.completeTask();
        taskCompletePage.complete(3, "Задача выполнена");
        (new ScrumPageBase(driver)).gotoHistoryBoard();
    }

    private void metricsStage() throws InterruptedException {
        (new ScrumPageBase(driver)).gotoBurnDown();
        Thread.sleep(3000);
        (new ScrumPageBase(driver)).gotoDevelopmentSpeed();
        Thread.sleep(3000);
        KnowledgeBasePage knowledgeBasePage = (new ScrumPageBase(driver)).gotoKnowledgeBase();
    }
}
