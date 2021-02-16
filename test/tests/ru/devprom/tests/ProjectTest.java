package ru.devprom.tests;

import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;
import org.testng.annotations.BeforeClass;
import org.testng.annotations.Test;

import ru.devprom.helpers.DataProviders;
import ru.devprom.helpers.Messages;
import ru.devprom.helpers.ScreenshotsHelper;
import ru.devprom.items.Project;
import ru.devprom.items.RTask;
import ru.devprom.items.Request;
import ru.devprom.items.Template;
import ru.devprom.items.Template.Lang;
import ru.devprom.items.User;
import ru.devprom.pages.FavoritesPage;
import ru.devprom.pages.LoginPage;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.admin.ActivitiesPage;
import ru.devprom.pages.admin.UsersListPage;
import ru.devprom.pages.project.BlocksPage;
import ru.devprom.pages.project.MenuCustomizationPage;
import ru.devprom.pages.project.PermissionsPage;
import ru.devprom.pages.project.ProjectPageBase;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.questions.QuestionNewPage;
import ru.devprom.pages.project.questions.QuestionsPage;
import ru.devprom.pages.project.requests.RequestEditPage;
import ru.devprom.pages.project.requests.RequestNewPage;
import ru.devprom.pages.project.requests.RequestViewPage;
import ru.devprom.pages.project.requests.RequestsBoardPage;
import ru.devprom.pages.project.requests.RequestsPage;
import ru.devprom.pages.project.requirements.RequirementsPage;
import ru.devprom.pages.project.settings.MemberProfilePage;
import ru.devprom.pages.project.settings.ProjectMembersPage;
import ru.devprom.pages.project.settings.SaveTemplatePage;
import ru.devprom.pages.project.tasks.TaskNewPage;
import ru.devprom.pages.project.tasks.TaskViewPage;
import ru.devprom.pages.project.tasks.TasksBoardPage;

public class ProjectTest extends ProjectTestBase {
	Project myTestProject;
	User testUser;

	
	@BeforeClass
	public void assignUser(){
		testUser = new User(username, password, user, "mail", true, true);
	}
	
	@Test
	public void createSDLCProjectTest() {
		ProjectNewPage npp = (new PageBase(driver)).createNewProject();
		Template SDLC = new Template(
				this.waterfallTemplateName);
		String p = DataProviders.getUniqueString();
		this.myTestProject = new Project("MyP1" + p, "MyP1" + DataProviders.getUniqueStringAlphaNum(), SDLC);
		ProjectPageBase sdlcFirstPage = (ProjectPageBase) npp
				.createNew(myTestProject);
		FILELOG.debug("Created new project " + myTestProject.getName());
		Assert.assertEquals(sdlcFirstPage.getProjectTitle(),
				myTestProject.getName());
	}
        
	@Test(dependsOnMethods = "createSDLCProjectTest")
	public void createDuplicateNameProject() {
		ProjectNewPage npp = (new PageBase(driver)).createNewProject();
		Template SDLC = new Template(
				this.waterfallTemplateName);
		String p = DataProviders.getUniqueString();
		Project newTestProject = new Project("MyP1" + p,
				myTestProject.getCodeName(), SDLC);
		npp.createWithError(newTestProject);
		Assert.assertTrue(npp
				.hasErrorMessage(Messages.ERROR_MESSAGE_DUPLICATE_PROJECT_CODENAME
						.getText()));
	}

	@Test(dependsOnMethods = "createSDLCProjectTest")
	public void createBadNameProject() {
		ProjectNewPage npp = (new PageBase(driver)).createNewProject();
		Template SDLC = new Template(
				this.waterfallTemplateName);
		String p = DataProviders.getUniqueString();
		Project newTestProject = new Project("MyP1" + p, "Space name", SDLC);
		npp.createWithError(newTestProject);
		Assert.assertTrue(npp
				.hasErrorMessage(Messages.ERROR_MESSAGE_BAD_PROJECT_NAME
						.getText()));
	}

