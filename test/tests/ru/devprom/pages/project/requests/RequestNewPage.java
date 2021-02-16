package ru.devprom.pages.project.requests;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Request;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class RequestNewPage extends SDLCPojectPageBase {

	@FindBy(xpath = "//div[@id='modal-form']//*[@id='pm_ChangeRequestCaption']")
	protected WebElement captionEdit;

	@FindBy(xpath = "//div[contains(@id,'pm_ChangeRequestDescription')]")
	protected WebElement descriptionEdit;

	@FindBy(xpath = "//div[@id='modal-form']//*[@id='pm_ChangeRequestType']")
	protected WebElement typesList;

	@FindBy(xpath = "//div[@id='modal-form']//*[@id='pm_ChangeRequestPriority']")
	protected WebElement priorityList;

	@FindBy(xpath = "//div[@id='modal-form']//*[@id='pm_ChangeRequestEstimation']")
	protected WebElement estimationEdit;

	@FindBy(id = "pm_ChangeRequestSubmitBtn")
	protected WebElement submitBtn;

	@FindBy(id = "pm_ChangeRequestCancelBtn")
	protected WebElement cancelBtn;

	public RequestNewPage(WebDriver driver) {
		super(driver);
	}

	public RequestsPage createCRShort(Request request){
		clickMainTab();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(captionEdit));
		captionEdit.clear();
		captionEdit.sendKeys(request.getName());
		if ( !request.getPriority().equals("") ) {
			(new Select(priorityList)).selectByVisibleText(request.getPriority());
		}
		submitDialog(submitBtn);
		//read ID
		driver.navigate().to(driver.getCurrentUrl()+"&state=all");	
    	String uid =driver.findElement(By.xpath("//td[@id='caption' and contains(.,'"+request.getName()+"')]/preceding-sibling::td[@id='uid']")).getText();
    	request.setId(uid.substring(1, uid.length()-1));
    	FILELOG.debug("Created Request: " + request.getId());
		return new RequestsPage(driver);
	}
	
	public RequestsPage completeNewCR(Request request) {
		clickMainTab();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(captionEdit));
		captionEdit.clear();
		captionEdit.sendKeys(request.getName());
		(new CKEditor(driver)).changeText(request.getDescription());
		if ( !request.getPriority().equals("") ) {
			(new Select(priorityList)).selectByVisibleText(request.getPriority());
		}
		submitDialog(submitBtn);
		//read ID
		driver.navigate().to(driver.getCurrentUrl()+"&state=all");
		String xpath = "//td[@id='caption' and contains(.,'"+request.getName()+"')]/preceding-sibling::td[@id='uid']"; 
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath(xpath)));
    	String uid = driver.findElement(By.xpath(xpath)).getText();
    	request.setId(uid.substring(1, uid.length()-1));
    	FILELOG.debug("Created Request: " + request.getId());
		return new RequestsPage(driver);
	}

	public RequestsPage createNewCR(Request request) {
		clickMainTab();
			(new Select(priorityList)).selectByVisibleText(request
					.getPriority());
		return completeNewCR(request);
	}

	public void addWIKIDescription(String text){
		driver.findElement(By.xpath("//div[@id='modal-form']//textarea[contains(@id,'pm_ChangeRequestDescription')]")).sendKeys(text);
	}
	
	public void close() {
		cancelDialog(cancelBtn);
	}

	public String createWithError(Request request) {
		clickMainTab();
		if ( request.getType()!=null) { (new Select(typesList)).selectByVisibleText(request.getType()); }
		(new Select(priorityList)).selectByVisibleText(request.getPriority());
		if ( request.getEstimation() > 0 ) { estimationEdit.sendKeys(String.valueOf(request.getEstimation())); }
		captionEdit.sendKeys(request.getName());
		makeElementTypeHiddenVisibleByJavascript(descriptionEdit);
		(new CKEditor(driver)).changeText(request.getDescription());
		submitBtn.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOfElementLocated(By.className("alert-error")));
		return driver.findElement(By.className("alert-error")).getText();
	}


	public boolean isAttributePresent(String attrRefName){
		clickMoreTab();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOfElementLocated(By.xpath("//div[@id='modal-form']//*[@name='"+attrRefName+"']")));
		return true;
	}
	
	public String checkSelectAttributeDefaultValue(String attrRefName){
		clickMoreTab();
		return driver.findElement(By.xpath("//div[@id='modal-form']//select[@name='"+attrRefName+"']/option[@selected]")).getText().trim();
	}
	
	public void setUserStringAttribute(String attrRefName, String value) {
		clickMoreTab();
		By loc = By.xpath("//div[@id='modal-form']//input[@name='"+attrRefName+"']");
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOfElementLocated(loc));
		WebElement el = driver.findElement(loc);
		el.clear();
		el.sendKeys(value);
	}
	
	
	public void setUserOptionAttribute(String attrRefName, String value) {
		clickMoreTab();
		By loc = By.xpath("//div[@id='modal-form']//select[@name='"+attrRefName+"']");
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOfElementLocated(loc));
		new Select(driver.findElement(loc)).selectByValue(value);
	}
	
	public void setUserOptionAttributeByVisibleText(String attrRefName, String visibleText) {
		clickMoreTab();
		By loc = By.xpath("//div[@id='modal-form']//select[@name='"+attrRefName+"']");
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOfElementLocated(loc));
		new Select(driver.findElement(loc)).selectByVisibleText(visibleText);
	}


	public void setUserAutocompleteAttribute(String attrRefName, String value) {
		clickMoreTab();
		WebElement element = driver.findElement(By.xpath("//div[@id='modal-form']//input[@name='"+attrRefName+"']/preceding-sibling::input"));
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(element));
        element.sendKeys(value);
        autocompleteSelect(value);
	}
	
	public void clickMoreTab()
	{
		clickTab("additional");
	}
	
	public void clickDeadlinesTab()
	{
		clickTab("deadlines");
	}

	public void clickMainTab()
	{
		clickTab("main");
	}
	
	public void clickTraceTab()
	{
		clickTab("trace");
	}
}
