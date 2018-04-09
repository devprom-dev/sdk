package ru.devprom.tests;

import org.testng.Assert;
import org.testng.annotations.Test;

import ru.devprom.helpers.DataProviders;
import ru.devprom.items.User;
import ru.devprom.pages.admin.BackUpsPage;
import ru.devprom.pages.admin.PluginsPage;
import ru.devprom.pages.admin.UserEditPage;
import ru.devprom.pages.admin.UsersListPage;

@Test(groups = "System")
public class UBackUpTest extends AdminTestBase {

	// Not tested
	@Test
	public void testRestoreFromReserveCopy() {
		User testUser1;
		User testUser2;
		// create new User
		driver.get(baseURL + "/admin/users.php");
		FILELOG.debug("Go to Users page. Creating new user.");
		UsersListPage ulp = new UsersListPage(driver);
		testUser1 = new User(DataProviders.getUniqueString(), true);
		ulp = ulp.addNewUser(testUser1, false);
		FILELOG.debug(testUser1.toString() + " has been created.");
		
		// go to "Резервные копии" page
		BackUpsPage bup = ulp.gotoBackUpsPage();
		FILELOG.debug("Go to BackUps page. Creating backup.");
		bup.makeBackUp();

		// go to users, find our guy and kill him
		ulp = bup.gotoUsers();
		UserEditPage vup = ulp.editUser(testUser1.getUsernameLong());
		ulp = vup.deleteUser();
		FILELOG.debug(testUser1 + " has been deleted");
				
		// create another user
		testUser2 = new User(DataProviders.getUniqueString(), true);
		ulp = ulp.addNewUser(testUser2, false);
		FILELOG.debug(testUser2.toString() + " has been created.");
		
		//go to Plugins and turn off "Story Mapping"
		PluginsPage pp = ulp.gotoPlugins();
		pp.disablePlugin("storymapping.php");
		// go to "Резервные копии" and restore last backup
		bup = ulp.gotoBackUpsPage();
		FILELOG.debug("Preparing to restore");
		bup.restoreLastBackUp();
		FILELOG.debug("System has been restored from the last backup");

		// check if testUser1 exists and testUser2 disappeared
		ulp = bup.gotoUsers();
		Assert.assertTrue(ulp.isUserExist(testUser1.getUsernameLong()));
		Assert.assertFalse(ulp.isUserExist(testUser2.getUsernameLong()));
		
		pp = ulp.gotoPlugins();
		Assert.assertTrue(pp.isPluginEnabled("storymapping.php"), "Плагин 'Story Mapping' выключен");

	}

}
