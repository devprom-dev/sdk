package ru.devprom.pages.admin;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;

import ru.devprom.helpers.Configuration;
import ru.devprom.items.User;
import ru.devprom.items.User.Lang;

public class UserAddNewPage extends AdminPageBase {

	@FindBy(id = "cms_UserSubmitBtn")
	private WebElement createBtn;

	@FindBy(xpath = "//input[@value='Отменить']")
	private WebElement cancelBtn;

	@FindBy(id = "cms_UserCaption")
	private WebElement usernameLongEdit;

	@FindBy(id = "cms_UserEmail")
	private WebElement emailEdit;

	@FindBy(id = "cms_UserLogin")
	private WebElement usernameEdit;

	@FindBy(id = "cms_UserPassword")
	private WebElement passEdit;

	@FindBy(id = "cms_UserRepeatPassword")
	private WebElement passrepeatEdit;

	@FindBy(id = "cms_UserIsAdmin")
	private WebElement isAdminCheckBox;

	@FindBy(name = "LicensePermissionreqs")
	private WebElement licenseReqsCheckbox;

	@FindBy(name = "LicensePermissiondev")
	private WebElement licenseDevCheckbox;

	@FindBy(name = "LicensePermissionqa")
	private WebElement licenseQACheckbox;

	@FindBy(name = "LicensePermissiondocs")
	private WebElement licenseDocsCheckbox;

	@FindBy(id = "cms_UserLanguage")
	private WebElement languageSelect;

	@FindBy(xpath = "//select[@id='cms_UserLanguage']/option[@value='1']")
	private WebElement russianSelect;

	@FindBy(xpath = "//select[@id='cms_UserLanguage']/option[@value='2']")
	private WebElement englishSelect;

	@FindBy(xpath = "//span[@name='cms_UserGroupId']//a[contains(@class,'embedded-add-button')]")
	private WebElement addGroupLink;

	@FindBy(xpath = "//span[@name='cms_UserGroupId']//input[contains(@id,'UserGroupText')]")
	private WebElement groupsList;

	@FindBy(xpath = "//span[@name='cms_UserGroupId']//input[@action='save']")
	private WebElement addGroupBtn;

	@FindBy(xpath = "//span[@name='cms_UserGroupId']//input[@action='cancel']")
	private WebElement cancelGroupBtn;

	public UserAddNewPage(WebDriver driver) {
		super(driver);
		Assert.assertTrue(isElementPresent(By.id("cms_UserPassword")));
		FILELOG.debug("Open Add Group page");
		FILELOG.debug("Current URL is: " + driver.getCurrentUrl());
	}

	public UsersListPage createUser(User user) {
    	(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(usernameLongEdit));
		usernameLongEdit.sendKeys(user.getUsernameLong());
		emailEdit.sendKeys(user.getEmail());
		usernameEdit.sendKeys(user.getUsername());
		passEdit.sendKeys(user.getPass());
		passrepeatEdit.sendKeys(user.getPass());
		if (user.isAdmin)
			isAdminCheckBox.click();
		licenseReqsCheckbox.click();
		licenseDevCheckbox.click();
		licenseDocsCheckbox.click();
		licenseQACheckbox.click();
		submitDialog(createBtn);
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//td[@id='email' and contains(.,'" + user.getEmail() + "')]")));
		user.id = Integer.parseInt(
				driver.findElement(
						By.xpath("//td[@id='email' and contains(.,'" + user.getEmail() + "')]/parent::tr"))
							.getAttribute("object-id"));
		return new UsersListPage(driver);
	}

	public UsersListPage createUserFull(User user) {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(usernameLongEdit));
		usernameLongEdit.sendKeys(user.getUsernameLong());
		emailEdit.sendKeys(user.getEmail());
		usernameEdit.sendKeys(user.getUsername());
		passEdit.sendKeys(user.getPass());
		passrepeatEdit.sendKeys(user.getPass());
		if (user.isAdmin)
			isAdminCheckBox.click();
		licenseReqsCheckbox.click();
		licenseDevCheckbox.click();
		licenseDocsCheckbox.click();
		licenseQACheckbox.click();
		for (String group : user.getGroups()) {
			(new WebDriverWait(driver, waiting)).until(ExpectedConditions
					.visibilityOf(addGroupLink));
			addGroupLink.click();
			groupsList.clear();
			groupsList.sendKeys(group);
			autocompleteSelect(group);
			addGroupBtn.click();
		}
		if (user.getLanguage() == Lang.english) {
			languageSelect.click();
			englishSelect.click();
		}
		submitDialog(driver.findElement(By.id("cms_UserSubmitBtn")));
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//td[@id='email' and contains(.,'" + user.getEmail() + "')]")));
		user.id = Integer.parseInt(
				driver.findElement(
						By.xpath("//td[@id='email' and contains(.,'" + user.getEmail() + "')]/parent::tr"))
						.getAttribute("object-id"));
		return new UsersListPage(driver);
	}

	public void createFirstUser(User user) {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(usernameLongEdit));
		usernameLongEdit.sendKeys(user.getUsernameLong());
		emailEdit.sendKeys(user.getEmail());
		usernameEdit.sendKeys(user.getUsername());
		passEdit.sendKeys(user.getPass());
		passrepeatEdit.sendKeys(user.getPass());
		if (user.isAdmin)
			isAdminCheckBox.click();
		licenseReqsCheckbox.click();
		licenseDevCheckbox.click();
		licenseDocsCheckbox.click();
		licenseQACheckbox.click();
		submitDialog(createBtn);
	}

}
