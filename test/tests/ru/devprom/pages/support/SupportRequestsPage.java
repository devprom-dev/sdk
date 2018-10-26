package ru.devprom.pages.support;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.items.Request;
import ru.devprom.pages.project.requests.RequestNewPage;

public class SupportRequestsPage extends SupportPageBase {

	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(.,'Действия')]")
	protected WebElement actionsBtn;
	
	@FindBy(xpath = "//a[@id='new-issue']")
	protected WebElement newRequestBtn;
	
	@FindBy(id = "pm_ChangeRequestCaption")
	protected WebElement captionEdit;

	@FindBy(id = "pm_ChangeRequestPriority")
	protected WebElement priorityList;
	
	@FindBy(id = "pm_ChangeRequestSubmitBtn")
	protected WebElement submitBtn;

	@FindBy(xpath = "//input[@type='button' and @value='Отменить']")
	protected WebElement cancelBtn;
	
	public SupportRequestsPage(WebDriver driver) {
		super(driver);
		// TODO Auto-generated constructor stub
	}

	public SupportRequestsPage(WebDriver driver, Project project) {
		super(driver, project);
		// TODO Auto-generated constructor stub
	}
	
	
	public RequestNewPage clickNewRequest() {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(newRequestBtn));
		newRequestBtn.click();
		waitForDialog();
		return new RequestNewPage(driver);
	}

	public SupportRequestsPage addNewRequestShort(Request request){
		newRequestBtn.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(captionEdit));
		captionEdit.clear();
		captionEdit.sendKeys(request.getName());
		submitDialog(submitBtn);
		//read ID
		driver.navigate().to(driver.getCurrentUrl()+"&state=all");	
    	String uid =driver.findElement(By.xpath("//td[@id='caption' and contains(.,'"+request.getName()+"')]/preceding-sibling::td[@id='uid']")).getText();
    	request.setId(uid.substring(1, uid.length()-1));
    	FILELOG.debug("Created Request: " + request.getId());
    	
    	return new SupportRequestsPage(driver);
	}
	
	public SupportRequestsPage duplicateInProject(Request request, String projectName){
		WebElement element = driver.findElement(By.xpath("//table[contains(@id,'requestlist')]//td[@id='uid']/a[text()='["
				+ request.getId() + "]']/../following-sibling::td[@id='operations']//a[contains(.,'Реализовать')]"));
		clickOnInvisibleElement(element);
		
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOfElementLocated((By.id("ProjectText"))));
		driver.findElement(By.id("ProjectText")). clear();
		driver.findElement(By.id("ProjectText")).sendKeys(projectName);
		autocompleteSelect(projectName);
		submitDialog(driver.findElement(By.id("pm_ChangeRequestSubmitBtn")));
		return new SupportRequestsPage(driver);
	}

	public String readIDByName(String name) {
		WebElement row = driver
				.findElement(By
						.xpath("//tr[contains(@id,'requestlist1_row_')]/td[@id='caption' and contains(.,'"
								+ name + "')]/.."));
		String id = row.findElement(By.id("uid")).getText();
		return id.substring(1, id.length() - 1);
	}
	
}
