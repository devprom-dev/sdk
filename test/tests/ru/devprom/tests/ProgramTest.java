package ru.devprom.tests;

import java.io.File;
import java.util.List;

import org.testng.Assert;
import org.testng.annotations.BeforeClass;
import org.testng.annotations.Test;

import ru.devprom.helpers.Configuration;
import ru.devprom.helpers.DataProviders;
import ru.devprom.helpers.FileOperations;
import ru.devprom.items.KanbanTask;
import ru.devprom.items.Project;
import ru.devprom.items.Request;
import ru.devprom.items.ScrumIssue;
import ru.devprom.items.Template;
import ru.devprom.items.User;
import ru.devprom.pages.FavoritesPage;
import ru.devprom.pages.LoginPage;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.admin.ActivitiesPage;
import ru.devprom.pages.admin.AdminPageBase;
import ru.devprom.pages.admin.UsersListPage;
import ru.devprom.pages.kanban.KanbanPageBase;
import ru.devprom.pages.kanban.KanbanTaskBoardPage;
import ru.devprom.pages.kanban.KanbanTaskNewPage;
import ru.devprom.pages.kanban.KanbanTasksPage;
import ru.devprom.pages.project.CrossProjectsRequestsBoard;
import ru.devprom.pages.project.LinkProjectsPage;
import ru.devprom.pages.project.LinkedProjectsPage;
import ru.devprom.pages.project.MenuCustomizationPage;
import ru.devprom.pages.project.ReleasesIterationsPage;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requests.RequestNewPage;
import ru.devprom.pages.project.requests.RequestViewPage;
import ru.devprom.pages.project.requests.RequestsPage;
import ru.devprom.pages.project.settings.ProjectMembersPage;
import ru.devprom.pages.scrum.IssuesBoardPage;
import ru.devprom.pages.scrum.ScrumPageBase;

public class ProgramTest extends ProjectTestBase {
	
	Project sdlcProject;
	Project scrumProject;
	Project kanbanProject;
	User testUser;
	User standaloneUser;
	User scrumUser;
	User kanbanUser;
	KanbanTask task;
	ScrumIssue issue;
	
	
	@BeforeClass
	public void assignUser(){
		testUser = new User(username, password, user, "mail", true, true);
	}
	
	
	/** Test creates a new project, removes iterations and makes it a subproject of the default one. */
	@Test
	public void subprojectTest() {

		PageBase page = new PageBase(driver);
		String p = DataProviders.getUniqueString();
		
		//Program Project
		Project webTest = new Project("Program"+p, "program"+p,
				new Template(this.waterfallTemplateName));

		ProjectNewPage pnp = page.createNewProject();
		SDLCPojectPageBase sdlcFirstPage = (SDLCPojectPageBase) pnp.createNew(webTest);
		FILELOG.debug("Created new project " + webTest.getName());

		//Sub Project
		Project subProject= new Project("subProject"+p, "subproject"+p,
				new Template(this.waterfallTemplateName));
		
		pnp = page.createNewProject();
		sdlcFirstPage = (SDLCPojectPageBase) pnp.createNew(subProject);
		FILELOG.debug("Created new project " + subProject.getName());
		
		//Go to releases and Iterations and clear all
		ReleasesIterationsPage rip = sdlcFirstPage.gotoReleasesIterations();
		rip.deleteAllIterations();
		rip.deleteAllReleases();
		List<String> releasesAndIterations = rip.getList();
		Assert.assertTrue(releasesAndIterations.size()==0, "Releases list is not empty");
		
		//Link this project to default one as a a subproject
		LinkedProjectsPage lpp = rip.gotoLinkedProjects();
		LinkProjectsPage lipp = lpp.includeToProgram();
		lipp.setRequestOptionsValue("2");
		lipp.setReleaseOptionsValue("1");
		lipp.linkProject(webTest.getName());
		
		//Check that releases and iterations from the program project are visible
		rip = lipp.gotoReleasesIterations();
		releasesAndIterations = rip.getList();
		Assert.assertTrue(releasesAndIterations.contains("Релиз 0"), "Can't find release 0");
		Assert.assertTrue(releasesAndIterations.contains("Итерация 0.1"), "Can't find iteration 0.1");
		
		//Create an Request and check it's visibility in program project
		RequestsPage mip = rip.gotoRequests();
		RequestNewPage rnp = mip.clickNewCR();
		Request request = new Request("SubProjectRequest"+DataProviders.getUniqueString());
		rnp.createCRShort(request);
		sdlcFirstPage = (SDLCPojectPageBase)mip.gotoProject(webTest);
		mip = sdlcFirstPage.gotoRequests();
		Request subrequest = mip.findRequestById(request.getId());
		Assert.assertEquals(subrequest.getName(), request.getName());
	}

