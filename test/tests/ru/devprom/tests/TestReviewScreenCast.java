/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package ru.devprom.tests;

import java.awt.Image;
import java.awt.Toolkit;
import java.awt.datatransfer.Clipboard;
import java.awt.datatransfer.StringSelection;
import java.awt.datatransfer.Transferable;
import java.io.File;
import java.io.IOException;
import java.util.ArrayList;
import javax.imageio.ImageIO;
import org.testng.annotations.BeforeClass;
import org.testng.annotations.Test;
import ru.devprom.helpers.Configuration;
import ru.devprom.helpers.CopyData;
import ru.devprom.helpers.DataProviders;
import ru.devprom.helpers.ImageTransferable;
import ru.devprom.items.KanbanTask;
import ru.devprom.items.Project;
import ru.devprom.items.Requirement;
import ru.devprom.items.Template;
import ru.devprom.items.TestScenario;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.kanban.KanbanAddSubtaskPage;
import ru.devprom.pages.kanban.KanbanBuildsPage;
import ru.devprom.pages.kanban.KanbanEnvirenmentNewPage;
import ru.devprom.pages.kanban.KanbanEnvirenmentsPage;
import ru.devprom.pages.kanban.KanbanNewBuildPage;
import ru.devprom.pages.kanban.KanbanPageBase;
import ru.devprom.pages.kanban.KanbanSubTaskEditPage;
import ru.devprom.pages.kanban.KanbanTaskBoardPage;
import ru.devprom.pages.kanban.KanbanTaskNewPage;
import ru.devprom.pages.kanban.KanbanTaskViewPage;
import ru.devprom.pages.kanban.KanbanTasksPage;
import ru.devprom.pages.kanban.KanbanTestsPage;
import ru.devprom.pages.project.requests.RequestDonePage;
import ru.devprom.pages.project.requests.RequestsPage;
import ru.devprom.pages.project.requirements.RequirementNewPage;
import ru.devprom.pages.project.requirements.RequirementViewPage;
import ru.devprom.pages.project.requirements.RequirementsDocsPage;
import ru.devprom.pages.project.requirements.RequirementsPage;
import ru.devprom.pages.project.testscenarios.StartTestingPage;
import ru.devprom.pages.project.testscenarios.TestScenarioAddToBaselinePage;
import ru.devprom.pages.project.testscenarios.TestScenarioNewPage;
import ru.devprom.pages.project.testscenarios.TestScenarioTestingPage;
import ru.devprom.pages.project.testscenarios.TestScenarioViewPage;
import ru.devprom.pages.project.testscenarios.TestScenariosPage;
import ru.devprom.pages.project.testscenarios.TestSpecificationNewPage;
import ru.devprom.pages.project.testscenarios.TestSpecificationViewPage;
import ru.devprom.pages.project.testscenarios.TestSpecificationsPage;

/**
 *
 * @author лена
 */
public class TestReviewScreenCast extends ProjectTestBase{
    Project kanbanProject;
    int timeOut = 1000;
    int bigTimeOut = 7000;
    
    TestScenario testPlan;
    KanbanTask bug;
    /**
	 * Сценарий обучающих роликов: Обзор тестирования
	 */
    @Test(description="S-3323")
	public void runTestReviewScreenCast() throws InterruptedException, IOException{
          developmentDocsStage();
          developmentTestsStage();
          improvementRecoveryStage();
          releaseTestingStage();
          versionTestingStage();
	}
        
    private void developmentDocsStage() throws InterruptedException {
        KanbanPageBase kanbanPage = createNewKanbanProject(true);
        TestSpecificationsPage testPlansPage = kanbanPage.gotoTestPlans();
        TestSpecificationNewPage newTestPlanPage = testPlansPage.createNewSpecification();
        testPlan = new TestScenario("Приемочные тесты");
        TestSpecificationViewPage viewTestplansPage = newTestPlanPage.create(testPlan);
        TestScenarioNewPage testScenarioNewPage = viewTestplansPage.addSection();
        TestScenario section1 = new TestScenario("Установка дистрибутива");
        section1.setTemplate("Приемочный сценарий");
        //
        testScenarioNewPage.createNewScenarioWithTemplate(section1);
        testScenarioNewPage = viewTestplansPage.addSection();
        TestScenario section2 = new TestScenario("Первоначальная настройка приложения");
        section2.setTemplate("Приемочный сценарий");
        testScenarioNewPage.createNewScenarioWithTemplate(section2);
    }

