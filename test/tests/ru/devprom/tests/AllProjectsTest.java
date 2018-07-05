package ru.devprom.tests;

import java.util.List;

import org.testng.Assert;
import org.testng.annotations.Test;

import ru.devprom.helpers.DataProviders;
import ru.devprom.items.Group;
import ru.devprom.items.User;
import ru.devprom.pages.FavoritesPage;
import ru.devprom.pages.LoginPage;
import ru.devprom.pages.MyProjectsPageBase;
import ru.devprom.pages.MyReportsPageBase;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.admin.AccessPermissionsPage;
import ru.devprom.pages.admin.ActivitiesPage;
import ru.devprom.pages.admin.AdminPageBase;
import ru.devprom.pages.admin.GroupsPage;
import ru.devprom.pages.admin.UsersListPage;
import ru.devprom.pages.allprojects.AllProjectsFunctionsGraphPage;
import ru.devprom.pages.allprojects.AllProjectsPageBase;
import ru.devprom.pages.allprojects.AllProjectsReleaseGraphPage;
import ru.devprom.pages.allprojects.AllProjectsTimetableReportPage;
import ru.devprom.pages.project.AllReportsPage;

public class AllProjectsTest extends AdminTestBase {

	@Test(description="S-1685")
	public void allProjectsReports() {
		
		String p = DataProviders.getUniqueString();
		
		//Создаем нового пользователя 
		UsersListPage ulp = (new AdminPageBase(driver)).gotoUsers();
		User testUser = new User(p, true);
		ulp = ulp.addNewUser(testUser, false);
		FILELOG.debug("Created user: " + testUser.getUsername());
		
		//Создаем новую группу
		GroupsPage gp = (new AdminPageBase(driver)).gotoGroups();
		Group testGroup = new Group("GroupForAllProjects" + p,
				"Test description");
		gp = gp.addGroup(testGroup);
		FILELOG.debug("One new group has been created: " + testGroup.getName());
		
		
		//Включаем созданного пользователя в созданную группу 
		gp = gp.addUser(testGroup, testUser);
		
		//Даем группе права на портфель "Все проекты"
		AccessPermissionsPage app = gp.gotoAccessPermissions();
		app = app.givePermissions(testGroup.getName(), "всем проектам");
		
		//Заходим под созданным пользователем
		LoginPage lp = app.logOut();
		FavoritesPage fp = lp.loginAs(testUser.getUsername(), testUser.getPass());
		
		Assert.assertTrue(fp.isAllProjectsEnabled(), "Пользователь не имеет доступ к портфелю 'Все проекты'");
		
		//Проверяем отчеты
		AllProjectsPageBase appb = fp.gotoAllProjects();
		AllProjectsTimetableReportPage aptrp = appb.gotoTimetableReport();
		Assert.assertTrue(aptrp.getTablesRowCount()>0,"Нет ни одной строчки в таблице затраченного времени");
		
		//Заходим под администратором
		lp = app.logOut();
	    fp = lp.loginAs(username, "1");
	
	     //Удаляем группу из системы
	  		ActivitiesPage ap = aptrp.goToAdminTools();
	  		gp = ap.gotoGroups();
	  		gp.deleteGroup(testGroup);
				 
		//Заходим под созданным пользователем
		 lp = app.logOut();
		 fp = lp.loginAs(testUser.getUsername(), testUser.getPass());
		
		 boolean isEnabled = new PageBase(driver).isAllProjectsEnabled();
		 lp = app.logOut();
		 fp = lp.loginAs(username, "1");
		Assert.assertFalse(isEnabled, "Пользователь все еще имеет доступ к портфелю 'Все проекты'");
		
	}
	
	@Test(description="S-1685")
	public void myProjectsReports() {
		//Проверяем отчеты
		MyProjectsPageBase mppb = (new AdminPageBase(driver)).gotoMyProjects();
		MyReportsPageBase mrpb = mppb.gotoMyReports();
		List<AllReportsPage.Report> reports = mrpb.getAllReportsList();
		int failedCount = 0;
		for (AllReportsPage.Report report:reports){
			
			mrpb.checkReport(report);
			FILELOG.debug("Checked report <"+report.name + "> STATUS: " +report.status);
		    if (!report.status.equals("verified")) failedCount++;
			driver.navigate().back();
		
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
	
}
