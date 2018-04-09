package ru.devprom.tests;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

import org.testng.Assert;
import org.testng.annotations.Test;

import ru.devprom.helpers.Configuration;
import ru.devprom.helpers.DataProviders;
import ru.devprom.items.Group;
import ru.devprom.items.Project;
import ru.devprom.items.Template;
import ru.devprom.items.User;
import ru.devprom.pages.FavoritesPage;
import ru.devprom.pages.LoginPage;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.admin.AdminPageBase;
import ru.devprom.pages.admin.GroupsPage;
import ru.devprom.pages.admin.UserEditPage;
import ru.devprom.pages.admin.UsersListPage;
import ru.devprom.pages.project.ProjectActivitiesPage;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.settings.MemberProfilePage;
import ru.devprom.pages.project.settings.MySettingsPage;
import ru.devprom.pages.project.settings.ProjectMembersPage;

public class UserTest extends AdminTestBase {
	User testUser;
	Group g1, g2, g3;

	@Test
	public void addSimpleUserTest() throws InterruptedException {
		UsersListPage ulp = (new AdminPageBase(driver)).gotoUsers();
		int wasUsers = ulp.getUsersCount();
		FILELOG.debug("Going to create 2 new users");
		for (int i = 0; i < 2; i++) {
			String p = DataProviders.getUniqueString();
			testUser = new User(p, true);
			ulp = ulp.addNewUser(testUser, false);
			FILELOG.debug("Created: " + testUser.getUsername());
		}
		int attempts = 5;
		while( attempts-- > 0 ) {
			Thread.sleep(1000);
			if ( ulp.getUsersCount() == wasUsers + 2 ) {
				org.testng.Assert.assertTrue(true);
				return;
			}
		}
		org.testng.Assert.assertEquals(ulp.getUsersCount(), wasUsers + 2);
	}

	@Test
	public void addFullUserTest() throws InterruptedException {
		g1 = new Group("TestGroup" + DataProviders.getUniqueString(),
				"Group for Users test");
		g2 = new Group("TestGroup" + DataProviders.getUniqueString(),
				"Group for Users test");
		GroupsPage gp = (new AdminPageBase(driver)).gotoGroups();
		gp = gp.addGroup(g1);
		gp = gp.addGroup(g2);
		FILELOG.debug("Created 2 groups: " + g1.getName() + " and "
				+ g2.getName());
		UsersListPage ulp = gp.gotoUsers();
		int wasUsers = ulp.getUsersCount();
		FILELOG.debug("Users count before" + wasUsers);
		String[] groups = { g1.getName(), g2.getName() };
		String p;
		for (int i = 0; i < 2; i++) {
			p = DataProviders.getUniqueString();
			User user = new User("Test" + p, "Test" + p, "Test" + p
					+ " Long Name", "test" + p + " email.com", false, false, p
					+ p, p + p + p, groups, true);
			ulp = ulp.addNewUser(user, true);
			FILELOG.debug("Created user: " + user.getUsername());
		}
		int attempts = 5;
		while( attempts-- > 0 ) {
			Thread.sleep(1000);
			if ( ulp.getUsersCount() == wasUsers + 2 ) {
				org.testng.Assert.assertTrue(true);
				return;
			}
		}
		Assert.assertEquals(ulp.getUsersCount(), wasUsers + 2);
	}

	@Test
	public void addUsersAndCompare() {
		UsersListPage ulp = (new AdminPageBase(driver)).gotoUsers();
		List<User> userBefore = ulp.getAllUsers();
		List<User> usersAdded = new ArrayList<User>();
		for (int i = 0; i < 2; i++) {
			String p = DataProviders.getUniqueString();
			usersAdded.add(new User(p, true));
			ulp = ulp.addNewUser(usersAdded.get(i), true);
			FILELOG.debug("Created user: " + usersAdded.get(i).getUsername());
		}

		List<User> usersAfter = ulp.getAllUsers();
		usersAfter.removeAll(userBefore);

		// Put the sets into arrays and sort them to assert the arrays are equal
		User[] usersAfterSorted = usersAfter.toArray(new User[0]);
		Arrays.sort(usersAfterSorted);
		User[] usersAddedSorted = usersAdded.toArray(new User[0]);
		Arrays.sort(usersAddedSorted);

		// Print our sorted arrays just for debugging
		/*
		 * System.out.println("usersAfter: "); for (User u:usersAfterSorted){
		 * System.out.println(u); } System.out.println("usersAdded: "); for
		 * (User u:usersAddedSorted){ System.out.println(u); }
		 */
		Assert.assertEquals(usersAddedSorted, usersAfterSorted);
	}

	@Test(dependsOnMethods = "addSimpleUserTest")
	public void deleteUsers() {
		UsersListPage ulp = (new AdminPageBase(driver)).gotoUsers();
		int wasUsers = ulp.getUsersCount();
		ulp = ulp.editUser(testUser.getUsernameLong()).deleteUser();
		FILELOG.debug("Deleted user" + testUser.getUsername());
		Assert.assertEquals(ulp.getUsersCount(), wasUsers - 1);
	}

	// run to delete all the users except for "excludeUsers", run this only for
	// clean up your DB
	// @Test
	public void clearUsers() {
		String[] excludeUsers = { "Artiom Hmelevsky" };
		UsersListPage ulp = (new AdminPageBase(driver)).gotoUsers();
		ulp.deleteAllButThis(excludeUsers);
		FILELOG.debug("Deleted all users but" + excludeUsers.toString());
		Assert.assertTrue(ulp.getUsersCount() == excludeUsers.length);
	}

