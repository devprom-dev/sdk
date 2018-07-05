package ru.devprom.pages.admin;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;

import ru.devprom.items.User;

public class GroupAddUserPage extends AdminPageBase {

	@FindBy(id = "co_UserGroupLinkUserGroup")
	private WebElement groupSelect;

	@FindBy(id = "SystemUserText")
	private WebElement userSelect;

	@FindBy(id = "co_UserGroupLinkSubmitBtn")
	private WebElement submitBtn;

	@FindBy(xpath = ".//input[@value='Отменить']")
	private WebElement cancelBtn;

	protected GroupAddUserPage(WebDriver driver) {
		super(driver);
		FILELOG.debug("Open Add User to Group page");
		FILELOG.debug("Current URL is: " + driver.getCurrentUrl());
	}

	public GroupsPage addUser(User user) {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(userSelect));
		try {
			Thread.sleep(1000);
		} catch (InterruptedException e) {
		}
		userSelect.sendKeys(user.getUsernameLong());
		autocompleteSelect(user.getUsernameLong());
		FILELOG.debug("Adding user to a group. Username in field is" + userSelect.getText());
		submitBtn.click();
		return new GroupsPage(driver);
	}

}