	/** Тест проверяет доступ пользователей подпроекта к Пожеланиям программы 
	 * @throws InterruptedException */
	@Test
	public void accessToProgramRequests() throws InterruptedException
	{
		PageBase page = new PageBase(driver);
		String p = DataProviders.getUniqueString();

		//Program Project
		Project webTest = new Project("Program"+p, "program"+p, new Template(this.waterfallTemplateName));
		ProjectNewPage pnp = page.createNewProject();
		SDLCPojectPageBase sdlcFirstPage = (SDLCPojectPageBase) pnp.createNew(webTest);
		FILELOG.debug("Created new project " + webTest.getName());
		
		//Create Request
		RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
		RequestNewPage ncrp = mip.clickNewCR();
		Request testRequest = new Request("TestCR-"
				+ DataProviders.getUniqueString(),
				"Program request",
				"Высокий",
				Request.getRandomEstimation(),
				"Доработка");
		mip = ncrp.createNewCR(testRequest);
		FILELOG.debug("Created Request: " + testRequest.getId());
		
		//Create test user
		AdminPageBase apb = mip.goToAdminTools();
		UsersListPage ulp = apb.gotoUsers();
		User testUser = new User(DataProviders.getUniqueString(), true);
		ulp = ulp.addNewUser(testUser, false);
		
		//Sub Project
		Project subProject= new Project("subProject"+p, "subproject"+p,
				new Template(this.waterfallTemplateName));
		
		//Create another Project and add User as a Member
		pnp = ulp.createNewProject();
		sdlcFirstPage = (SDLCPojectPageBase) pnp.createNew(subProject);
		ProjectMembersPage pmp = sdlcFirstPage.gotoMembers();
		pmp = pmp.gotoAddMember().addUserToProject(testUser, "Разработчик", 8,"");
		
		
		//Link this project to default one as a a subproject
		LinkedProjectsPage lpp = pmp.gotoLinkedProjects();
		LinkProjectsPage lipp = lpp.includeToProgram();
		lipp.setRequestOptionsValue("3");
		lipp.setReleaseOptionsValue("3");
		lipp.linkProject(webTest.getName());
		
		//Login as a subproject user
		LoginPage lp = lipp.logOut();
		FavoritesPage fp = lp.loginAs(testUser.getUsername(), testUser.getPass());
		fp.gotoProject(subProject);
		
		//Check the Request
		mip = (new SDLCPojectPageBase(driver)).gotoRequests();
		Assert.assertTrue(mip.isRequestPresent(testRequest.getId()), "Пожелание из Программы отсутствует в списке пожеланий подпроекта");
		
		//Open the Request and read Name 
		RequestViewPage rvp = mip.clickToRequest(testRequest.getId());
		Assert.assertEquals(rvp.readName(), testRequest.getName(), "Имя Пожелания не соответствует");
	}

