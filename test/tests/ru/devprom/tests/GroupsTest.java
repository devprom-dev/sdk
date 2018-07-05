package ru.devprom.tests;

import org.testng.Assert;
import org.testng.annotations.Test;

import ru.devprom.helpers.DataProviders;
import ru.devprom.items.Group;
import ru.devprom.items.User;
import ru.devprom.pages.admin.AdminPageBase;
import ru.devprom.pages.admin.GroupsPage;

public class GroupsTest extends AdminTestBase {
	Group testGroup;
	GroupsPage gp;

	@Test
	public void createGroup() {
		gp = (new AdminPageBase(driver)).gotoGroups();
		testGroup = new Group("TestGroup" + DataProviders.getUniqueString(),
				"Test description");
		gp = gp.addGroup(testGroup);
		FILELOG.debug("One new group has been created: " + testGroup.getName());
		Assert.assertTrue(gp.isGroupExist(testGroup));
	}

	@Test(dependsOnMethods = "createGroup")
	public void editGroup() {
		gp = (new AdminPageBase(driver)).gotoGroups();
		String p = DataProviders.getUniqueString();
		gp = gp.editGroup(testGroup, "NewTestGroup" + p, "New Description");
		FILELOG.debug("Group was edited, the new name is: " + "NewTestGroup"
				+ p);
		testGroup.setName("NewTestGroup" + p);
		testGroup.setDescription("New Description");
		Assert.assertTrue(gp.isGroupExist(testGroup));
	}

	@Test(dependsOnMethods = "editGroup")
	public void addUserToGroup() {
		gp = (new AdminPageBase(driver)).gotoGroups();
		User testUser = new User(username, password, user, "mail", true, true);
		int membersCountBefore = gp.getMembersCount(testGroup);
		gp = gp.addUser(testGroup, testUser);
		FILELOG.debug("Added user " + testUser.getUsername() + " to group: "
				+ testGroup.getName());
		Assert.assertEquals(gp.getMembersCount(testGroup),
				membersCountBefore + 1);
	}

	@Test(dependsOnMethods = "addUserToGroup")
	public void deleteGroup() {
		gp = (new AdminPageBase(driver)).gotoGroups();
		gp.deleteGroup(testGroup);
		Assert.assertTrue(true);
		FILELOG.debug("Group: " + testGroup.getName() + " has been deleted");
	}
}
