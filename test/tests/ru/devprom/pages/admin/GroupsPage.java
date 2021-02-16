package ru.devprom.pages.admin;

import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;

import ru.devprom.items.Group;
import ru.devprom.items.User;

public class GroupsPage extends AdminPageBase {

	// Page Object fields
	@FindBy(xpath = "//a[contains(.,'Добавить') and contains(@class,'append-btn')]")
	private WebElement addBtn;

	// constructor
	protected GroupsPage(WebDriver driver) {
		super(driver);
		Assert.assertTrue(isElementPresent(By.id("usergrouplist1")));
		FILELOG.debug("Open Groups main page");
		FILELOG.debug("Current URL is: " + driver.getCurrentUrl());
	}

	public GroupsPage addGroup(Group group) {
		clickOnInvisibleElement(addBtn);
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By.id("co_UserGroupCaption")));
		return new GroupAddNewPage(driver).createGroup(group);
	}

	public GroupsPage editGroup(Group group, String newname, String newdescription) {
		selectGroupMenu(group, "Изменить");
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By.id("co_UserGroupCaption")));
		GroupEditPage egp = new GroupEditPage(driver);
		return egp.editGroup(newname, newdescription);
	}

	public GroupsPage deleteGroup(Group group) {
		selectGroupMenu(group, "Изменить");
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By.id("co_UserGroupCaption")));
		GroupEditPage egp = new GroupEditPage(driver);
		egp.deleteGroup();
		(new WebDriverWait(driver, waiting)).until(
				ExpectedConditions.invisibilityOfElementLocated(
						By.xpath("//td[@id='caption' and contains(.,'" + group.getName() + "')]")));
		return new GroupsPage(driver);
	}

	public GroupsPage addUser(Group group, User user) {
		selectGroupMenu(group, "Включить пользователя");
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By.id("co_UserGroupLinkUserGroup")));
		GroupAddUserPage autg = new GroupAddUserPage(driver);
		FILELOG.debug("Opening Add User to group page");
		return autg.addUser(user);
	}

	private void selectGroupMenu(Group group, String item) {
		WebElement gr = driver
				.findElement(By.xpath(".//td[@id='caption'][text()='"
						+ group.getName() + "']"));
		WebElement menu = driver
				.findElement(By.xpath(".//td[@id='caption'][text()='"
						+ group.getName()
						+ "']/following-sibling::td//a[contains(@class,'dropdown-toggle')]"));
		WebElement menuitem = driver
				.findElement(By.xpath(".//td[@id='caption'][text()='"
						+ group.getName()
						+ "']/following-sibling::td//a[text()='" + item + "']"));
		clickOnInvisibleElement(gr);
		clickOnInvisibleElement(menu);
		clickOnInvisibleElement(menuitem);
	}

	public int getGroupsCount() {
		try {
			return driver.findElements(
					By.xpath("//tr[starts-with(@id,'usergrouplist1_row_')]"))
					.size();
		} catch (NoSuchElementException e) {
			return 0;
		}
	}

	public Boolean isGroupExist(Group group) {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By.xpath("//td[@id='caption'][text()='"+ group.getName()+ "']")));
		return true;
	}

	public int getMembersCount(Group group) {
		return Integer.parseInt(driver.findElement(
				By.xpath(".//td[@id='caption'][text()='" + group.getName()
						+ "']/following-sibling::td[@id='custom']")).getText());

	}

}
