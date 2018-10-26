package ru.devprom.tests;

import java.io.File;
import java.util.Arrays;
import java.util.List;

import org.openqa.selenium.By;
import org.testng.Assert;
import org.testng.annotations.Test;

import ru.devprom.helpers.DataProviders;
import ru.devprom.helpers.DateHelper;
import ru.devprom.helpers.FileOperations;
import ru.devprom.items.Document;
import ru.devprom.items.Iteration;
import ru.devprom.items.Project;
import ru.devprom.items.RTask;
import ru.devprom.items.ScrumTask;
import ru.devprom.items.Spent;
import ru.devprom.items.Template;
import ru.devprom.items.TestScenario;
import ru.devprom.items.TimetableItem;
import ru.devprom.pages.MyProjectsPageBase;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.project.CrossProjectsTasksBoard;
import ru.devprom.pages.project.IterationNewPage;
import ru.devprom.pages.project.LinkProjectsPage;
import ru.devprom.pages.project.LinkedProjectsPage;
import ru.devprom.pages.project.MenuCustomizationPage;
import ru.devprom.pages.project.ReleasesIterationsPage;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.TimetablePage;
import ru.devprom.pages.project.documents.DocumentNewPage;
import ru.devprom.pages.project.documents.DocumentViewPage;
import ru.devprom.pages.project.documents.DocumentsPage;
import ru.devprom.pages.project.settings.StateEditPage;
import ru.devprom.pages.project.tasks.MyTasksPage;
import ru.devprom.pages.project.tasks.TaskCompletePage;
import ru.devprom.pages.project.tasks.TaskEditPage;
import ru.devprom.pages.project.tasks.TaskNewPage;
import ru.devprom.pages.project.tasks.TaskPrintCardsPage;
import ru.devprom.pages.project.tasks.TaskViewPage;
import ru.devprom.pages.project.tasks.TasksPage;
import ru.devprom.pages.project.tasks.TasksPrintListPage;
import ru.devprom.pages.project.tasks.TasksStatePage;
import ru.devprom.pages.project.testscenarios.StartTestingPage;
import ru.devprom.pages.project.testscenarios.TestScenarioTestingPage;
import ru.devprom.pages.project.testscenarios.TestScenarioViewPage;
import ru.devprom.pages.project.testscenarios.TestSpecificationNewPage;
import ru.devprom.pages.project.testscenarios.TestSpecificationViewPage;
import ru.devprom.pages.project.testscenarios.TestSpecificationsPage;
import ru.devprom.pages.scrum.ScrumPageBase;
import ru.devprom.pages.scrum.TasksBoardPage;

public class TaskTest extends ProjectTestBase {
	private String executor = "WebTestUser";

	
	
	/** This method creates a task and executes it */
	@Test
	public void testCreateAndResolve() {
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		TasksPage mtp = favspage.gotoTasks();
		RTask testTask = createTask();
		TaskViewPage tvp = mtp.clickToTask(testTask.getId());
		TaskCompletePage ctp = tvp.completeTask();
		tvp = ctp.complete(testTask);
		mtp = tvp.gotoTasks();
		mtp.showAll();
		Assert.assertEquals(mtp.getTaskProperty(testTask.getId(), "state"), "Выполнена");
		
	}
	
	/** This method creates a task and binds new test requirements to it, than checks for we have in output */
	@Test (priority=2)
	public void testCreateAndBindToTest() {
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		TestSpecificationsPage tsp = favspage
				.gotoTestPlans();
		TestSpecificationNewPage ntsp = tsp.createNewSpecification();
		TestScenario testPlan = new TestScenario("TestPlan"
				+ DataProviders.getUniqueString());
		TestScenario testScenario = new TestScenario("testScenario"
				+ DataProviders.getUniqueString());
		TestSpecificationViewPage tspecp = ntsp.create(testPlan);
		tspecp.addNewTestScenario(testScenario);
		
		TasksPage mtp = tspecp.gotoTasks();
		RTask testTask = createTask();
		TaskViewPage tvp = mtp.clickToTask(testTask.getId());
		tvp.addTestDocumentation(testScenario.getName());
		String testScenarioID = tvp.getLastActivityID('S');
		mtp = tvp.gotoTasks();
		mtp.addColumn("TestScenario");
		Assert.assertEquals(mtp.getTaskProperty(testTask.getId(), "testscenario"),testScenarioID);
		mtp.removeColumn("TestScenario");
		
		
	}
	
