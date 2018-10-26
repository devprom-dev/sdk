package ru.devprom.pages.allprojects;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.pages.PageBase;

public class AllProjectsPageBase extends PageBase {

	@FindBy(xpath = "//ul/li[@id='quick-7']/a")
	private WebElement activitiesLink;
	
	@FindBy(xpath = "//ul/li/a[contains(@href,'resourceusage')]")
	private WebElement taskLoadLink;
	
	@FindBy(xpath = "//ul/li/a[contains(@href,'activitiesreport')]")
	private WebElement timeSpentLink;
	
	@FindBy(xpath = "//ul/li/a[@id='menu-group-resources']")
	protected WebElement resourcesMenusGroup;
	
	public AllProjectsPageBase(WebDriver driver) {
		super(driver);
	}

	public AllProjectsTimetableReportPage gotoTimetableReport(){
		resourcesMenusGroup.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(timeSpentLink));
		timeSpentLink.click();
		return new AllProjectsTimetableReportPage(driver);
	}
}
