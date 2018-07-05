package ru.devprom.pages.admin;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;

import ru.devprom.items.Group;

public class GroupAddNewPage extends AdminPageBase {

	@FindBy(id = "co_UserGroupSubmitBtn")
	private WebElement createBtn;

	@FindBy(xpath = ".//input[@value='Отменить']")
	private WebElement cancelBtn;

	@FindBy(id = "co_UserGroupCaption")
	private WebElement groupnameEdit;

	@FindBy(id = "co_UserGroupDescription")
	private WebElement groupdescriptionEdit;

	protected GroupAddNewPage(WebDriver driver) {
		super(driver);
		Assert.assertTrue(isElementPresent(By.id("co_UserGroupCaption")));
		FILELOG.debug("Open Add Group page");
		FILELOG.debug("Current URL is: " + driver.getCurrentUrl());
	}

	public GroupsPage createGroup(Group group) {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(groupnameEdit));
		groupnameEdit.sendKeys(group.getName());
		groupdescriptionEdit.sendKeys(group.getDescription());
		submitDialog(createBtn);
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath(".//td[@id='caption' and text()='"
						+ group.getName() + "']")));
		return new GroupsPage(driver);
	}

}
