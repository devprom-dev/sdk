package ru.devprom.pages.admin;

import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.NoSuchElementException;
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

public class UserEditPage extends AdminPageBase {

	@FindBy(id = "cms_UserSubmitBtn")
	private WebElement createBtn;

	@FindBy(id = "cms_UserCancelBtn")
	private WebElement cancelBtn;

	@FindBy(id = "cms_UserDeleteBtn")
	private WebElement deleteBtn;

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

	@FindBy(xpath = "//select[@id='cms_UserLanguage']")
	private Select languageLabel;
	
	protected UserEditPage(WebDriver driver) {
		super(driver);
		Assert.assertTrue(isElementPresent(By.id("cms_Useraction")));
		FILELOG.debug("Open Edit User page");
		FILELOG.debug("Current URL is: " + driver.getCurrentUrl());
	}

	public UsersListPage deleteUser() {
		deleteBtn.click();
		safeAlertAccept();
		(new WebDriverWait(driver, waiting)).until(elementDissapeared(By
				.xpath("//div[@id='modal-form']")));
		try {
			Thread.sleep(3000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		return new UsersListPage(driver);
	}

	public void editUser(User user) {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(usernameLongEdit));
		if (!usernameLongEdit.getText().equals(user.getUsernameLong())) {
			usernameLongEdit.clear();
			usernameLongEdit.sendKeys(user.getUsernameLong());
		}
		if (!emailEdit.getText().equals(user.getEmail())) {
			emailEdit.clear();
			emailEdit.sendKeys(user.getEmail());
		}
		if (!usernameEdit.getText().equals(user.getUsername())) {
			usernameEdit.clear();
			usernameEdit.sendKeys(user.getUsername());
		}
		if (!passEdit.getText().equals(user.getPass())) {
			passEdit.clear();
			passEdit.sendKeys(user.getPass());
			passrepeatEdit.clear();
			passrepeatEdit.sendKeys(user.getPass());
		}

		if (user.isAdmin) {
			if (!isAdminCheckBox.isSelected())
				isAdminCheckBox.click();
		} else {
			if (isAdminCheckBox.isSelected())
				isAdminCheckBox.click();
		}

		// Edit groups list - thats is possible to make it easier (deleting all
		// and then adding what we need)
		// but in this version we delete only what we really need to

		// Finding groups the user included in
		List<WebElement> groupsL = null;
		String[] inGroups;
		Boolean toDelete;
		try {
			groupsL = driver
					.findElements(By
							.xpath(".//span[@name='cms_UserGroupId']//div[contains(@id,'embeddedList')]//*[contains(@class,'title')]"));
		} catch (NoSuchElementException e) {
			// The user is not included in groups
		}
		// if the user included in some groups put their names into array
		if (!groupsL.equals(null)) {
			inGroups = new String[groupsL.size()];
			for (int i = 0; i < inGroups.length; i++) {
				inGroups[i] = groupsL.get(i).getText().trim();
				toDelete = true;
				for (String group : user.getGroups()) {
					if (group.equals(inGroups[i])) {
						toDelete = false;
					}
				}
				// if the current group is not in our new list - delete it
				if (toDelete) {
					int deleteNumber = 100 + i;
					clickOnInvisibleElement(driver.findElement(By.xpath(".//span[@name='cms_UserGroupId']//div[contains(@id,'Caption" + deleteNumber+ "')]//li[@uid='delete']/a")));
				}
			}
		}
		languageSelect.click();
		if (user.getLanguage() == Lang.english)
			englishSelect.click();
		else
			russianSelect.click();
		for (String group : user.getGroups()) {
			clickOnInvisibleElement(addGroupLink);
			(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(groupsList));
			groupsList.clear();
			groupsList.sendKeys(group);
			autocompleteSelect(group);
			addGroupBtn.click();
		}
		submitDialog(createBtn);
	}

	
	public UserViewPage makeAdminAndSave() {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(createBtn));
		if (!isAdminCheckBox.isSelected())
			isAdminCheckBox.click();
		submitDialog(createBtn);
		return new UserViewPage(driver);
	}
	
	public User readUser() {
		String username = "";
		String usernameLong = "";
		String email = "";
		Boolean isAdmin = false;
		Boolean isRussian = true;
		String contacts = "";
		String description = "";
		String[] groups = null;
		List<WebElement> groupsL = null;

		username = usernameEdit.getAttribute("value");
		usernameLong = usernameLongEdit.getAttribute("value");
		email = emailEdit.getAttribute("value");
		isAdmin = isAdminCheckBox.isSelected();
		//if (languageLabel.getFirstSelectedOption().getText() == "Русский") isRussian = true;

		try {
			groupsL = driver
					.findElements(By
							.xpath(".//span[@name='cms_UserGroupId']//div[contains(@id,'embeddedList')]//*[contains(@class,'title')]"));
		} catch (NoSuchElementException e) {
			// The user is not included in groups
		}
		// if the user included in some groups put their names into array
		if (!groupsL.equals(null)) {
			groups = new String[groupsL.size()];
			for (int i = 0; i < groupsL.size(); i++) {
				groups[i] = (groupsL.get(i).getText().trim());
			}
		}
		return new User(username, username, usernameLong, email, isAdmin,
				isRussian, contacts, description, groups, true);

	}

	public WebElement getForm() {
		return driver.findElement(By.id("modal-form"));
	}
	
	public void close() {
		cancelDialog(cancelBtn);
	}
}
