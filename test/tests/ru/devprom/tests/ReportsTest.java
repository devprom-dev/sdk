package ru.devprom.tests;

import java.util.List;

import org.openqa.selenium.By;
import org.testng.Assert;
import org.testng.annotations.AfterClass;
import org.testng.annotations.AfterMethod;
import org.testng.annotations.BeforeMethod;
import org.testng.annotations.Test;

import ru.devprom.helpers.Configuration;
import ru.devprom.helpers.DataProviders;
import ru.devprom.items.Project;
import ru.devprom.items.Template;
import ru.devprom.items.User;
import ru.devprom.items.Template.Lang;
import ru.devprom.pages.LoginPage;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.admin.AdminPageBase;
import ru.devprom.pages.admin.UsersListPage;
import ru.devprom.pages.project.AllReportsPage;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.SaveReportPage;
import ru.devprom.pages.project.requests.RequestsPage;
import ru.devprom.pages.project.settings.ProjectMembersPage;
import ru.devprom.pages.project.settings.SaveTemplatePage;

public class ReportsTest extends TestBase {

	@Test
	public void testOpenReports() {
		String p = DataProviders.getUniqueString();
		Template SDLC = new Template(
				this.waterfallTemplateName);
		Project project = new Project("MyP1" + p, "MyP1" + p, SDLC);
		ProjectNewPage npp = (new PageBase(driver)).createNewProject();
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) npp.createNew(project);
		FILELOG.debug("Created new project " + project.getName());					
		
