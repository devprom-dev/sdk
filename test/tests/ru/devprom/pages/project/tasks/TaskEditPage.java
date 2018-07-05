package ru.devprom.pages.project.tasks;

import java.io.File;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;

public class TaskEditPage extends TaskNewPage {
	
	@FindBy(id = "pm_TaskSubmitBtn")
	protected WebElement saveBtn;

	@FindBy(xpath = "//div[@id='modal-form']//a[contains(@class,'embedded-add-button') and preceding-sibling::input[@value='attachment']]")
	protected WebElement addAttachmentBtn;
	
	@FindBy(xpath = "//div[@id='modal-form']//span[@id='pm_TaskTraceTask']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addPreviousTaskBtn;
	
	@FindBy(xpath = "//div[@id='modal-form']//span[@id='pm_TaskRequirement']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addRequirementBtn;
	 
	@FindBy(xpath = "//div[@id='modal-form']//span[@id='pm_TaskTestScenario']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addTestDocBtn;
	
	@FindBy(xpath = "//div[@id='modal-form']//span[@id='pm_TaskTestExecution']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addTestResultsBtn;
	
	@FindBy(xpath = "//div[@id='modal-form']//span[@id='pm_TaskHelpPage']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addDocsBtn;
	
	@FindBy(xpath = "//div[@id='modal-form']//span[@id='pm_TaskWatchers']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addWarchersBtn;
	
	@FindBy(xpath = "//div[@id='modal-form']//span[@id='pm_TaskAttachments']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addAttachmentsBtn;
	public TaskEditPage(WebDriver driver) {
		super(driver);
	}

	public TaskEditPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	
	public TaskViewPage saveChanges()
	{
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(saveBtn));
		submitDialog(saveBtn);
		return new TaskViewPage(driver);
	}
	
	
	 
    public void addRequest(String requestname)
    {
    	clickMainTab();
    	requestsList.clear();
    	requestsList.sendKeys(requestname);
    	autocompleteSelect(requestname);
    }
 
    
    public void addPreviousTask(String taskname)
    {
    	clickAdditionalTab();
    	addPreviousTaskBtn.click();
    	driver.findElement(
				By.xpath("//div[@id='modal-form']//input[@value='tasktracetask']/following-sibling::div[contains(@id,'fieldRowObjectId')]//input[contains(@id,'ObjectIdText')]"))
				.sendKeys(taskname);
		autocompleteSelect(taskname);
		driver.findElement(
				By.xpath("//div[@id='modal-form']//input[@value='tasktracetask']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
				.click();
    }
    
	public void addRequirements(String requirements)
	{
		clickTraceTab();
		addRequirementBtn.click();
		driver.findElement(
				By.xpath("//div[@id='modal-form']//input[@value='tasktracerequirement']/following-sibling::div[contains(@id,'fieldRowObjectId')]//input[contains(@id,'ObjectIdText')]"))
				.sendKeys(requirements);
		autocompleteSelect(requirements);
		driver.findElement(
				By.xpath("//div[@id='modal-form']//input[@value='tasktracerequirement']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
				.click();

	}
	
	public void addTestDoc(String testdoc)
	{
		clickTraceTab();
		addTestDocBtn.click();
		driver.findElement(
				By.xpath("//div[@id='modal-form']//input[@value='tasktracetestscenario']/following-sibling::div[contains(@id,'fieldRowObjectId')]//input[contains(@id,'ObjectIdText')]"))
				.sendKeys(testdoc);
		autocompleteSelect(testdoc);
		driver.findElement(
				By.xpath("//div[@id='modal-form']//input[@value='tasktracetestscenario']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
				.click();

	}
	
	public void addTestResults(String testresults)
	{
		clickTraceTab();
		addTestResultsBtn.click();
		driver.findElement(
				By.xpath("//div[@id='modal-form']//input[@value='tasktracetestexecution']/following-sibling::div[contains(@id,'fieldRowObjectId')]//input[contains(@id,'ObjectIdText')]"))
				.sendKeys(testresults);
		autocompleteSelect(testresults);
		driver.findElement(
				By.xpath("//div[@id='modal-form']//input[@value='tasktracetestexecution']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
				.click();
	}
	
	public void addDocs(String doc)
	{
		clickTraceTab();
		addDocsBtn.click();
		driver.findElement(
				By.xpath("//div[@id='modal-form']//input[@value='tasktracehelppage']/following-sibling::div[contains(@id,'fieldRowObjectId')]//input[contains(@id,'ObjectIdText')]"))
				.sendKeys(doc);
		autocompleteSelect(doc);
		driver.findElement(
				By.xpath("//div[@id='modal-form']//input[@value='tasktracehelppage']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
				.click();
	}
	
	public void addWatcher(String watcher)
	{
		clickMoreTab();
		addWarchersBtn.click();
		driver.findElement(
				By.xpath("//div[@id='modal-form']//input[@value='watcher']/following-sibling::div[contains(@id,'fieldRowSystemUser')]//input[contains(@id,'SystemUserText')]"))
				.sendKeys(watcher);
		autocompleteSelect(watcher);
		driver.findElement(
				By.xpath("//div[@id='modal-form']//input[@value='watcher']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
				.click();

	}
	
	//TODO implement this - find a way to dismiss Open dialog
	public void addAttachment(File file)
	{
		clickMainTab();
		//turn off popup dialog
		String codeIE = "$.browser.msie = true; document.documentMode = 8;";
		((JavascriptExecutor) driver).executeScript(codeIE);
		addAttachmentBtn.click();
		// make file input visible
		((JavascriptExecutor) driver).executeScript("document.evaluate(\"//div[@id='modal-form']//input[contains(@id,'_File') and @type='file']\", document, null, 9, null).singleNodeValue.removeAttribute('style')");
		// fill file input
		driver.findElement(By.xpath("//div[@id='modal-form']//input[contains(@id,'_File') and @type='file']")).sendKeys(file.getAbsolutePath());
		driver.findElement(By.xpath("//div[@id='modal-form']//span[@id='pm_TaskAttachment']//input[@action='save']")).click();
	}
}
