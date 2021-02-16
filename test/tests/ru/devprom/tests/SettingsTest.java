package ru.devprom.tests;

import org.testng.Assert;
import org.testng.annotations.Test;

import ru.devprom.helpers.DataProviders;
import ru.devprom.items.Project;
import ru.devprom.items.Template;
import ru.devprom.items.User;
import ru.devprom.pages.FavoritesPage;
import ru.devprom.pages.LoginPage;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.admin.ActivitiesPage;
import ru.devprom.pages.admin.UsersListPage;
import ru.devprom.pages.project.AddMemberPage;
import ru.devprom.pages.project.MenuCustomizationPage;
import ru.devprom.pages.project.ProjectCommonSettingsPage;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requests.RequestsPage;
import ru.devprom.pages.project.settings.MySettingsPage;
import ru.devprom.pages.project.settings.ProjectMembersPage;

public class SettingsTest extends ProjectTestBase {

	
	/**
	 * Тест сохраняет пользовательские настройки отчета и меню, проверяет их корректное отображение 
	 * для другого члена проекта, затем проверяет как работает сброс к изначальным настройкам.
	 */
	@Test
	public void customSettingsForAllMembers() throws InterruptedException {
		PageBase page = new PageBase(driver);
		 String p = DataProviders.getUniqueString();
		 Project project = new Project("SDLCProject"+p, "sdlc"+DataProviders.getUniqueStringAlphaNum(),new Template(this.waterfallTemplateName));
			
		 ProjectNewPage pnp = page.createNewProject();
		SDLCPojectPageBase sdlc =  (SDLCPojectPageBase)pnp.createNew(project);
  		FILELOG.debug("Created new project " + project.getName());
  		
  		RequestsPage mip = sdlc.gotoRequests();
		mip.showColumn("Type");
		mip.setFilter("owner", "user-id");
		
		Assert.assertTrue(mip.isColumnPresent("type"), "Нет колонки Тип после добавления оригинальным пользователем");
		Assert.assertTrue(mip.isFilterPresent("owner"), "Нет фильтра Автор после добавления оригинальным пользователем");
		Assert.assertTrue(sdlc.isReportAccessible("favs", "", "Бэклог"), "Не виден отчет Баклог в Избранном для оригинального пользователя");
	
		mip.savePageSettins();
		
		MenuCustomizationPage mcp=  mip.gotoMenuFavsCustomization();
	    mcp.searchMenuItem("Все обсуждения");
		mcp.addFilteredMenuItem("Все обсуждения");
		mcp.saveChanges();
		sdlc = mcp.close();
		
		MySettingsPage msp = sdlc.gotoMySettingsPage();
		msp.saveModuleSettingsForAll();
		
		ActivitiesPage ap = msp.goToAdminTools();
	    UsersListPage ulp = ap.gotoUsers();
	    User member = new User(p, true);
	    ulp = ulp.addNewUser(member, false);
	    sdlc = (SDLCPojectPageBase)ulp.gotoProject(project);
	    ProjectMembersPage pmp = sdlc.gotoMembers();
	    AddMemberPage amp = pmp.gotoAddMember();
	    pmp = amp.addUserToProject(member, "Разработчик", 2,  "Дайджест об изменениях в проекте: ежедневно");
	    LoginPage lp = pmp.logOut();
	    FavoritesPage fp = lp.loginAs(member.getUsername(), member.getPass());
	    sdlc = (SDLCPojectPageBase)  fp.gotoProject(project);
	    Assert.assertTrue(sdlc.isReportAccessible("favs", "", "Все обсуждения"), "Не виден отчет Обсуждения в Избранном для члена проекта");
	    mip = sdlc.gotoRequests();
	    Assert.assertTrue(mip.isColumnPresent("type"), "Нет колонки Тип в модуле Баклог члена проекта");
		Assert.assertTrue(mip.isFilterPresent("owner"), "Нет фильтра Автор в модуле Баклог члена проекта");
		
		lp = pmp.logOut();
		fp = lp.loginAs(username, password);
		sdlc = (SDLCPojectPageBase)  fp.gotoProject(project);
		ProjectCommonSettingsPage spg = sdlc.gotoCommonSettings();
		spg.resetModuleSettings();
		mip = msp.gotoRequests();
		
		Assert.assertFalse(mip.isColumnPresent("type"), "Осталась колонка Тип после сбрасывания настроек");
		Assert.assertFalse(mip.isFilterPresent("owner"), "Остался фильтр Автор после сбрасывания настроек");
		Assert.assertFalse(sdlc.isReportAccessible("favs", "", "Все обсуждения"), "Остался виден отчет Обсуждения в Избранном для оригинального пользователя");
	
		lp = pmp.logOut();
	    fp = lp.loginAs(member.getUsername(), member.getPass());
	    sdlc = (SDLCPojectPageBase)  fp.gotoProject(project);
	    mip = sdlc.gotoRequests();
		Assert.assertFalse(mip.isColumnPresent("type"), "Осталась колонка Тип после сбрасывания настроек - для члена проекта");
		Assert.assertFalse(mip.isFilterPresent("owner"), "Остался фильтр Автор после сбрасывания настроек - для члена проекта");
		Assert.assertFalse(sdlc.isReportAccessible("favs", "", "Все обсуждения"), "Остался виден отчет Обсуждения в Избранном для члена проекта");
	
	}

	
}