	@Test (priority = 10, description="S-1995")
	public void createProjectsAndUsers(){
		
         String p = DataProviders.getUniqueString();

		//SDLC Member
        standaloneUser = new User("standalone"+p, true); 
         
        //SDLC Member
        scrumUser = new User("scrum"+p, true); 
         
        //SDLC Member
        kanbanUser = new User("kanban"+p, true); 
         
         
        //SDLC Project
		sdlcProject= new Project("sdlcProject"+p, "sdlcproject"+p,
				new Template(this.waterfallTemplateName));
		
		//Scrum Project
		scrumProject = new Project("scrumProject"+p, "scrumproject"+p,
				new Template(this.scrumTemplateName));
		
		//Kanban Project
		kanbanProject = new Project("kanbanProject"+p, "kanbanproject"+p,
				new Template(this.kanbanTemplateName));
		
		
		LoginPage lp = (new PageBase(driver)).logOut();
		FavoritesPage fp = lp.loginAs(testUser.getUsername(), testUser.getPass());
		ActivitiesPage ap = fp.goToAdminTools();
		UsersListPage ulp = ap.gotoUsers();
		ulp = ulp.addNewUser(standaloneUser, false);
		FILELOG.debug("Created: " + standaloneUser.getUsername());
		ulp = ulp.addNewUser(scrumUser, false);
		FILELOG.debug("Created: " + scrumUser.getUsername());
		ulp = ulp.addNewUser(kanbanUser, false);
		FILELOG.debug("Created: " + kanbanUser.getUsername());
		
		ProjectNewPage pnp = (new PageBase(driver)).createNewProject();
		pnp.createNew(sdlcProject);
		FILELOG.debug("Created new project " + sdlcProject.getName());
		
		pnp = (new PageBase(driver)).createNewProject();
		ScrumPageBase spb = (ScrumPageBase) pnp.createNew(scrumProject);
		FILELOG.debug("Created new project " + scrumProject.getName());
		
		ProjectMembersPage pmp = spb.gotoMembers();
		pmp = pmp.gotoAddMember().addUserToProject(scrumUser, "Участник команды", 10, "Дайджест об изменениях в проекте: ежедневно");
		
		
		pnp = (new PageBase(driver)).createNewProject();
		KanbanPageBase kpb = (KanbanPageBase) pnp.createNew(kanbanProject);
		FILELOG.debug("Created new project " + kanbanProject.getName());
		
		pmp = kpb.gotoMembers();
		pmp = pmp.gotoAddMember().addUserToProject(kanbanUser, "Участник команды", 10, "Дайджест об изменениях в проекте: ежедневно");
		
		pmp.gotoProject(sdlcProject);
		LinkedProjectsPage lpp = (new SDLCPojectPageBase(driver)).gotoLinkedProjects();
		LinkProjectsPage lipp = lpp.addSubproject();
		lpp = lipp.linkProject(scrumProject.getName());
		lipp = lpp.addSubproject();
		lipp.linkProject(kanbanProject.getName());
	}
	
	@Test (priority = 11, dependsOnMethods = { "createProjectsAndUsers" }, description="S-1996")
	public void accessToProject(){
		String p = DataProviders.getUniqueString();
		
		LoginPage lp = (new PageBase(driver)).logOut();
		FavoritesPage fp = lp.loginAs(scrumUser.getUsername(), scrumUser.getPass());
		ScrumPageBase spb =  (ScrumPageBase) fp.gotoProject(scrumProject);
		
		List<String> projects = spb.getProjectsList();
		FILELOG.debug("Список проектов для пользователя "+ scrumUser.getUsername() + ":");
		for (String pr:projects) {
			FILELOG.debug(pr);
		}
		
		Assert.assertTrue(projects.contains(sdlcProject.getName()), "В списке проектов нет SDLC проекта - программы");
		Assert.assertTrue(projects.contains(scrumProject.getName()), "В списке проектов нет scrum проекта - текущего");
		Assert.assertFalse(projects.contains(kanbanProject.getName()), "В списке проектов ошибочно присутствует kanban-проект");
		
		issue = new ScrumIssue("Issue" + p, "Низкий", "Некое описание");
		IssuesBoardPage ibp = spb.gotoIssuesBoard();
		ibp = ibp.addNewIssue(issue);
		
		lp = (new PageBase(driver)).logOut();
		fp = lp.loginAs(kanbanUser.getUsername(), kanbanUser.getPass());
		KanbanPageBase kpb = (KanbanPageBase) fp.gotoProject(kanbanProject);
		projects = spb.getProjectsList();
		
		FILELOG.debug("Список проектов для пользователя "+ kanbanUser.getUsername() + ":");
		for (String pr:projects) {
			FILELOG.debug(pr);
		}
		
		Assert.assertTrue(projects.contains(sdlcProject.getName()), "В списке проектов нет SDLC проекта - программы");
		Assert.assertTrue(projects.contains(kanbanProject.getName()), "В списке проектов нет kanban проекта - текущего");
		Assert.assertFalse(projects.contains(scrumProject.getName()), "В списке проектов ошибочно присутствует scrum-проект");
		
		
		task = new KanbanTask("TestTask"+p);
		KanbanTasksPage ktp = kpb.gotoKanbanTasks();
		KanbanTaskNewPage ktnp = ktp.addNewTask();
		ktp = ktnp.createTask(task);
		KanbanTaskBoardPage ktbp = ktp.gotoKanbanBoard();
		Assert.assertTrue(ktbp.isTaskPresent(task.getNumericId()), "На доске не найдена новая задача");
		
	}
	
