package ru.devprom.pages.admin;

import java.util.ArrayList;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;

import ru.devprom.items.User;
import ru.devprom.pages.project.BlocksPage;

public class UsersListPage extends AdminPageBase {

	// Page Object fields

	// @FindBy
	// (xpath="//a[@href='/admin/users.php?class=metaobject&entity=cms_User&cms_UserId=&area=']")
	@FindBy(xpath = "//a[contains(.,'Добавить') and contains(@class,'append-btn')]")
	private WebElement addBtn;

	// constructor
	public UsersListPage(WebDriver driver) {
		super(driver);
		Assert.assertTrue(isElementPresent(By.id("userlist1")));
		FILELOG.debug("Open Users list main page");
		FILELOG.debug("Current URL is: " + driver.getCurrentUrl());
	}

	public UsersListPage addNewUser(User user, Boolean isFull) {
		clickOnInvisibleElement(addBtn);
		waitForDialog();
		UserAddNewPage aup = new UserAddNewPage(driver);
		if (isFull)
			return aup.createUserFull(user);
		else
			return aup.createUser(user);
	}

	public int getUsersCount() {
		try {
			return driver.findElements(
					By.xpath("//tr[contains(@id, 'userlist1_row_')]")).size();
		} catch (NoSuchElementException e) {
			return 0;
		}
	}

	public List<User> getAllUsers() {
		List<WebElement> allUsers = driver.findElements(By
				.xpath("//tr[contains(@id, 'userlist1_row_')]"));
		List<User> users = new ArrayList<User>();
		for (WebElement userRow : allUsers) {
			users.add(new User("User" + allUsers.indexOf(userRow), "pass",
					userRow.findElement(By.id("caption")).getText(), userRow
							.findElement(By.id("email")).getText(), false,
					false));
		}
		return users;
	}

	public UsersListPage deleteAllButThis(String[] usernamesLong) {
		WebElement userLink;
		Boolean isDelete;
		int allUsersCount = getUsersCount();
		for (int i = 1; i <= allUsersCount; i++) {
			isDelete = true;
			userLink = driver.findElement(By.id("userlist1_row_" + i));
			for (int k = 0; k < usernamesLong.length; k++) {
				if (usernamesLong[k].equals(userLink.findElement(
						By.xpath("./td[@id='caption']/a")).getText())) {
					isDelete = false;
					break;
				}
			}
			if (isDelete) {
				editUser(userLink.findElement(By.xpath("./td[@id='caption']/a")).getText()).deleteUser();
				allUsersCount--;
				i--;
			}
		}

		return new UsersListPage(driver);
	}

	public Boolean isUserExist(String usernameLong) {
		try {
			driver.findElement(By.xpath(".//a[contains(.,'" + usernameLong
					+ "')]"));
		} catch (NoSuchElementException e) {
			return false;
		}
		return true;
	}

	public void addFirstUser(User user) {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOf(addBtn));
		addBtn.click();
		UserAddNewPage aup = new UserAddNewPage(driver);
		aup.createFirstUser(user);
	}
	
	public BlocksPage blockUser(String userName)
	{
		WebElement blockBtn = driver.findElement(
				By.xpath("//td[@id='caption']/a[text()='"+userName+"']/../following-sibling::td[@id='operations']//a[text()='Заблокировать']"));
        clickOnInvisibleElement(blockBtn);
		(new WebDriverWait(driver,waiting)).until(
				ExpectedConditions.presenceOfElementLocated(By.xpath("//table[contains(@id,'blacklist')]")));
		(new WebDriverWait(driver,waiting)).until(
				ExpectedConditions.presenceOfElementLocated(By.xpath("//td[@id='systemuser' and contains(.,'"+userName+"')]")));
        return new BlocksPage(driver);
	}
	
	public UserEditPage editUser(String userName) {
		WebElement editBtn = driver.findElement(By.xpath("//td[@id='caption']/a[text()='"+userName+"']/../following-sibling::td[@id='operations']//a[text()='Изменить']"));
        clickOnInvisibleElement(editBtn);
        waitForDialog();
        return new UserEditPage(driver);
	}
	
}
