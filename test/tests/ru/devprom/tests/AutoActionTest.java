package ru.devprom.tests;

import org.testng.Assert;
import org.testng.annotations.Test;
import ru.devprom.helpers.DataProviders;
import ru.devprom.items.*;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.kanban.*;
import ru.devprom.pages.project.autoactions.AutoActionNewPage;
import ru.devprom.pages.project.requests.*;
import ru.devprom.pages.scrum.*;

public class AutoActionTest extends ProjectTestBase 
{
	@Test(description = "S-4434 Действие по комментарию")
	public void addNewAutoAction() throws InterruptedException 
	{
		Project webTest = new Project("AutoActionTest"+DataProviders.getUniqueString(), 
				"AutoActionTest"+DataProviders.getUniqueStringAlphaNum(),
				new Template(this.kanbanTemplateName));

		// создаем проект для изоляции теста
		PageBase page = new PageBase(driver);
		ProjectNewPage pnp = page.createNewProject();
		
		KanbanPageBase firstPage = (KanbanPageBase) pnp.createNew(webTest);

		// переходим на страницу управления автоматическими действиями
		AutoActionNewPage newPage = firstPage.gotoAutoActions().addNewAutoAction();
		
		// дальше идет логика теста
		newPage.addName(DataProviders.getUniqueString()).addComment("готово").setActionState("resolved").save();
				
		// переходим на страницу создания пожеланий в проекте Канбан и создаем пожелание с описанием
		KanbanTask wish = new KanbanTask("addNewAutoAction");
		KanbanTaskNewPage checkTask = firstPage.gotoKanbanBoard().goToAddWish();
		checkTask.addName(wish.getName());
	    checkTask.addDescription("готово");
	    checkTask.save();
	
	    //проверяем на доске что пожелание находится в состоянии "Готов" : ставим фильтр состояния "Готов", затем смотрим, что наше пожелание есть на доске
	    KanbanTaskBoardPage wishStateResolved = firstPage.gotoKanbanBoard();
	    wishStateResolved.setFilter("state","resolved");
	    Thread.sleep(waiting);
		wish.setId(wishStateResolved.getIDTaskByName(wish.getName()));
	    Assert.assertTrue(wishStateResolved.isTaskPresent(wish.getNumericId()),"Пожелание не находится в состоянии Готов");
	}

	@Test(description = "S-4433 Создание, изменение вместе и раздельно")
	public void massAutoActions () throws InterruptedException
	{
		Project webTest = new Project("AutoActionTest"+DataProviders.getUniqueString(),
				"AutoActionTest"+DataProviders.getUniqueStringAlphaNum(),
				new Template(this.kanbanTemplateName));

		// создаем проект для изоляции теста
		PageBase page = new PageBase(driver);
		ProjectNewPage pnp = page.createNewProject();
		KanbanPageBase firstPage = (KanbanPageBase) pnp.createNew(webTest);

		// переходим на страницу управления автоматическими действиями
		AutoActionNewPage newPage = firstPage.gotoAutoActions().addNewAutoAction();

		//логика теста: добавляем автоматическое действие
		newPage.addName(DataProviders.getUniqueString()).setCondition0("State").setValue0("новые").setActionState("analysis").save();

		// переходим на страницу создания пожеланий в проекте Канбан и создаем два пожелания
		KanbanTask wish1 = new KanbanTask("addNewAutoActionWish1");
		KanbanTaskNewPage checkTask1 = firstPage.gotoKanbanBoard().goToAddWish();
		checkTask1.addName(wish1.getName());
		checkTask1.save();
		KanbanTask wish2 = new KanbanTask("addNewAutoActionWish2");
		KanbanTaskNewPage checkTask2 = firstPage.gotoKanbanBoard().goToAddWish();
		checkTask2.addName(wish2.getName());
		checkTask2.save();

		//проверяем на доске что пожелания находится в состоянии "Анализ" : ставим фильтр состояния "Анализ", затем смотрим, что наши пожелания есть на доске
		KanbanTaskBoardPage wishStateAnalysis1 = firstPage.gotoKanbanBoard();
		wishStateAnalysis1.setFilter("state","analysis");
		Thread.sleep(waiting);
		wish1.setId(wishStateAnalysis1.getIDTaskByName(wish1.getName()));
		Assert.assertTrue(wishStateAnalysis1.isTaskPresent(wish1.getNumericId()),"Пожелание не находится в состоянии Анализ");
		KanbanTaskBoardPage wishStateAnalysis2 = firstPage.gotoKanbanBoard();
		Thread.sleep(waiting);
		wish2.setId(wishStateAnalysis2.getIDTaskByName(wish2.getName()));
		Assert.assertTrue(wishStateAnalysis2.isTaskPresent(wish2.getNumericId()),"Пожелание не находится в состоянии Анализ");
	}