		AllReportsPage arp = favspage.gotoAllReports();
		List<AllReportsPage.Report> reports = arp.getAllReportsList();
		int failedCount = 0;
		String reportsUrl = driver.getCurrentUrl();
		for (AllReportsPage.Report report:reports){
			
			//System.out.println(report);
			arp.checkReport(report);
			FILELOG.debug("Checked report <"+report.name + "> STATUS: " +report.status);
		    if (!report.status.equals("verified")) failedCount++;
		    driver.navigate().to(reportsUrl);
		
		}
		if (failedCount>0) {
			for (AllReportsPage.Report report:reports){
				if (!report.status.equals("verified")) {
					FILELOG.error("Report verification failed. Report name: "+report.name+". Status: " +report.status);
				}
			}
			Assert.fail("Some of the reports failed. See the log file for statuses.");
		}
				
	}
	
	@Test(description="S-1962")
	public void copyReportToAnotherUser() {
		String p = DataProviders.getUniqueString();
		String reportName = "Мой Баклог" + p;
		String newUserReportName = "Отчет пользователя" + p;
		User user = new User(p, true);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(
						this.waterfallTemplateName));
		SDLCPojectPageBase page =  (SDLCPojectPageBase) new PageBase(driver).gotoProject(webTest);		
		RequestsPage mip = page.gotoRequests();
		mip  = mip.selectFilterValue("state", "Все");
		mip  = mip.selectFilterValue("state", "В релизе");
		SaveReportPage srp = mip.saveReport();
		srp.saveReport(reportName, true, "");
		
		page.gotoCustomReport("favs", "", reportName);
		String filtered = page.readFilterCaption("state");
		Assert.assertTrue(filtered.contains("В релизе"), "Фильтр Состояние не содержит В релизе");
	
		String reportURL = driver.getCurrentUrl();
        page.goToAdminTools();
        UsersListPage ulp = (new AdminPageBase(driver)).gotoUsers();	
        ulp = ulp.addNewUser(user, false);
		FILELOG.debug("Created: " + user.getUsername());
		
		page =  (SDLCPojectPageBase) page.gotoProject(webTest);		
		ProjectMembersPage pmp = page.gotoMembers();
		pmp = pmp.gotoAddMember().addUserToProject(user, "Тестировщик", 2,
				"Дайджест об изменениях в проекте: ежедневно");
		
		LoginPage lp = pmp.logOut();
		lp.loginAs(user.getUsername(), user.getPass());
		driver.get(reportURL);
		filtered = page.readFilterCaption("state");
		Assert.assertTrue(filtered.contains("В релизе"), "Фильтр Состояние не содержит В релизе");
		srp = mip.saveReport();
		srp.saveReport(newUserReportName, true, "");
		
		page.gotoCustomReport("favs", "", newUserReportName);
		filtered = page.readFilterCaption("state");
		Assert.assertTrue(filtered.contains("В релизе"), "Фильтр Состояние не содержит В релизе");
	}
	
	
	@Test(description="S-1963")
	public void reportRecycling() {
		String p = DataProviders.getUniqueString();
		String reportName = "Мой Баклог" + p;
		Template myTemplate = new Template("MyTemplate"+p, "Template for test", "template"+p, Lang.russian);
		Project project = new Project("MyTemplateProject" + p, "mtp" + p, myTemplate);
		User user = new User(p, true);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(
						this.waterfallTemplateName));
		SDLCPojectPageBase page =  (SDLCPojectPageBase) new PageBase(driver).gotoProject(webTest);		
		RequestsPage mip = page.gotoRequests();
		mip  = mip.selectFilterValue("state", "Все");
		mip  = mip.selectFilterValue("state", "В релизе");
		mip.addColumn("Attachment");
		mip.addColumn("RecentComment");
		SaveReportPage srp = mip.saveReport();
		srp.saveReport(reportName, true, "");
		
		SaveTemplatePage stp = page.gotoSaveTemplatePage();
		stp = stp.saveTemplate(myTemplate);
		Assert.assertTrue(stp.isSuccess(), "Шаблон не был создан");
		
		ProjectNewPage npp = stp.createNewProject();
		page = (SDLCPojectPageBase) npp.createNewSDLCFromUserTemplate(project);
		FILELOG.debug("Created project " + project.getName() + " from user Template " + myTemplate);	
		
		page.gotoCustomReport("favs", "", reportName);
		
		String filtered = page.readFilterCaption("state");
		Assert.assertTrue(filtered.contains("В релизе"), "Фильтр Состояние не содержит В релизе");
		Assert.assertTrue(page.isColumnPresent("attachment"), "Отсутствует колонка Приложения");
		Assert.assertTrue(page.isColumnPresent("recentcomment"), "Отсутствует колонка Комментарии");
		
        page.goToAdminTools();
        UsersListPage ulp = (new AdminPageBase(driver)).gotoUsers();	
        ulp = ulp.addNewUser(user, false);
		FILELOG.debug("Created: " + user.getUsername());
		
		page =  (SDLCPojectPageBase) page.gotoSDLCProject(project.getName());		
		ProjectMembersPage pmp = page.gotoMembers();
		pmp = pmp.gotoAddMember().addUserToProject(user, "Тестировщик", 2,
				"Дайджест об изменениях в проекте: ежедневно");
		
		LoginPage lp = pmp.logOut();
		lp.loginAs(user.getUsername(), user.getPass());
		page =  (SDLCPojectPageBase) page.gotoSDLCProject(project.getName());		
		page.gotoCustomReport("favs", "", reportName);
		filtered = page.readFilterCaption("state");
		Assert.assertTrue(filtered.contains("В релизе"), "Фильтр Состояние не содержит В релизе");
		Assert.assertTrue(page.isColumnPresent("attachment"), "Отсутствует колонка Приложения");
		Assert.assertTrue(page.isColumnPresent("recentcomment"), "Отсутствует колонка Комментарии");
	}
	
	
	
	
	@BeforeMethod
	public void doLogin() throws InterruptedException {
		driver.get(Configuration.getBaseUrl());
		new LoginPage(driver).loginAs(username, password);
	}
	
	@AfterMethod
	public void doLogout() throws InterruptedException {
		FILELOG.debug("do logout");
		driver.findElement(By.id("navbar-user-menu")).click();
		driver.findElement(By.xpath("//a[@href='/logoff']")).click();
		// catching "Вы действительно хотите покинуть страницу?" alert
		try {
			driver.switchTo().alert().accept();
		} catch (org.openqa.selenium.NoAlertPresentException e) {
			// no alert no problem
		}
		FILELOG.info("Logout done");

	}
	
}