    private void developmentTestsStage() throws InterruptedException {
        RequirementsDocsPage requrementsDocsPage = (new KanbanPageBase(driver)).gotoRequirementsDocs();
        RequirementViewPage requirementsViewPage = requrementsDocsPage.addDoc();
        requirementsViewPage.editRequirementName("Варианты использования");
        RequirementNewPage rnp = requirementsViewPage.addChildRequirement();
        Requirement r = new Requirement("Авторизация пользователя");
        r.setTemplateName("OpenUP");
        rnp.createChild(r, true);
        TestScenarioNewPage tsnp = requirementsViewPage.createNewTestSuit();
        TestScenario scenario = new TestScenario("Детальная проверка авторизации");
        TestScenario ParentScenario = new TestScenario("Ручные функциональные тесты");
        scenario.setParentPage(ParentScenario);
        ArrayList<String> table = new ArrayList<String>();
        table.add("Действие 1");
        table.add("Действие 2");
        table.add("Действие 3");
        table.add("Результат 1");
        table.add("Результат 2");
        table.add("Результат 3");
        tsnp.createScenarioWithTable(scenario, table);
        requirementsViewPage.waitForTraceEntity("TestScenario");
        requirementsViewPage.addContent(r.getClearId(), "\nПоявился новый шаг основного сценария");
        driver.navigate().refresh();
        TestSpecificationsPage testSpecoficationsPage = requirementsViewPage.clickAttentionTesting();
        Thread.sleep(bigTimeOut);
        testSpecoficationsPage.clickRepair();
    }

