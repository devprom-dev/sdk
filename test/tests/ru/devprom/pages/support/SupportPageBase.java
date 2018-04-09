package ru.devprom.pages.support;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.project.ProjectPageBase;
import ru.devprom.helpers.Configuration;

public class SupportPageBase extends ProjectPageBase {

 	@FindBy(xpath = ".//ul[@id='menu_favs']//a[@module='issues-backlog']")
	protected WebElement requestsItem;
 	
 	@FindBy(xpath = ".//ul[@id='menu_favs']//a[@uid='project-log']")
	protected WebElement activitiesItem;
	
	public SupportPageBase(WebDriver driver) {
		super(driver);
	}

	public SupportPageBase(WebDriver driver, Project project) {
		super(driver, project);
	}

	
	public SupportRequestsPage gotoRequests() {
		clickOnFavoriteLink();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(requestsItem));
		requestsItem.click();
		return new SupportRequestsPage(driver);
	}
	
	public SupportActivitiesPage gotoActivities(){
		driver.navigate().to(Configuration.getBaseUrl() + "/pm/" + getProject().getCodeName() + "/project/log?report=project-log" );
		return new SupportActivitiesPage(driver);
	}
}
