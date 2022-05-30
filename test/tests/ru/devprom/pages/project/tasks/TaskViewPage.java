package ru.devprom.pages.project.tasks;

import java.util.ArrayList;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.Keys;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.helpers.DateHelper;
import ru.devprom.items.Commit;
import ru.devprom.items.Project;
import ru.devprom.items.Spent;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.SpentTimePage;
import ru.devprom.pages.project.requests.RequestViewPage;

public class TaskViewPage extends SDLCPojectPageBase {

	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(.,'Действия')]")
	protected WebElement actionsBtn;

	@FindBy(xpath = "//a[@id='workflow-resolved']")
	protected WebElement completeBtn;
	
	@FindBy(xpath = "//a[@id='modify']")
	protected WebElement editBtn;
	
	@FindBy(xpath = "//span[@name='pm_TaskTestScenario']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addTestDocBtn;
	
	@FindBy(xpath = "//span[@name='pm_TaskFact']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addSpentTimeBtn;
	
	@FindBy(id = "pm_TaskRelease")
	protected WebElement iterationSelect;

	@FindBy(id = "pm_TaskTaskType")
	protected WebElement typeSelect;

	@FindBy(id = "ChangeRequestText")
	protected WebElement requestsList;

	@FindBy(id = "pm_TaskAssignee")
	protected WebElement executorList;

	@FindBy(id = "pm_TaskPriority")
	protected WebElement prioritySelect;

	@FindBy(id = "pm_TaskPlanned")
	protected WebElement estimationEdit;

	@FindBy(id = "pm_TaskLeftWork")
	protected WebElement estimationLeftEdit;

	@FindBy(xpath = "//div[@class='comment']/a")
	protected WebElement addComment;
	
	@FindBy(id = "btn")
	protected WebElement saveCommentBtn;
	
	public TaskViewPage(WebDriver driver) {
		super(driver);
	}

	public TaskViewPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public TaskCompletePage completeTask() {
		actionsBtn.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(completeBtn));
		completeBtn.click();
		return new TaskCompletePage(driver);
	}
	
	public TaskEditPage editTask() {
		actionsBtn.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(editBtn));
		editBtn.click();
		return new TaskEditPage(driver);
	}
	
	public void addTestDocumentation(String testdoc){
		expandAdditional();
		addTestDocBtn.click();
		try {
			driver.findElement(
					By.xpath("//input[@value='tasktracetestscenario']/following-sibling::div[contains(@id,'fieldRowObjectId')]//input[contains(@id,'ObjectIdText')]"))
					.sendKeys(testdoc);
			autocompleteSelect(testdoc);
			driver.findElement(
					By.xpath("//input[@value='tasktracetestscenario']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
					.click();

		} catch (NoSuchElementException e) {
			FILELOG.error("Test documentation " + testdoc
					+ " is not found in the system");
		}
	}

	public void expandAdditional() {
		if ( addTestDocBtn.isDisplayed() ) return;
		driver.findElement(
				By.xpath("//a[contains(@href,'collapsetrace')]")).click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(addTestDocBtn));
	}
	
	public String readName(){
		return driver.findElement(By.xpath("//*[contains(@id,'pm_TaskCaption')]")).getText();
	}
	
	public String readStatus(){
	  return driver.findElement(By.xpath("//span[@id='state-label']")).getText();
	}
	
	public String readIteration(){
		return driver.findElement(By.id("pm_TaskRelease")).getText();
	}
	
	public String readType(){
		return driver.findElement(By.xpath("//*[@id='pm_TaskTaskType']")).getText();
	}
	
	public String readOwner(){
		return driver.findElement(By.xpath("//*[@id='pm_TaskAssignee']")).getText();
	}
	
	public String readPriority(){
		return driver.findElement(By.xpath("//*[@id='pm_TaskPriority']")).getText();
	}
	
	public double readEstimatesPlanned(){
		if (driver.findElement(By.id("pm_TaskPlanned")).getText().equals(null) || driver.findElement(By.id("pm_TaskPlanned")).getText().equals(""))
			return 0.0;
		else return Double.parseDouble(driver.findElement(By.id("pm_TaskPlanned")).getText());
	}
	
	public String readRequest(){
		try {
		return driver.findElement(By.name("pm_TaskChangeRequest")).getText();
		}
		catch (NoSuchElementException e) {
			return "No Requests";
		}
		}
	
	public List<String> readPreviousTasks(){
		List<String> result = new ArrayList<String>();
		List<WebElement> elements = driver.findElements(By.xpath("//input[@value='tasktracetask']/following-sibling::div[contains(@id,'embeddedItems')]/div[contains(@id,'Caption')]//*[contains(@class,'title')]"));
	    for (WebElement element:elements){
	    	result.add(element.getText());
	    }
	    return result;
	}
	
	public List<String> readRequirements(){
		List<String> result = new ArrayList<String>();
		List<WebElement> elements = driver.findElements(By.xpath("//input[@value='tasktracerequirement']/following-sibling::div[contains(@id,'embeddedItems')]/div[contains(@id,'Caption')]//*[contains(@class,'title')]"));
	    for (WebElement element:elements){
	    	result.add(element.getText());
	    }
	    return result;
	}
	
	public List<String> readTestDoc(){
		List<String> result = new ArrayList<String>();
		List<WebElement> elements = driver.findElements(By.xpath("//input[@value='tasktracetestscenario']/following-sibling::div[contains(@id,'embeddedItems')]/div[contains(@id,'Caption')]//*[contains(@class,'title')]"));
	    for (WebElement element:elements){
	    	result.add(element.getText());
	    }
	    return result;
	}
	
	public List<String> readTestResults(){
		List<String> result = new ArrayList<String>();
		List<WebElement> elements = driver.findElements(By.xpath("//input[@value='tasktracetestexecution']/following-sibling::div[contains(@id,'embeddedItems')]/div[contains(@id,'Caption')]//*[contains(@class,'title')]"));
	    for (WebElement element:elements){
	    	result.add(element.getText());
	    }
	    return result;
	}
	
	public List<String> readDocs(){
		List<String> result = new ArrayList<String>();
		List<WebElement> elements = driver.findElements(By.xpath("//input[@value='tasktracehelppage']/following-sibling::div[contains(@id,'embeddedItems')]/div[contains(@id,'Caption')]//*[contains(@class,'title')]"));
	    for (WebElement element:elements){
	    	result.add(element.getText());
	    }
	    return result;
	}
	
	public List<String> readWatchers(){
		List<String> result = new ArrayList<String>();
		List<WebElement> elements = driver.findElements(By.xpath("//input[@value='watcher']/following-sibling::div[contains(@id,'embeddedItems')]/div[contains(@id,'Caption')]//*[contains(@class,'title')]"));
	    for (WebElement element:elements){
	    	result.add(element.getText());
	    }
	    return result;
	}
	
	public List<Commit> readCommitRecords() {
		List<WebElement> captions = driver
				.findElements(By
						.xpath("//span[@name='pm_TaskSourceCode']//span[contains(@class,'title')]"));
		List<Commit> commitArray = new ArrayList<Commit>();
		for (int i = 0; i < captions.size(); i++) {
			try {
				commitArray.add(new Commit(captions.get(i).getText()));
			}
			catch(StringIndexOutOfBoundsException e) {
			}
		}
		return commitArray;
	}

	public void addSpentTimeRecord(Spent spent) {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOf(addSpentTimeBtn));
		addSpentTimeBtn.click();
		WebElement reportDate = driver.findElement(
					By.xpath("//input[@value='activitytask']/following-sibling::div[contains(@id,'fieldRowReportDate')]//input[contains(@id,'ReportDate')]"));
		reportDate.clear();
		reportDate.sendKeys(spent.date);
		reportDate.sendKeys(Keys.TAB);
		driver.findElement(
				By.xpath("//input[@value='activitytask']/following-sibling::div//input[contains(@id,'Capacity')]"))
				.sendKeys(String.valueOf(spent.hours));
		driver.findElement(
				By.xpath("//input[@value='activitytask']/following-sibling::div[contains(@id,'fieldRowDescription')]//textarea[contains(@id,'Description')]"))
				.sendKeys(spent.description);
		driver.findElement(
				By.xpath("//input[@value='activitytask']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
				.click();
	}

	public List<Spent> readSpentRecords()
	{
		String url = driver.getCurrentUrl();
		clickOnInvisibleElement(driver.findElement(By.id("activity-edit")));
		List<Spent> spentList = (new SpentTimePage(driver)).readSpentRecords();
		driver.navigate().to(url);
		return spentList;
	}

	public TaskViewPage addComment(String comment) {
		scrollToElement(addComment);
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.elementToBeClickable(addComment));
		
		addComment.click();
		(new WebDriverWait(driver, waiting)).
			until(ExpectedConditions.presenceOfElementLocated(
					By.xpath("//div[contains(@id,'comments-form')]")));
		try {
			Thread.sleep(2000);
		} catch (InterruptedException e) {
		}

		CKEditor we = new CKEditor(driver);
		we.typeText(comment);

		saveCommentBtn.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOfElementLocated(By.xpath("//div[@class='comment-text' and contains(.,'"+comment+"')]")));
		
		return new TaskViewPage(driver);
	}

	
	public String readLastComment(){
		if (driver.findElements(By.xpath("//div[@class='comment-text']")).isEmpty()) return "Комментариев нет";
		else
		return driver.findElement(By.xpath("//div[@class='comment-text']//p")).getText();
	}
	
	public List<String> readAttachmentHeaders(){
		List<String> headers = new ArrayList<String>();
		List<WebElement> elements = driver.findElements(By.xpath("//a[contains(@id,'File')]"));
		for (WebElement el:elements){
			headers.add(el.getText());
		}
		return headers;
	}

    public void clickOnTestScenario(String id) {
        driver.findElement(By.xpath("//*[@id='fieldRowIssueTraces']//*[contains(.,'"+id+"')]")).click();
    }
}
