package ru.devprom.pages.project.settings;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class MemberProfilePage extends SDLCPojectPageBase {

	@FindBy(id = "pm_ParticipantSubmitBtn")
	private WebElement submitBtn;
	
	@FindBy(id = "pm_ParticipantCancelBtn")
	private WebElement cancelBtn;

	@FindBy(id = "pm_ParticipantNotificationEmailType")
	private WebElement notificationsEdit;

	public MemberProfilePage(WebDriver driver) {
		super(driver);
	}

	public MemberProfilePage(WebDriver driver, Project project) {
		super(driver, project);
	}
	
	public ProjectMembersPage changeNotifications(String notifications){
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(notificationsEdit));
		(new Select(notificationsEdit)).selectByVisibleText(notifications);
		submitDialog(submitBtn);
		return new ProjectMembersPage(driver);
	}
	
	public String readNotifications(){
		String value = (new Select(notificationsEdit)).getFirstSelectedOption().getText();
		cancelDialog(cancelBtn);
		return value;
	}
	
	public void excludeFromProject(){
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOfAllElementsLocatedBy(By.id("pm_ParticipantIsActive")));
		driver.findElement(By.id("pm_ParticipantIsActive")).click();
	}

	public ProjectMembersPage saveChanges(){
		submitDialog(submitBtn);
		return new ProjectMembersPage(driver);
	}
	
}