	/**
	 * The method checks the correspondence between real tasks list and print
	 * list preview
	 */
	@Test (priority=3)
	public void testTasksPrintList() {
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		TasksPage mtp = favspage.gotoTasks();
		mtp.showAll();
		RTask[] tasksInGrid = mtp.readAllTasks();
		Arrays.sort(tasksInGrid);
		FILELOG.debug("Tasks: ");
		for (RTask task:tasksInGrid){
			FILELOG.debug(task);
		}
		
		TasksPrintListPage tplp = mtp.clickPrintList();
		RTask[] printedTasks = tplp.getPrintedTasks();
		Arrays.sort(printedTasks);
		FILELOG.debug("Printed tasks: ");
		for (RTask task:printedTasks){
			FILELOG.debug(task);
		}
		driver.navigate().back();
		Assert.assertEquals(printedTasks, tasksInGrid);
		
	}
	
	/**
	 * The method checks the correspondence between real tasks list and print
	 * cards preview
	 */
	@Test (priority=3)
	public void testTasksPrintCards() {
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		TasksPage mtp = favspage.gotoTasks();
		mtp.showAll();
		RTask[] tasksInGrid = mtp.readAllTasks();
		Arrays.sort(tasksInGrid);
		FILELOG.debug("Tasks: ");
		for (RTask task:tasksInGrid){
			FILELOG.debug(task);
		}
		
		TaskPrintCardsPage tplp = mtp.clickPrintCards();
		RTask[] printedTasks = tplp.getPrintedTasks();
		Arrays.sort(printedTasks);
		FILELOG.debug("Printed tasks: ");
		for (RTask task:tasksInGrid){
			task.setType(""); // skip type check
		}
		for (RTask task:printedTasks){
			FILELOG.debug(task);
		}
		driver.navigate().back();
		Assert.assertEquals(printedTasks, tasksInGrid);
		
	}
	

	/**
	 * The method checks exported to Excel data and compare it with the Tasks
	 * board data
	 * 
	 * @throws Exception
	 */
	@Test  (priority=3)
	public void exportToExcelTest() throws Exception {
		
		/*PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(
						this.waterfallTemplateName),
				executor);
		  page.gotoProject(webTest);*/
		
		TasksPage mtp = (new SDLCPojectPageBase(driver)).gotoTasks();
		mtp.showAll();
		RTask[] tExisted = mtp.readAllTasks();
		Arrays.sort(tExisted);
		for (RTask t : tExisted) {
			FILELOG.debug(t);
		}
		RTask[] tExported = mtp.exportToExcel();
		Arrays.sort(tExported);
		for (RTask t : tExported) {
			FILELOG.debug(t);
		}
		Assert.assertEquals(tExported, tExisted);
	}
	
	/**
	 * The method perform Edit Type mass operation on a few Tasks, and then
	 * checks the types
	 */
	@Test(priority = 2)
	public void massOperationsTest() {
		TasksPage mtp = (new SDLCPojectPageBase(driver)).gotoTasks();
		RTask testTask1 = createTask();
		RTask testTask2 = createTask(); 
		mtp.showAll();
		mtp.checkTask(testTask1.getId());
		mtp.checkTask(testTask2.getId());
		mtp = mtp.massChangeType("Анализ");
		driver.navigate().refresh();
		testTask1.setType("Анализ");
		testTask2.setType("Анализ");
		Assert.assertEquals(
				mtp.getTaskProperty(testTask1.getId(), "type"),
				"Анализ");
		Assert.assertEquals(
				mtp.getTaskProperty(testTask2.getId(), "type"),
				"Анализ");
	}

