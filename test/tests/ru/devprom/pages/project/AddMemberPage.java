package ru.devprom.pages.project;

import java.util.Random;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;

import ru.devprom.helpers.ScreenshotsHelper;
import ru.devprom.items.User;
import ru.devprom.pages.project.settings.ProjectMembersPage;

public class AddMemberPage extends SDLCPojectPageBase {

	@FindBy(id = "SystemUserText")
	private WebElement usernameLongEdit;

	@FindBy(id = "pm_ParticipantProjectRole")
	private WebElement roleEdit;

	@FindBy(id = "pm_ParticipantCapacity")
	private WebElement capacityEdit;

	@FindBy(id = "pm_ParticipantNotificationEmailType")
	private WebElement notificationsEdit;

	@FindBy(id = "pm_ParticipantSubmitBtn")
	private WebElement createBtn;

	@FindBy(xpath = ".//input[@value='Отменить']")
	private WebElement cancelBtn;

	public AddMemberPage(WebDriver driver) {
		super(driver);
	}

	public ProjectMembersPage addUserToProject(User user, String role,
			int capacity, String notifications) {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOfElementLocated(By.id("SystemUserText")));
		(new Select(roleEdit)).selectByVisibleText(role);
		capacityEdit.sendKeys(String.valueOf(capacity));
		if (notifications.equals("")) {
			String[] not = new String[] { 
					"Отправлять уведомления сразу по электронной почте", 
					"Дайджест об изменениях в проекте: каждые 10 минут", 
					"Дайджест об изменениях в проекте: каждый час"
					};
			notifications = not[new Random(System.currentTimeMillis()).nextInt(not.length - 1)];
		}
	    (new Select(notificationsEdit)).selectByVisibleText(notifications);
		usernameLongEdit.sendKeys(user.getUsernameLong());
		autocompleteSelect(user.getUsernameLong());
		submitDialog(createBtn);
		driver.navigate().refresh();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//td[@id='caption' and contains(.,'"+user.getUsernameLong()+"')]")));
		return new ProjectMembersPage(driver);
	}
}
