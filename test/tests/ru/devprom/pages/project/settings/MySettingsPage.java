package ru.devprom.pages.project.settings;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.pages.project.SDLCPojectPageBase;

public class MySettingsPage extends SDLCPojectPageBase
{
	@FindBy(id = "pm_ParticipantSubmitBtn")
	private WebElement submitBtn;

	@FindBy(id = "pm_ParticipantNotificationEmailType")
	private WebElement notificationsEdit;

	public MySettingsPage(WebDriver driver) {
		super(driver);
	}

	public MySettingsPage changeNotifications(String notifications)
	{
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(notificationsEdit));
		(new Select(notificationsEdit)).selectByVisibleText(notifications);
		submitDialog(submitBtn);
		return new MySettingsPage(driver);
	}

	public void saveModuleSettingsForAll(){
		driver.findElement(By.xpath("//div[@id='fieldRowModuleSettings']//a[@action='makedefault']")).click();
	}
	
    public void resetMyModuleSettings(){
		driver.findElement(By.xpath("//div[@id='fieldRowModuleSettings']//a[@action='reset']")).click();
    }
}