	/**
	 * The method creates new Task and fill all the properties,
	 * then saves it and reads the saved properties.
	 */
	@Test
	public void testCreateNewTask() {
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		  page.gotoProject(webTest);
		  RTask testTask = new RTask("TestTask"+DataProviders.getUniqueString(), user, RTask.getRandomType(), RTask.getRandomEstimation());
		
		TasksPage mtp = (new SDLCPojectPageBase(driver)).gotoTasks();
		
		//Create object for Task and set up all the properties
		testTask.setIteration("0.1");
		testTask.setPriority(RTask.getRandomPriority());
		  
		//Create document
		DocumentsPage dp = (new SDLCPojectPageBase(driver)).gotoDocuments();
		DocumentNewPage ndp = dp.clickNewDoc();
		Document tDoc = new Document("TestDoc"
				+ DataProviders.getUniqueString(), "Some document body");
		DocumentViewPage dvp = ndp.createNewDoc(tDoc);
		
		//Create and pass test scenario
		
		
		TestSpecificationsPage tsp = dvp.gotoTestPlans();
		TestSpecificationNewPage ntsp = tsp.createNewSpecification();
		TestScenario testPlan = new TestScenario("TestPlan"
				+ DataProviders.getUniqueString());
		testPlan.setContent("");
		TestSpecificationViewPage tspecp = ntsp.create(testPlan);
		
		
		TestScenario testScenario = new TestScenario("TestScenario"+DataProviders.getUniqueString());
		TestScenarioViewPage tsvp = tspecp.addNewTestScenario(testScenario);
		StartTestingPage stp = tsvp.beginTest();
                TestScenarioTestingPage tstp = stp.startTest("0.1", "");
		String testResults = tstp.getTestRunId();
		tstp.passTest(testScenario);
		driver.navigate().refresh();
		
		//Create Task in system and fill all the properties
		mtp = (new SDLCPojectPageBase(driver)).gotoTasks();
		TaskNewPage ntp = mtp.createNewTask();
	    mtp = ntp.createTask(testTask);
	    TaskViewPage tvp = mtp.clickToTask(testTask.getId());
	    TaskEditPage tep = tvp.editTask();
	    tep.addPreviousTask("TestTask");
	    tep.addTestDoc(testScenario.getName());
	    tep.addTestResults(testPlan.getName());
	    tep.addDocs("TestDoc");
	    tep.addWatcher(user);
	    tep.addRequest("issue");
	    tvp= tep.saveChanges();
        		
		//Read and check the properties 
	    Assert.assertEquals(tvp.readName(), testTask.getName());
	    Assert.assertTrue(tvp.readIteration().contains(testTask.getIteration()));
	    Assert.assertEquals(tvp.readType(), testTask.getType());
	    Assert.assertEquals(tvp.readPriority(), testTask.getPriority());
	    Assert.assertEquals(tvp.readOwner(), testTask.getExecutor());
	    Assert.assertTrue(tvp.readPreviousTasks().get(0).contains("TestTask"));
	    Assert.assertTrue(tvp.readTestDoc().get(0).contains(testScenario.getName()));
	    Assert.assertTrue(tvp.readTestResults().get(0).contains(testResults));
	    //Assert.assertTrue(tvp.readRequirements().get(0).contains("R"));
	    Assert.assertTrue(tvp.readDocs().get(0).contains("TestDoc"));
	    Assert.assertTrue(tvp.readRequest().contains("I"));
	    Assert.assertTrue(tvp.readWatchers().get(0).contains(user));
	}
	
	
	/**
	 * The method creates 2 iterations, a task, and moves completed task to the old iteration.
	 */
	@Test
	public void moveTaskToOldIteration() {
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(
						this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		ReleasesIterationsPage rip = favspage.gotoReleasesIterations();
	
		//Creating 2 iterations for the release
		Iteration iteration2 = new Iteration("","Old iteration", DateHelper.getDayBefore(8), DateHelper.getDayBefore(3), "0");
		Iteration iteration1 = new Iteration("","Current iteration", DateHelper.getCurrentDate(), DateHelper.getDayAfter(5), "0");
				
		IterationNewPage inp = rip.addIteration();
		rip = inp.createIteration(iteration2);
		inp = rip.addIteration();
		rip = inp.createIteration(iteration1);
			
		//Create new Task and complete it
		TasksPage mtp = favspage.gotoTasks();
		RTask testTask = new RTask("TestTask"+DataProviders.getUniqueString(), user, RTask.getRandomType(), RTask.getRandomEstimation());
		testTask.setIteration(iteration1.getFullName());
		testTask.setPriority(RTask.getRandomPriority());
		TaskNewPage ntp = mtp.createNewTask();
		mtp = ntp.createTask(testTask);
		FILELOG.debug("Task created: " + testTask);		
		
        //Change iteration 		
		TaskViewPage tvp = mtp.clickToTask(testTask.getId());
		TaskCompletePage ctp = tvp.completeTask();
		tvp = ctp.complete(testTask);
        TaskEditPage tep = tvp.editTask();
        tep.selectIteration(iteration2.getFullName());
        tvp = tep.saveChanges();
		
        //Go to "Релизы и итерации" and check the Task
        
        rip = tvp.gotoReleasesIterations();
        Assert.assertTrue(rip.isTaskPresent(testTask.getId()), "Задача не обнаружена");
	}
		
	@Test(description="S-1864")
	public void timeSpentOnMyTasks() {
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		TasksPage mtp = favspage.gotoTasks();
		RTask testTask = createTask();
		MyProjectsPageBase mppb = mtp.gotoMyProjects();
		MyTasksPage mytp = mppb.gotoMyTasks();
		mytp.showNRows("all");
		mytp.addColumn("Spent");
		Spent spent1 = new Spent(DateHelper.getCurrentDate(), 2.0, executor, "Списание из портфеля Мои проекты");
		mytp = mytp.addSpentRecord(spent1, testTask.getId());
		TaskViewPage tvp = mytp.clickToTask(testTask.getId());
		List<Spent> spentRecords = tvp.readSpentRecords();
		Assert.assertTrue(spentRecords.size()>0, "После списания нет записей о затратах времени");
		Assert.assertEquals(spentRecords.get(0), spent1, "Неверная запись о затратах времени после списания");
		
		favspage = (SDLCPojectPageBase) tvp.gotoProject(webTest);
		mtp = favspage.gotoTasks();
		mtp.showAll();
		mtp.addColumn("Spent");
		spentRecords = mtp.readSpentRecords(testTask.getId());
		Assert.assertTrue(spentRecords.size()>0, "При открытии задачи из проекта нет записей о затратах времени");
		Assert.assertEquals(spentRecords.get(0), spent1, "Неверная запись о затратах времени при открытии задачи из проекта");
		
		Spent spent2 = new Spent(DateHelper.getCurrentDate(), 2.0, executor, "Списание из проекта");
		mtp.addSpentRecord(spent2, testTask.getId());
		TimetablePage tp = tvp.gotoTimetablePage();
		tp = tp.setMode("tasks");
		TimetableItem[] items = tp.readTimetable();
		for( TimetableItem i : items ) {
			if ( i.getName().contains(testTask.getName()) ) {
				Assert.assertEquals(i.getSum(), "4", "Неверное количество списанного времени в отчете");
				return;
			}
		}
		Assert.fail("В отчете о затраченном времени не найдена задача " + testTask.getName());
	}
	
	/**
	 * В "настройке полей формы" скрываем несколько полей для формы создания новой задачи.
	 * В тесте создается новый проект, чтобы в случае сбоя изменения не остались в основном проекте.
	 * S-1781
	 * @throws InterruptedException 
	 */
	@Test
	public void hideFieldsOnCreateForm() {
		 PageBase page = new PageBase(driver);
			
			//New SDLC Project
			String p = DataProviders.getUniqueString();
			Project project = new Project("SDLCProject"+p, "sdlcproject"+DataProviders.getUniqueStringAlphaNum(),new Template(this.waterfallTemplateName));
			
			//Create a Development Project
			ProjectNewPage pnp = page.createNewProject();
			SDLCPojectPageBase sdlcFirstPage = (SDLCPojectPageBase) pnp.createNew(project);
			FILELOG.debug("Created new project " + project.getName());
		
			//Check the attributes are visible on a create new form
			TasksPage tp = sdlcFirstPage.gotoTasks();
			TaskNewPage tnp = tp.createNewTask();
			Assert.assertTrue(tnp.isFieldVisibleByLabel("Исполнитель"), "Не видно поля 'Исполнитель'");
			Assert.assertTrue(tnp.isFieldVisibleByLabel("Тип"), "Не видно поля 'Тип'");
			tp = tnp.cancel();
			
			TasksStatePage tsp = tp.gotoTasksStatePage();
			StateEditPage tsmp = tsp.editState("Добавлена");
			tsmp.addAttribute("Исполнитель", false, false);
			tsmp.addAttribute("Тип", false, false);
			
			tsmp.saveChanges();
			tsp = new TasksStatePage(driver);
			tp = tsp.gotoTasks();
			tp = tp.turnOffFilter("taskassignee");
			tnp = tp.createNewTask();
			Assert.assertTrue(tnp.isFieldNotVisibleByLabel("Исполнитель"), "Не скрыто поле 'Исполнитель'");
			Assert.assertTrue(tnp.isFieldNotVisibleByLabel("Тип"), "Не скрыто поле 'Тип'");
			RTask task = new RTask("Task"+DataProviders.getUniqueString(), "", "", 2.0);
			task.setIteration("");
			tnp.createTask(task);
			FILELOG.debug("Created new task " + task);
			
	}		
	
	/**
	 * Перенос задачи с помощью кросс-проектной доски задач
	 * S-2222
	 */
	@Test
	public void moveTaskToAnotherProject() {
		
		String p = DataProviders.getUniqueString();
			String comment = "Тест по сценарию S-2222";
			String attachment = "Attachment" + DataProviders.getUniqueStringAlphaNum() + ".png";
			
			//Создаем новый проект SDLC
			Project sdlcTest = new Project("SDLCProject" +p, "sdlcproject" +DataProviders.getUniqueStringAlphaNum(),
					new Template(this.waterfallTemplateName));
			PageBase page = new PageBase(driver);
			ProjectNewPage pnp = page.createNewProject();
			pnp.createNew(sdlcTest);
			FILELOG.debug("Created new project " + sdlcTest.getName());
			
			//Создаем новый проект scrum
			Project scrumProject = new Project("Scrumproject"+p, "scrumproject"+DataProviders.getUniqueStringAlphaNum(),new Template(this.scrumTemplateName));
			pnp = page.createNewProject();
			pnp.createNew(scrumProject);
			FILELOG.debug("Created new project " + scrumProject.getName());
			
			//Включаем его как подпроект для основного тестового (создаем программу)
			LinkedProjectsPage lpp = new SDLCPojectPageBase(driver).gotoLinkedProjects();
			LinkProjectsPage lipp = lpp.includeToProgram();
			lipp.setRequestOptionsValue("1");
			lipp.setReleaseOptionsValue("2");
			lipp.linkProject(sdlcTest.getName());
			
			//Создаем задачу, чтобы проект отображался на кросс-проектной доске
			TasksBoardPage tbp = new ScrumPageBase(driver).gotoTasksBoard();
			ScrumTask task = new ScrumTask("Task" + p);
			tbp = tbp.addNewTaskScrum(task);
			
			//Возвращаемся в проект по умолчанию
			SDLCPojectPageBase favspage = (SDLCPojectPageBase) new PageBase(driver).gotoProject(sdlcTest);
			TasksPage mtp = favspage.gotoTasks();

			//Создаем задачу с аттачментом, списанием и комментарием
			RTask testTask = createTask();
			TaskViewPage tvp = mtp.clickToTask(testTask.getId());
		    TaskEditPage tep = tvp.editTask();
		    File file = FileOperations.createPNG(attachment);
		    tep.addAttachment(file);
		    tvp = tep.saveChanges();
		    Spent spent = new Spent("", 1.0, user, "Тестовое списание");
		    tvp.addSpentTimeRecord(spent);
		    tvp = tvp.addComment(comment);
		    
		    //Идем в Избранное, настраиваем отображение Кросс-проектной доски задач
		    MenuCustomizationPage mcp = tvp.gotoMenuFavsCustomization();
			mcp.searchMenuItem("Доска задач");	
			mcp.addFilteredMenuItem("Доска задач");
			mcp.saveChanges();
			favspage =  mcp.close();
			favspage.gotoCustomReport("favs", "", "Доска задач");
			CrossProjectsTasksBoard cprb = new CrossProjectsTasksBoard(driver);
			cprb.setupGrouping("Project");
			cprb = cprb.moveToAnotherProject(testTask.getNumericId(), scrumProject.getName(), 1);
			Assert.assertTrue(cprb.isRequestInSection(testTask.getNumericId(), scrumProject.getName(), 1), "Пожелание не найдено в другом проекте после перемещения");
		    tvp =  cprb.clickToTask(testTask.getId());
		    List<Spent> spentRecords = tvp.readSpentRecords();
			Assert.assertEquals(spentRecords.size(), 1, "Количество записей о списанном времени неверное");
			Assert.assertEquals(spentRecords.get(0).hours, spent.hours, "Ошибка в записи о списанном времени");
			Assert.assertEquals(tvp.readLastComment(), comment, "Не найден или не верный комментарий");
			List<String> attachments = tvp.readAttachmentHeaders();
			Assert.assertEquals(attachments.size(), 1, "Количество аттачментов неверное");
			Assert.assertEquals(attachments.get(0), attachment, "Неверное имя файла аттачмента");
	}
	
	
	
	private RTask createTask(){
		RTask testTask = new RTask("TestTask"+DataProviders.getUniqueString(), username, RTask.getRandomType(), RTask.getRandomEstimation());
		testTask.setPriority(RTask.getRandomPriority());
		TasksPage mtp = new TasksPage(driver);
	    TaskNewPage ntp = mtp.createNewTask();
	    mtp = ntp.createTask(testTask);
	    FILELOG.debug("Task created: " + testTask);
		return testTask; 
	}

}