	@Test (priority = 12, dependsOnMethods = { "accessToProject" }, description="S-1997")
	public void accessToProgram() throws InterruptedException{
		LoginPage lp = (new PageBase(driver)).logOut();
		FavoritesPage fp = lp.loginAs(testUser.getUsername(), testUser.getPass());
		SDLCPojectPageBase favspage =  (SDLCPojectPageBase) fp.gotoProject(sdlcProject);
		
		MenuCustomizationPage mcp = favspage.gotoMenuFavsCustomization();
		mcp.searchMenuItem("Доска пожеланий");	
		mcp.addFilteredMenuItem("Доска пожеланий");
		mcp.saveChanges();
		favspage =  mcp.close();
		favspage.gotoCustomReport("favs", "", "Доска пожеланий");
		CrossProjectsRequestsBoard cprb = new CrossProjectsRequestsBoard(driver);
		cprb = cprb.turnOnFilter("priority", "all");
		cprb = cprb.moveToAnotherProject(task.getNumericId(), scrumProject.getName(), 1);
		Assert.assertTrue(cprb.isRequestInSection(task.getNumericId(), scrumProject.getName(), 1), "Пожелание не найдено в другом проекте после перемещения");
	
		ScrumPageBase spb =  (ScrumPageBase) cprb.gotoProject(scrumProject);
		IssuesBoardPage ibp = spb.gotoIssuesBoard();
		
		Thread.sleep(2000);
		Assert.assertTrue(ibp.isIssuePresent(task.getNumericId()), "Нет перемещенного пожелания на доске scrum-проекта");
		
		KanbanPageBase kpb = (KanbanPageBase) ibp.gotoProject(kanbanProject);
		KanbanTaskBoardPage ktbp = kpb.gotoKanbanBoard();
		Thread.sleep(2000);
		
		Assert.assertFalse(ktbp.isTaskPresent(task.getNumericId()), "Перемещенная задача осталась на доске kanban-проекта");
		
	}
	
	@Test (priority = 13, dependsOnMethods = { "createProjectsAndUsers" }, description="S-1998")
	public void noAccess() {
		LoginPage lp = (new PageBase(driver)).logOut();
		FavoritesPage fp = lp.loginAs(standaloneUser.getUsername(), standaloneUser.getPass());
		List<String> projects = fp.getProjectsList();
		Assert.assertFalse(projects.contains(sdlcProject.getName()), "В списке проектов ошибочно присутствует SDLC проект");
		Assert.assertFalse(projects.contains(kanbanProject.getName()), "В списке проектов ошибочно присутствует kanban проект");
		Assert.assertFalse(projects.contains(scrumProject.getName()), "В списке проектов ошибочно присутствует scrum-проект");
	}
	
	@Test (priority = 14, dependsOnMethods = { "createProjectsAndUsers" }, description="S-2095")
	public void accessToProjectSettings() {
		LoginPage lp = (new PageBase(driver)).logOut();
		FavoritesPage fp = lp.loginAs(testUser.getUsername(), testUser.getPass());
		
		ScrumPageBase spb =  (ScrumPageBase) fp.gotoProject(scrumProject);
		Assert.assertTrue(spb.isReportAccessible("stg", "menu-folder-workflow", ""), "Координатор не видит раздел 'Состояния'");
		
		lp = (new PageBase(driver)).logOut();
		FileOperations.deleteDirectory(new File(Configuration.getCachePath()));
	    fp = lp.loginAs(scrumUser.getUsername(), scrumUser.getPass());
		spb =  (ScrumPageBase) fp.gotoProject(scrumProject);
		
		Assert.assertFalse(spb.isReportAccessible("stg", "", "Терминология"), "Участник видит 'Терминологию'");
		
		lp = (new PageBase(driver)).logOut();
		fp = lp.loginAs(testUser.getUsername(), testUser.getPass());
		spb =  (ScrumPageBase) fp.gotoProject(scrumProject);
		Assert.assertTrue(spb.isReportAccessible("stg", "menu-folder-workflow", ""), "Координатор не видит раздел 'Состояния'");
		
	}
	
	
}