    private void improvementRecoveryStage() {
        KanbanTaskBoardPage kanbanBoard = (new KanbanPageBase(driver)).gotoKanbanBoard();
        KanbanTaskNewPage kanbanNewBugPage = kanbanBoard.createTaskInCell(0,"Тестирование (");
        bug = new KanbanTask("При первоначальной настройке приложения возникло сообщение об ошибке");
        bug.setDescription("Установил присланный мне дистрибутив.\nВыполнил установку и на шаге указания параметров подключения нового пользователя получил сообщение об ошибке:");
        bug.setType("Ошибка");
        kanbanNewBugPage.addName(bug.getName());
        kanbanNewBugPage.addDescription(bug.getDescription());
        kanbanNewBugPage.saveTaskFromBoard(bug);
        KanbanTaskViewPage taskViewPage = kanbanBoard.clickToTask(bug.getId());
        TestScenarioNewPage testScenarioNewPage = taskViewPage.clickActionCreateScenario();
        TestScenario scenario = new TestScenario("Проверка граничных условий");
        TestScenario testPlan = new TestScenario("Ручные функциональные тесты");
        scenario.setParentPage(testPlan);
        ArrayList<String> table = new ArrayList<String>();
         table.add("Действие 1");
         table.add("Действие 2");
         table.add("Действие 3");
         table.add("Результат 1");
         table.add("Результат 2");
         table.add("Результат 3");
        testScenarioNewPage.createScenarioWithTable(scenario, table);
     //   testScenarioNewPage.createScenarioWithNewTestPlan(scenario);
    }
/*
    private void createTestPlanStage() {
        throw new UnsupportedOperationException("Not supported yet."); //To change body of generated methods, choose Tools | Templates.
    }
*/
    private void releaseTestingStage() throws InterruptedException, IOException {
        KanbanBuildsPage buildsPage = (new KanbanPageBase(driver)).gotoBuilds();
        KanbanNewBuildPage buildNewPage = buildsPage.clickNewBuild();
        buildNewPage.createSimpleNewBuild("3.4.0");
        Thread.sleep(timeOut);
        KanbanEnvirenmentsPage envirenmentsPage = (new KanbanPageBase(driver)).gotoEnvironments();
        KanbanEnvirenmentNewPage envirenmentNewPage = envirenmentsPage.clickAddEnvironment();
        envirenmentNewPage.createEnvironment("Chrome", "", "");
        envirenmentsPage.clickAddEnvironment();
        envirenmentNewPage.createEnvironment("IE", "", "");
        
        TestSpecificationsPage testPlansPage = envirenmentsPage.gotoTestPlans();
        testPlan.setCompleteId(testPlansPage.getIdByName(testPlan.getName()));
        StartTestingPage startTestingPage = testPlansPage.clickStartTesting(testPlan.getId());
        TestScenarioTestingPage testingPage = startTestingPage.startTest("3.4.0","Chrome");
        testingPage.passTest(testPlan);
        testingPage.passTest(testPlan);
        testingPage.passTest(testPlan);
        RequirementsPage requirenmentPage = (new KanbanPageBase(driver)).goRequirementReestr();
        requirenmentPage.checkAll();
        TestScenariosPage scenariosPage = requirenmentPage.clickMoreTestDocs();
        scenariosPage.checkAll();
        TestScenario checkVersionPlan = new TestScenario("Проверка версии 3.4 на регресс");
        scenariosPage.massIncludeToNewTestPlan(checkVersionPlan.getName());
        KanbanTaskBoardPage kanbanBoard = (new KanbanPageBase(driver)).gotoKanbanBoard();
        bug.setId(kanbanBoard.getIDTaskByName(bug.getName()));
        kanbanBoard.selectWish(bug.getId());
        kanbanBoard.massTestDocs();
        scenariosPage.checkAll();
        TestScenarioViewPage testPlanViewPage = scenariosPage.massIncludeToTestPlan(checkVersionPlan.getName());
        testPlanViewPage.startTesting();
        startTestingPage.startTest("3.4.0", "IE");
        //----
        testingPage.fillCell("1", "4", "Ok");
        //driver.navigate().refresh();
        testingPage.fillCell("2", "4", "Ok");
        //driver.navigate().refresh();
        (new CopyData()).copyImage(Configuration.getPathToBugImage());
        testingPage.pasteToCell("3", "4"); 
        //-----
        testingPage.failTest(checkVersionPlan);
        testingPage.rejectWish("Не прошел тест, все подробности смотри в связанном отчете по тестированию");
        testingPage.gotoScenarioNumber(2);
        KanbanTask bug2 = new KanbanTask("Проблема авторизации пользователя, если в пароле есть символы врехнего регистра");
        KanbanTaskNewPage taskNewPage = testingPage.createNewBug();
        taskNewPage.addName(bug2.getName());
        taskNewPage.save();
        //-----
        testingPage.fillCell("1", "4", "Ok");
        //driver.navigate().refresh();
        testingPage.fillCell("2", "4", "Ok");
        //driver.navigate().refresh();
        testingPage.pasteToCell("3", "4"); 
        //-----
        testingPage.failTest(checkVersionPlan);
        try {
    		Thread.sleep(6000);
    	}
        catch (InterruptedException e) {
    	}
        (new KanbanPageBase(driver)).gotoKanbanBoard();
        kanbanBoard.moveToAnotherRelease(bug.getNumericId(), 0, "Разработка: готово");
        kanbanBoard.clickSubmit();
        try {
    		Thread.sleep(2000);
    	}
        catch (InterruptedException e) {
    	}
        kanbanBoard.moveToAnotherRelease(bug.getNumericId(), 0, "Тестирование (");
        try {
    		Thread.sleep(2000);
    	}
        catch (InterruptedException e) {
    	}
        bug2.setId(kanbanBoard.getIDTaskByName(bug2.getName()));
        kanbanBoard.moveToAnotherRelease(bug2.getNumericId(), 0, "Анализ (");
        try {
    		Thread.sleep(2000);
    	}
        catch (InterruptedException e) {
    	}
        kanbanBoard.moveToAnotherRelease(bug2.getNumericId(), 0, "Анализ: готово");
        kanbanBoard.clickSubmit();
        try {
    		Thread.sleep(2000);
    	}
        catch (InterruptedException e) {
    	}
        kanbanBoard.moveToAnotherRelease(bug2.getNumericId(), 0, "Разработка (");
        try {
    		Thread.sleep(2000);
    	}
        catch (InterruptedException e) {
    	}
        kanbanBoard.moveToAnotherRelease(bug2.getNumericId(), 0, "Разработка: готово");
        kanbanBoard.clickSubmit();
        try {
    		Thread.sleep(2000);
    	}
        catch (InterruptedException e) {
    	}
        (new KanbanPageBase(driver)).gotoBuilds();
        buildsPage.clickNewBuild();
        buildNewPage.createSimpleNewBuild("3.4.1");
        Thread.sleep(timeOut);
        RequestsPage foundBugsPage = (new KanbanPageBase(driver)).gotoFoundBugs();
        foundBugsPage.checkAll();
        foundBugsPage.moreStartTesting();
        startTestingPage.startTest("3.4.1", "IE");
        RequestDonePage requestDonePage = testingPage.rejectWishWithOutTime();
        requestDonePage.submit();
        testingPage.passTest(testPlan);
        requestDonePage = testingPage.readyWishWithOutTime();
        requestDonePage.submit();
        testingPage.passTest(testPlan);
        KanbanTestsPage testsPage = (new KanbanPageBase(driver)).gotoTests();
        File file = new File(Configuration.getPathToTestReport());
        testsPage.importReport(file);
        
    }
/*
    private void automationTestingSatge() {
        throw new UnsupportedOperationException("Not supported yet."); //To change body of generated methods, choose Tools | Templates.
    }
    */
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
            Project project = new Project("Тестирование продукта", "kanban" + DataProviders.getUniqueStringAlphaNum(), kanbanTemplate);
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

    private void versionTestingStage() {
        TestSpecificationsPage testPlansPage = (new KanbanPageBase(driver)).gotoTestPlans();
        TestSpecificationViewPage testPlanViewPage = testPlansPage.clickToSpecification(testPlan.getId());
        TestScenarioAddToBaselinePage addToBaselinePage = testPlanViewPage.addToBaseline();
        addToBaselinePage.addToBaseline(testPlan, "Релиз 0", false);
        TestScenarioNewPage testScenarioNewPage = testPlanViewPage.addSection();
        TestScenario testScenario = new TestScenario("Новый тестовый сценарий");
        testScenario.setTemplate("Приемочный сценарий");
        testScenarioNewPage.createNewScenarioWithTemplate(testPlan);
        testPlanViewPage.compareWithVersion("Начальная");
    }
}
