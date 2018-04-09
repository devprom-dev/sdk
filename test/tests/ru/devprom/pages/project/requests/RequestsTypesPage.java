package ru.devprom.pages.project.requests;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class RequestsTypesPage extends SDLCPojectPageBase {

	@FindBy(xpath = "//a[@id='new-requesttype']")
	protected WebElement addBtn;
	
	public RequestsTypesPage(WebDriver driver) {
		super(driver);
		// TODO Auto-generated constructor stub
	}

	public RequestsTypesPage(WebDriver driver, Project project) {
		super(driver, project);
		// TODO Auto-generated constructor stub
	}
	
	public RequestsTypesPage addNewType(String requestName){
		addBtn.click();
		
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.presenceOfElementLocated(By.id("pm_IssueTypeCaption")));
		driver.findElement(By.id("pm_IssueTypeCaption")).sendKeys(requestName);
		submitDialog(driver.findElement(By.id("pm_IssueTypeSubmitBtn")));
		
		return new RequestsTypesPage(driver);
	}

}