	@Test(dependsOnMethods = "createSDLCProjectTest")
	public void addMemberToProject() throws InterruptedException {
		ActivitiesPage ap = (new PageBase(driver)).goToAdminTools();
		User member = new User("_member" + DataProviders.getUniqueString(), true);
		UsersListPage ulp = ap.gotoUsers().addNewUser(member, false);
		ProjectMembersPage pmp =  ((SDLCPojectPageBase) ulp.gotoProject(myTestProject)).gotoMembers();
		pmp = pmp.gotoAddMember().addUserToProject(member,"Тестировщик", 2, "Дайджест об изменениях в проекте: ежедневно");
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By.xpath("//tr[contains(@id,'participantlist1_row')]/td[@id='caption' and contains(.,'"+member.getUsername()+"')]")));
	}
	
	@Test (priority = 10, description="S-1878")
	public void verticalMenuCustomization() {
		String p = DataProviders.getUniqueString();
		Template SDLC = new Template(
				this.waterfallTemplateName);
		Template myTemplate = new Template("MyTemplate"+p, "Template for test", "template"+DataProviders.getUniqueStringAlphaNum(), Lang.russian);
		Project projectSource = new Project("MyP1" + p, "MyP1" + DataProviders.getUniqueStringAlphaNum(), SDLC);
		Project projectTarget = new Project("MyP2" + p, "MyP2" + DataProviders.getUniqueStringAlphaNum(), myTemplate);		
		
		ProjectNewPage npp = (new PageBase(driver)).createNewProject();
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) npp.createNew(projectSource);
		FILELOG.debug("Created new project " + projectSource.getName());					
		
		RequirementsPage rp = favspage.gotoRequirements();
		MenuCustomizationPage mcp = rp.gotoMenuReqsCustomization();
		mcp.removeMenuItem("Матрица трассируемости");
		mcp.searchMenuItem("База знаний");		
		mcp.addFilteredMenuItem("База знаний", "Реестр требований");
		mcp.saveChanges();
		favspage =  mcp.close();
		List<String> ss= favspage.getAllVisibleMenuItems();
		Assert.assertFalse(ss.contains("Матрица трассируемости"),"Ссылка на Матрица трассируемости осталась на странице");
		Assert.assertTrue(ss.contains("База знаний"),"Ссылка на базу знаний не появилась на странице");
		
		SaveTemplatePage stp = favspage.gotoSaveTemplatePage();
		stp = stp.saveTemplate(myTemplate);
		Assert.assertTrue(stp.isSuccess(), "Шаблон не был создан");
		
		npp = stp.createNewProject();
		favspage = (SDLCPojectPageBase) npp.createNewSDLCFromUserTemplate(projectTarget);
		FILELOG.debug("Created project " + projectTarget.getName() + " from user Template " + myTemplate);	
		
		rp = favspage.gotoRequirements();
		ss= rp.getAllVisibleMenuItems();
		Assert.assertFalse(ss.contains("Матрица трассируемости"),"Ссылка на Матрица трассируемости появилась в новом проекте");
		Assert.assertTrue(ss.contains("База знаний"),"Ссылка на базу знаний не появилась в новом проекте");
		
	}
	
	@Test (priority = 15, dataProvider = "sp1", dataProviderClass = DataProviders.class, description="S-2098")
	public void rolesAndPermissions(String projectType, String role, String requestType, String requestsReport, String authorAttribute) {
	
		String p = DataProviders.getUniqueString();
		Project	project = new Project("TestProject" + p, "testproject" + DataProviders.getUniqueStringAlphaNum(),
				new Template(projectType));
		
		    LoginPage lp = (new PageBase(driver)).logOut();
	        FavoritesPage fp = lp.loginAs(testUser.getUsername(), testUser.getPass());
	
			ProjectNewPage pnp = fp.createNewProject();
			pnp.createNew(project);
			SDLCPojectPageBase sdlcFirstPage =  new SDLCPojectPageBase(driver); 
			FILELOG.debug("Created new project " + project.getName());

			String roleId = sdlcFirstPage.gotoProjectRolesPage().getRoleId(role);

			PermissionsPage pp = sdlcFirstPage.gotoPermissionsPage();
		    pp.showAll();
		    pp.setFilter("role", roleId);
		    pp.setRight("Настройки: Атрибуты", "none");
			pp.setRight("Бэклог", "none");
			pp.setRight("Обсуждение", "none");
			pp.setRight(authorAttribute, "view");
			
			ProjectMembersPage pmp  = pp.gotoMembers();
			pmp = pmp.assignRole(testUser.getUsernameLong(), role);
			Assert.assertTrue(new ProjectPageBase(driver).isReportAccessible("reqs", "", "Бэклог"), "Недоступен отчет Баклог");
			pmp.gotoSettingsPage();
			Assert.assertTrue(new ProjectPageBase(driver).isSettingAccessible("dicts-pmcustomattribute"), "Недоступен отчет Атрибуты");	
			
			MenuCustomizationPage mcp = new SDLCPojectPageBase(driver).gotoMenuFavsCustomization();
			mcp.searchMenuItem("Все обсуждения");
			if (projectType.equals("Scrum")){
			mcp.addFilteredMenuItem("Все обсуждения", "Бэклог");
			mcp.saveChanges();
			mcp.close();
			new ProjectPageBase(driver).gotoCustomReport("favs", "menu-folder-settings", "Все обсуждения");
			}
			else {
			mcp.addFilteredMenuItem("Все обсуждения", "Бэклог");
			mcp.saveChanges();
			mcp.close();
			new ProjectPageBase(driver).gotoCustomReport("favs", "", "Все обсуждения");
			}
			QuestionsPage qp = new QuestionsPage(driver);
			QuestionNewPage qnp = qp.addNewQuestion();
			qp = qnp.createNewQuestion("Новый вопрос");
			
			pp.gotoCustomReport("reqs", "", "Бэклог");
			RequestsPage rp = new RequestsPage(driver);
			RequestNewPage rnp = rp.clickNewRequestUserType(requestType);
			Request request = new Request("TestRequest" + p);
			rp = rnp.createCRShort(request);
			RequestViewPage rvp = rp.clickToRequest(request.getId());
			
			RequestEditPage rep = rvp.gotoEditRequest();
			Assert.assertTrue(rep.isElementPresent(By.id("AuthorText")), "Поле Автор недоступно для редактирования владельцу проекта");
			rep.close();
			
			//Создание и использование участника проекта
			User testuser = new User("Member"+p, true);
			ActivitiesPage ap = fp.goToAdminTools();
			UsersListPage ulp = ap.gotoUsers();
			ulp = ulp.addNewUser(testuser, false);
			FILELOG.debug("Created: " + testuser.getUsername());
			
			ulp.gotoProject(project);
			pmp = new SDLCPojectPageBase(driver).gotoMembers();
			pmp = pmp.gotoAddMember().addUserToProject(testuser, role, 10, "Дайджест об изменениях в проекте: ежедневно");
			
			lp = (new PageBase(driver)).logOut();
			fp = lp.loginAs(testuser.getUsername(), testuser.getPass());
			ProjectPageBase ppb = (ProjectPageBase) fp.gotoProject(project);
			Assert.assertFalse(new ProjectPageBase(driver).isReportAccessible("reqs", "", "Бэклог"), "Ошибочно доступен отчет Баклог");
			ppb.gotoSettingsPage();
			Assert.assertFalse(new ProjectPageBase(driver).isSettingAccessible("dicts-pmcustomattribute"), "Ошибочно доступен отчет Атрибуты");	

			mcp = new SDLCPojectPageBase(driver).gotoMenuFavsCustomization();
		    Assert.assertFalse(mcp.isItemExists("Все обсуждения"), "Ошибочно доступен отчет Обсуждения");
			mcp.close();
			
			new ProjectPageBase(driver).gotoCustomReport("reqs", "", requestsReport);
			
			rvp = new RequestsBoardPage(driver).clickToRequest(request.getId());
			rep = rvp.gotoEditRequest();
			Assert.assertFalse(rep.isElementPresent(By.id("AuthorText")), "Поле Автор доступно для редактирования участнику проекта");
			rep.close();
	}
	
	@Test  (priority = 16, description="S-2000")
	public void blockMember() {
		 String p = DataProviders.getUniqueString();
	       User member = new User("m"+p, true); 
     	   Project sdlcProject= new Project("sdlcProject"+p, "sdlcproject"+DataProviders.getUniqueStringAlphaNum(),
					new Template(this.waterfallTemplateName));
     	   Request request = new Request("Доработка"+p, "", "Высокий", 10.0, "Доработка");
     	    RTask task = new RTask("Задача-1", member.getUsernameLong(), "Разработка", 6.0);
     	   task.setIteration("0.1");
     	   RTask task2 = new RTask("Задача-2", member.getUsernameLong(), "Разработка", 6.0);
     	    
     	    LoginPage lp = (new PageBase(driver)).logOut();
	        FavoritesPage fp = lp.loginAs(testUser.getUsername(), testUser.getPass());
	
     	    ActivitiesPage ap = fp.goToAdminTools();
			UsersListPage ulp = ap.gotoUsers();
			ulp = ulp.addNewUser(member, false);
			FILELOG.debug("Created: " + member.getUsername());
			
			ProjectNewPage pnp = new PageBase(driver).createNewProject();
			pnp.createNew(sdlcProject);
			FILELOG.debug("Created new project " + sdlcProject.getName());

			ProjectMembersPage pmp = (new SDLCPojectPageBase(driver)).gotoMembers();
			pmp = pmp.gotoAddMember().addUserToProject(member, "Разработчик", 10, "Дайджест об изменениях в проекте: ежедневно");
			
			RequestsPage mip = pmp.gotoRequests();
			RequestNewPage rnp = mip.clickNewCR();
			mip = rnp.createNewCR(request);
			RequestsBoardPage rbp = mip.gotoRequestsBoard();
			rbp  = rbp.addTask(request.getNumericId(), task);
			ap = rbp.goToAdminTools();
			ulp = ap.gotoUsers();
			BlocksPage bp = ulp.blockUser(member.getUsernameLong());
			
			lp = bp.logOut();
			lp.typeUsername(member.getUsername());
			lp.typePassword(member.getPass());
			lp.submitExpectingFailure();
			Assert.assertTrue(lp.getErrorMessage().contains("Ваша учетная запись заблокирована"), "Нет сообщения о блокировке учетной записи");
			
			fp = lp.loginAs(testUser.getUsername(), testUser.getPass());
			
			fp.gotoProject(sdlcProject);
			pmp = (new SDLCPojectPageBase(driver)).gotoMembers();
			Assert.assertFalse(pmp.isMember(member.getUsernameLong()), "Заблокированный пользователь по-прежнему в списке участников");
			
			TasksBoardPage tbp  = rbp.gotoTasksBoard();
			TaskViewPage tvp = tbp.clickToTaskByName(task.getName());
			Assert.assertEquals(tvp.readOwner(), member.getUsernameLong(), "Задача назначена не на того пользователя");
			
			
			rbp = tvp.gotoRequestsBoard();
			TaskNewPage tnp = rbp.addTask(request.getNumericId());
			List<String> ownersList = tnp.getExecutorCandidatesList();
			Assert.assertFalse(ownersList.contains(member.getUsernameLong()), "Заблокированный пользователь может быть выбран для назначения");
			tnp.cancel();
			
			driver.navigate().refresh();
			ap = tnp.goToAdminTools();
			bp = ap.gotoBlockedUsers();
			bp = bp.unblockUser(member.getUsernameLong());
			Assert.assertFalse(bp.isUserInList(testUser.getUsernameLong()), "Пользователь все еще в списке заблокированных");
			
			
			fp.gotoProject(sdlcProject);
			pmp = (new SDLCPojectPageBase(driver)).gotoMembers();
			Assert.assertTrue(pmp.isMember(member.getUsernameLong()), "Разблокированного пользователя нет в списке участников");
			
			rbp = mip.gotoRequestsBoard();
			rbp  = rbp.addTask(request.getNumericId(), task2);
			
			pmp = rbp.gotoMembers();
			MemberProfilePage mpp = pmp.gotoMemberProfile(member.getUsernameLong());
			mpp.excludeFromProject();
			pmp = mpp.saveChanges();
			
			rbp = (new SDLCPojectPageBase(driver)).gotoRequestsBoard();
			tnp = rbp.addTask(request.getNumericId());
			ownersList = tnp.getExecutorCandidatesList();
			Assert.assertFalse(ownersList.contains(member.getUsernameLong()), "Исключенный из проекта пользователь может быть выбран для назначения");
			tnp.cancel();
			tbp  = rbp.gotoTasksBoard();
			tvp = tbp.clickToTaskByName(task.getName());
			Assert.assertEquals(tvp.readOwner(), member.getUsernameLong(), "Задача " + task.getName() + " назначена не на того пользователя");
			
			tbp  = tvp.gotoTasksBoard();
			tvp = tbp.clickToTaskByName(task2.getName());
			Assert.assertEquals(tvp.readOwner(), member.getUsernameLong(), "Задача " + task2.getName() + " назначена не на того пользователя");
	}
}