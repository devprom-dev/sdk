package ru.devprom.pages.admin;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.testng.Assert;

public class UserViewPage extends AdminPageBase {

	@FindBy(xpath = "//a[contains(text(),'Действия')]")
	private WebElement actionsBtn;

	@FindBy(xpath = "//a[@id='modify']")
	private WebElement changeBtn;

	@FindBy(xpath = "//a[text()='Включить в проект']")
	private WebElement includeToProjectBtn;

	@FindBy(xpath = "//a[text()='Заблокировать']")
	private WebElement disableBtn;

	@FindBy(xpath = "//a[text()='Добавить в группу']")
	private WebElement addToGroupBtn;

	@FindBy(xpath = "//a[text()='Права доступа']")
	private WebElement permissionsBtn;

	@FindBy(xpath = "//a[@id='bulk-delete']")
	private WebElement removeBtn;

	@FindBy(id = "cms_UserCaption")
	private WebElement usernameLongEdit;

	@FindBy(id = "cms_UserEmail")
	private WebElement emailEdit;

	@FindBy(id = "cms_UserLogin")
	private WebElement usernameEdit;

	@FindBy(id = "cms_UserIsAdmin")
	private WebElement isAdminCheckBox;

	public UserViewPage(WebDriver driver) {
		super(driver);
		Assert.assertTrue(isElementPresent(By.id("cms_UserId")));
		FILELOG.debug("Open View User page");
		FILELOG.debug("Current URL is: " + driver.getCurrentUrl());
	}

	public UserEditPage clickEditUser() {
		actionsBtn.click();
		changeBtn.click();
		return new UserEditPage(driver);
	}

}
