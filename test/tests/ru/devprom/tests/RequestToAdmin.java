package ru.devprom.tests;

import org.testng.Assert;
import org.testng.annotations.Test;

import ru.devprom.helpers.DataProviders;
import ru.devprom.items.Project;
import ru.devprom.items.Template;
import ru.devprom.items.User;
import ru.devprom.pages.FavoritesPage;
import ru.devprom.pages.LoginPage;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.SendRequestForm;
import ru.devprom.pages.admin.ActivitiesPage;
import ru.devprom.pages.admin.CommonSettingsPage;
import ru.devprom.pages.admin.UsersListPage;
import ru.devprom.pages.project.ProjectPageBase;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requests.RequestViewPage;

public class RequestToAdmin extends ProjectTestBase {
	
	Project adminProject;
	
	/** The method creates Administrative Project  */
	@Test
	public void requestToAdministrator() {
		String requestText = "Test Request To Admin";
		String p = DataProviders.getUniqueString();
		
		//return to default project and create new user
		ActivitiesPage ap = (new PageBase(driver)).goToAdminTools();
		UsersListPage ulp = ap.gotoUsers();
		User user = new User("UserRequestTest"+p, "1", "UserRequestTest"+p, "UserRequestTest"+p+"@mail.com", false, true);
		ulp = ulp.addNewUser(user, true);
		FILELOG.debug("Created user " + user.getUsername());
		
		//Setup Administration Project
		CommonSettingsPage csp = ulp.gotoCommonSettings();
		String name = csp.createAdministrativeProject();
		Assert.assertEquals(name, "Администрирование DEVPROM", "Неверное имя созданного проекта администрирования");
		//re-login with the new user
		LoginPage lp =  csp.logOut();
		FavoritesPage fp = lp.loginAs(user.getUsername(), user.getPass());
		
		//sending request to Admin
		SendRequestForm srf = fp.sendRequestToAdmin();
		srf.send(requestText);
		
		//Going to the Request and check it's text
		RequestViewPage rvp = srf.gotoRequestLink();
		String requestDescription = rvp.readDescription();
		//Assert.assertEquals(rvp.readOriginator(), user.getUsernameLong());
		Assert.assertEquals(requestDescription, requestText);
	}
}