	@Test
	public void editUser() {
		g1 = new Group("TestGroup" + DataProviders.getUniqueString(),
				"Group for Users test");
		g2 = new Group("TestGroup" + DataProviders.getUniqueString(),
				"Group for Users test");
		g3 = new Group("TestGroup" + DataProviders.getUniqueString(),
				"Group for Users test");
		GroupsPage gp = (new AdminPageBase(driver)).gotoGroups();
		gp = gp.addGroup(g1);
		gp = gp.addGroup(g2);
		gp = gp.addGroup(g3);
		FILELOG.debug("Created 3 new groups: " + g1.getName() + " and "
				+ g2.getName() + " and " + g3.getName());
		String[] groups = { g1.getName(), g2.getName() };
		UsersListPage ulp = gp.gotoUsers();
		String p = DataProviders.getUniqueString();
		User user = new User("Test" + p, "Test" + p, "Test" + p + " Long Name",
				"test" + p + " email.com", false, true, p + p, p + p + p,
				groups, true);
		ulp = ulp.addNewUser(user, true);
		FILELOG.debug("Created user " + user.getUsername());
		user.setDescription("New description");
		user.setGroups(new String[] { g3.getName() });
		FILELOG.debug("Set groups for user " + user.getUsername());
		UserEditPage vup = ulp.editUser(user.getUsernameLong());
		vup.editUser(user);
		vup = ulp.editUser(user.getUsernameLong());
		user.isValid = false;
		User user2 = vup.readUser();
		vup.close();
		Assert.assertTrue(user2.isLike(user));
	}

	/**Test creates new user, assign a coordinator role and includes it to the default project 
	 * @throws InterruptedException */
	@Test
	public void includeCoordinatorToProject() throws InterruptedException {
        //Создаем нового пользователя 
        		UsersListPage ulp = (new AdminPageBase(driver)).gotoUsers();
				String p = DataProviders.getUniqueString();
				User coordinator = new User(p, true);
				ulp = ulp.addNewUser(coordinator, false);
				FILELOG.debug("Created: " + coordinator.getUsername());
		//Переходим в основной проект
				Template SDLC = new Template(
						this.waterfallTemplateName);
				Project project = new Project("DEVPROM.WebTest", "devprom_webtest",SDLC);
				ProjectMembersPage pmp = ((SDLCPojectPageBase) (new PageBase(driver))
						.gotoProject(project)).gotoMembers();
		//Добавляем пользователя в проект с ролью "Руководитель проекта"
				pmp = pmp.gotoAddMember().addUserToProject(coordinator, "Руководитель проекта", 2,	"");
		//Убеждаемся в том, что пользователь и его роль отображается правильно в списке участников проекта 
				Assert.assertEquals(pmp.readUserRole(coordinator.getUsernameLong()), "Руководитель проекта", "Отображаемая в списке роль участника не соответствует заданному");
		//Переходим на страницу активностей и читаем последнюю запись, проверяем, что в ней говорится о добавлении нашего пользователя в проект
				ProjectActivitiesPage pap = pmp.gotoProjectActivities();
				String lastActivity = pap.readLastActivity();
				pap.goToAdminTools();
				Assert.assertTrue(lastActivity.contains(coordinator.getUsernameLong()), "Пользователь не упоминатся в последней активности");
				Assert.assertTrue(lastActivity.contains("Участник"), "В записи о последней активности отсутствуе слово 'Участник'");
	}
	
	/**Test creates new user, assign it as a customer to the project and check "change digest" settings */ 
	@Test
	public void changeDigestTest() {
//Создаем нового пользователя 
		UsersListPage ulp = (new AdminPageBase(driver)).gotoUsers();
		String p = DataProviders.getUniqueString();
		User customer = new User(p, true);
		ulp = ulp.addNewUser(customer, false);
		FILELOG.debug("Created: " + customer.getUsername());
//Переходим в основной проект
		Template SDLC = new Template(
				this.waterfallTemplateName);
		Project project = new Project("DEVPROM.WebTest", "devprom_webtest",SDLC);
		ProjectMembersPage pmp = ((SDLCPojectPageBase) (new PageBase(driver))
				.gotoProject(project)).gotoMembers();
//Добавляем пользователя в проект с ролью "Заказчик"
		pmp = pmp.gotoAddMember().addUserToProject(customer, "Аналитик", 2,	"");
//Выходим из приложения и входим под созданным пользователем
		LoginPage lp = pmp.logOut();
		FavoritesPage favorites = lp.loginAs(customer.getUsername(), customer.getPass());
//Изменяем настройки дайжеста для текущего пользователя
		MySettingsPage msp = ((SDLCPojectPageBase) favorites.gotoProject(project)).gotoMySettingsPage();
		msp = msp.changeNotifications("Дайджест об изменениях в проекте: каждый час");
//Выходим из проекта и заходим под обычым пользователем
		lp = pmp.logOut();
		favorites = lp.loginAs(Configuration.getUsername(), Configuration.getPassword());
		pmp = ((SDLCPojectPageBase) favorites.gotoProject(project)).gotoMembers();
//Проверяем настройки дайждеста для "Заказчика"		
		MemberProfilePage mpp = pmp.gotoMemberProfile(customer.getUsernameLong());
		String digest = mpp.readNotifications();
		mpp.goToAdminTools();
		
		Assert.assertEquals(digest, "Дайджест об изменениях в проекте: каждый час");
	}
}
