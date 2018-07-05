package ru.devprom.pages.admin;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;

public class GroupEditPage extends AdminPageBase {

	@FindBy(id = "co_UserGroupSubmitBtn")
	private WebElement createBtn;

	@FindBy(xpath = ".//input[@value='Отменить']")
	private WebElement cancelBtn;

	@FindBy(id = "co_UserGroupDeleteBtn")
	private WebElement deleteBtn;

	@FindBy(id = "co_UserGroupCaption")
	private WebElement groupnameEdit;

	@FindBy(id = "co_UserGroupDescription")
	private WebElement groupdescriptionEdit;

	protected GroupEditPage(WebDriver driver) {
		super(driver);
		Assert.assertTrue(isElementPresent(By.id("co_UserGroupaction")));
		FILELOG.debug("Open Edit Group page");
		FILELOG.debug("Current URL is: " + driver.getCurrentUrl());
	}

	public GroupsPage editGroup(String newname, String newdescription) {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(groupnameEdit));
		if (!groupnameEdit.getText().equals(newname)) {
			groupnameEdit.clear();
			groupnameEdit.sendKeys(newname);
		}

		if (!groupdescriptionEdit.getText().equals(newdescription)) {
			groupdescriptionEdit.clear();
			groupdescriptionEdit.sendKeys(newdescription);
		}
		submitDialog(createBtn);
		if (!isElementPresent(By.id("usergrouplist1")))
			gotoGroups();
		return new GroupsPage(driver);
	}

	public GroupsPage deleteGroup() {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(deleteBtn));
		submitDelete(deleteBtn);
		driver.navigate().refresh();
		return new GroupsPage(driver);
	}
}