	@Test(description = "S-4435 Действие как часть жизненного цикла пожелания")
	public void actionAsWorkFlowStage () throws InterruptedException
	{
		Project webTest = new Project("AutoActionTest"+DataProviders.getUniqueString(),
				"AutoActionTest"+DataProviders.getUniqueStringAlphaNum(),
				new Template(this.scrumTemplateName));

		// создаем проект для изоляции теста
		PageBase page = new PageBase(driver);
		ProjectNewPage pnp = page.createNewProject();
		ScrumPageBase firstPage = (ScrumPageBase) pnp.createNew(webTest);

		// переходим на страницу управления автоматическими действиями
		AutoActionNewPage newPage = firstPage.gotoAutoActions().addNewAutoAction();

		//логика теста: добавляем автоматическое действие
		newPage.addName(DataProviders.getUniqueString()).setCondition0("State").setValue0("бэклог спринта").setSprint("1").save();

		//добавляем еще одно автоматическое действие
		AutoActionNewPage newPage1 = firstPage.gotoAutoActions().addNewAutoAction();
		newPage1.addName(DataProviders.getUniqueString()).setCondition0("State")
				.setValue0("бэклог спринта").setCondition1("Estimation")
				.setOperator1("greater").setValue1("0").setActionState("planned").save();

		//переходим на доску историй Скрам, создаем историю и переводим в состояние "Бэклог спринта"
		ScrumIssue issue = new ScrumIssue("addNewIssue","","", "grooming");
		ScrumIssueNewPage ibp = firstPage.gotoBackLog().addUserStory();
		ibp.createIssue(issue);
		BackLogPage blp = firstPage.gotoBackLog();
		issue.setId(blp.getIDByName(issue.getName()));
		RequestsBoardPage boardPage = firstPage.gotoHistoryBoard();
		Thread.sleep(waiting);

		//переходим на страницу редактирования и устанавливаем оценку
		RequestEditPage rep = boardPage.editRequest(issue.getNumericId());
		rep.editEstimation(2);
		rep.saveEdited();
		Thread.sleep(waiting);
		Assert.assertTrue(boardPage.isRequestInSection(issue.getNumericId(),"","Запланировано в спринт"),"Пожелание не находится в состоянии Запланировано в спринт");
	}

	@Test(description = "S-4436 Создание задачи и комментария")
	public void autoCreateTaskComment () throws InterruptedException
	{
	Project webTest = new Project("AutoActionTest"+DataProviders.getUniqueString(),
				"AutoActionTest"+DataProviders.getUniqueStringAlphaNum(),
				new Template(this.kanbanTemplateName));

		// создаем проект для изоляции теста
		PageBase page = new PageBase(driver);
		ProjectNewPage pnp = page.createNewProject();
		KanbanPageBase firstPage = (KanbanPageBase) pnp.createNew(webTest);

		// переходим на страницу управления автоматическими действиями
		AutoActionNewPage newPage = firstPage.gotoAutoActions().addNewAutoAction();

		//логика теста: добавляем автоматическое действие
		newPage.addName(DataProviders.getUniqueString()).setValue0("создать задачу")
                .addComment(DataProviders.getUniqueString()).addTaskName("новая задача")
				.addTaskType("Анализ").save();

		// переходим на страницу создания пожеланий в проекте Канбан и создаем пожелание с описанием
		KanbanTask wish = new KanbanTask("addNewAutoAction");
		KanbanTaskNewPage checkTask = firstPage.gotoKanbanBoard().goToAddWish();
		checkTask.addName(wish.getName());
		checkTask.addDescription("создать задачу");
		checkTask.save();
		KanbanTaskBoardPage ktbp = firstPage.gotoTaskBoard();
		Thread.sleep(waiting);
		Assert.assertTrue(ktbp.isTextPresent("новая задача"),"Пожелание не находится в состоянии Анализ");
	}

	@Test(description = "S-4437 Косвенное (каскадное) выполнение автоматического действия")
	public void indirectAutoActionExecution() throws InterruptedException
	{
		Project webTest = new Project("AutoActionTest"+DataProviders.getUniqueString(),
				"AutoActionTest"+DataProviders.getUniqueStringAlphaNum(),
				new Template(this.scrumTemplateName));

		// создаем проект для изоляции теста
		PageBase page = new PageBase(driver);
		ProjectNewPage pnp = page.createNewProject();
		ScrumPageBase firstPage = (ScrumPageBase) pnp.createNew(webTest);

		// переходим на страницу управления автоматическими действиями
		AutoActionNewPage newPage = firstPage.gotoAutoActions().addNewAutoAction();

		//логика теста: добавляем автоматическое действие
		newPage.addName(DataProviders.getUniqueString()).setCondition0("Iteration").setValue0("1").setActionState("grooming").save();

		//добавляем еще одно автоматическое действие
		AutoActionNewPage newPage1 = firstPage.gotoAutoActions().addNewAutoAction();
		newPage1.addName(DataProviders.getUniqueString()).setCondition0("State")
				.setValue0("бэклог спринта").setCondition1("Estimation")
				.setOperator1("greater").setValue1("0").setActionState("planned").save();

		//переходим на доску историй Скрам и создаем историю
		ScrumIssue issue = new ScrumIssue("addNewIssue","","", "");
		ScrumIssueNewPage ibp = firstPage.gotoBackLog().addUserStory();
		ibp.createIssue(issue);

		//нужно установить ID для дальнейших манипуляций
		BackLogPage blp = firstPage.gotoBackLog();
		issue.setId(blp.getIDByName(issue.getName()));
		RequestsBoardPage boardPage = firstPage.gotoHistoryBoard();
		Thread.sleep(waiting);

		//сначала ставим спринт на странице редактирования
		RequestEditPage rep1 = boardPage.editRequest(issue.getNumericId());
		rep1.setSprint("1");
		rep1.saveEdited();
		Thread.sleep(waiting);

		//снова переходим на страницу редактирования и устанавливаем оценку
		RequestEditPage rep = boardPage.editRequest(issue.getNumericId());
		rep.editEstimation(2);
		rep.saveEdited();
		Thread.sleep(waiting);
		Assert.assertTrue(boardPage.isRequestInSection(issue.getNumericId(),"","Запланировано в спринт"),"Пожелание не находится в состоянии Запланировано в спринт");
	}
}