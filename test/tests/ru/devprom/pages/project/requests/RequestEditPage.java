package ru.devprom.pages.project.requests;

import java.io.File;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.Keys;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.helpers.DateHelper;
import ru.devprom.items.ProductFunction;
import ru.devprom.items.RTask;
import ru.devprom.items.Request;
import ru.devprom.items.Spent;
import ru.devprom.pages.CKEditor;

//TODO add Remove methods for all the embedded objects

public class RequestEditPage extends RequestNewPage {

	@FindBy(id = "FunctionText")
	protected WebElement functionsList;

	@FindBy(xpath = "//form//span[@name='pm_ChangeRequestTags']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addTag;

	@FindBy(id = "AuthorText")
	protected WebElement authorEdit;

	@FindBy(id = "PlannedReleaseText")
	protected WebElement releaseSelect;

	@FindBy(id = "SubmittedVersionText")
	protected WebElement versionsList;

	@FindBy(id = "ClosedInVersionText")
	protected WebElement closedInVersionList;

	@FindBy(xpath = "//form//span[@name='pm_ChangeRequestTasks']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addTaskBtn;

	@FindBy(xpath = "//form//input[@value='task']/following-sibling::div[contains(@id,'fieldRowTaskType')]//select")
	protected WebElement taskTypeSelect;

	@FindBy(xpath = "//form//input[@value='task']/following-sibling::div[contains(@id,'fieldRowAssignee')]//select")
	protected WebElement taskExecutorSelect;

	@FindBy(xpath = "//form//a[contains(@class,'file-browse')]")
	protected WebElement addAttachmentBtn;

	@FindBy(xpath = "//form//span[@name='pm_ChangeRequestLinks']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addLinkedReqsBtn;

	@FindBy(xpath = "//form//input[@value='requestlink']/following-sibling::div[contains(@id,'fieldRowLinkType')]//select[contains(@id,'LinkType')]")
	protected WebElement linkedTypeSelect;

	@FindBy(xpath = "//form//span[@name='pm_ChangeRequestDeadlines']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addDeadlineBtn;

	@FindBy(xpath = "//form//span[@name='pm_ChangeRequestRequirement']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addRequirementsBtn;

	@FindBy(xpath = "//form//span[@name='pm_ChangeRequestWatchers']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addWatcherBtn;
	
	@FindBy(xpath = "//form//span[@name='pm_ChangeRequestFact']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addSpentTimeBtn;

	@FindBy(id = "pm_ChangeRequestDeleteBtn")
	protected WebElement deleteBtn;

	@FindBy(xpath = "//form//span[@name='pm_ChangeRequestTestScenario']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addTestDocBtn;

	@FindBy(xpath = "//form//span[@name='pm_ChangeRequestHelpPage']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addDocsBtn;

	@FindBy(xpath = "//form//span[@name='pm_ChangeRequestSourceCode']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addSourceCodeBtn;

	public RequestEditPage(WebDriver driver) {
		super(driver);
	}

	public RequestsPage completeNewCRShort(Request request) {
		FILELOG.error("Not applicable method");
		return null;
	}

	public RequestsPage createNewCR(Request request) {
		FILELOG.error("Not applicable method");
		return null;
	}

	public RequestViewPage saveEdited()
	{
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.elementToBeClickable(submitBtn));
		submitDialog(submitBtn);
		return new RequestViewPage(driver);
	}
	
	public RequestsBoardPage saveEditedFromBoard()
	{
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.elementToBeClickable(submitBtn));
		submitDialog(submitBtn);
		return new RequestsBoardPage(driver);
	}

	public void editName(String name)
	{
		clickMainTab();
		captionEdit.clear();
		captionEdit.sendKeys(name);
	}

	public void editDescription(String description)
	{
		clickMainTab();
		(new CKEditor(driver)).changeText(description);
	}

	public void editType(String type)
	{
		clickMainTab();
		try {
			(new Select(typesList)).selectByVisibleText(type);
		} catch (NoSuchElementException e) {
			FILELOG.error("There is no type: " + type);
		}
	}

	public void editEstimation(double estimation)
	{
		clickMainTab();
		estimationEdit.clear();
		estimationEdit.sendKeys(String.valueOf(estimation));
	}

	public void addSpentTimeRecord(Spent spent)
	{
		clickMoreTab();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOf(addSpentTimeBtn));
		addSpentTimeBtn.click();
		WebElement reportDate = driver.findElement(
				By.xpath("//form//input[@value='activityrequest']/following-sibling::div[contains(@id,'fieldRowReportDate')]//input[contains(@id,'ReportDate')]"));
		if (!spent.date.equals(DateHelper.getCurrentDate())) {
			reportDate.clear();
			reportDate.sendKeys(spent.date);
        }
		reportDate.sendKeys(Keys.TAB);
		driver.findElement(
				By.xpath("//form//input[@value='activityrequest']/following-sibling::div//input[contains(@id,'Capacity')]"))
				.sendKeys(String.valueOf(spent.hours));
		driver.findElement(
				By.xpath("//form//input[@value='activityrequest']/following-sibling::div[contains(@id,'fieldRowDescription')]//textarea[contains(@id,'Description')]"))
				.sendKeys(spent.description);
		driver.findElement(
				By.xpath("//form//input[@value='activityrequest']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
				.click();
	}

	public String readId()
	{
		String id = driver
				.findElement(
						By.xpath("//ul[contains(@class,'breadcrumb')]/li/a[contains(@class,'with-tooltip')]"))
				.getText();
		return id.substring(1, id.length() - 1);
	}
	
	public void editFunction(String function) {

	}

	public RequestsPage deleteRequest()
	{
		deleteBtn.click();
		safeAlertAccept();
		return new RequestsPage(driver);
	}

	public void addTestDoc(String testdoc)
	{
		clickTraceTab();
		addTestDocBtn.click();
		try {
			driver.findElement(
					By.xpath("//form//input[@value='requesttracetestscenario']/following-sibling::div[contains(@id,'fieldRowObjectId')]//input[contains(@id,'ObjectIdText')]"))
					.sendKeys(testdoc);
			autocompleteSelect(testdoc);
			driver.findElement(
					By.xpath("//form//input[@value='requesttracetestscenario']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
					.click();

		} catch (NoSuchElementException e) {
			FILELOG.error("Test documentation " + testdoc
					+ " is not found in the system");
		}
	}

	public void addDocs(String docs)
	{
		clickTraceTab();
		addDocsBtn.click();
		try {
			driver.findElement(
					By.xpath("//form//input[@value='requesttracehelppage']/following-sibling::div[contains(@id,'fieldRowObjectId')]//input[contains(@id,'ObjectIdText')]"))
					.sendKeys(docs);
			autocompleteSelect(docs);
			driver.findElement(
					By.xpath("//form//input[@value='requesttracehelppage']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
					.click();

		} catch (NoSuchElementException e) {
			FILELOG.error("Documentation " + docs
					+ " is not found in the system");
		}
	}

	public void addSourceCode(String sourcecode)
	{
		clickTraceTab();
		addDocsBtn.click();
		try {
			driver.findElement(
					By.xpath("//form//input[@value='requesttracesourcecode']/following-sibling::div[contains(@id,'fieldRowObjectId')]//input[contains(@id,'ObjectIdText')]"))
					.sendKeys(sourcecode);
			autocompleteSelect(sourcecode);
			driver.findElement(
					By.xpath("//form//input[@value='requesttracesourcecode']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
					.click();

		} catch (NoSuchElementException e) {
			FILELOG.error("Source code " + sourcecode
					+ " is not found in the system");
		}
	}

	// TODO complete
	public void updateRequest(Request request) {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(captionEdit));
		captionEdit.clear();
		captionEdit.sendKeys(request.getName());

		
		estimationEdit.clear();
		estimationEdit.sendKeys(String.valueOf(request.getEstimation()));
		
		if (!"".equals(request.getType())) setType(request.getType());
		
		if (!"".equals(request.getPriority())) (new Select(priorityList)).selectByVisibleText(request.getPriority());
		editDescription(request.getDescription());
	
	}

	public void setType(String type)
	{
		clickMoreTab();
		(new Select(typesList)).selectByVisibleText(type);
	}

	public void addTag(String tag) throws InterruptedException {
		clickMainTab();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOf(addTag));
		addTag.click();
		(new WebDriverWait(driver, waiting))
				.until(ExpectedConditions.visibilityOfElementLocated(By
						.xpath("//form//input[@value='requesttag']/following-sibling::div[contains(@id,'fieldRowTag')]//input[contains(@id,'TagText')]")));
		driver.findElement(
				By.xpath("//form//input[@value='requesttag']/following-sibling::div[contains(@id,'fieldRowTag')]//input[contains(@id,'TagText')]"))
				.sendKeys(tag);
		Thread.sleep(3000);
		driver.findElement(
				By.xpath("//form//input[@value='requesttag']/following-sibling::div[contains(@class,'embedded_footer')]//input[contains(@id,'saveEmbedded')]"))
				.click();
		Thread.sleep(3000);
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOf(addTag));
	}

	public void setVersion(String version)
	{
		clickMoreTab();
		versionsList.clear();
		versionsList.sendKeys(version);
		autocompleteSelect(version);
	}

	public void setClosedVersion(String version)
	{
		clickMoreTab();
		closedInVersionList.sendKeys(version);
		autocompleteSelect(version);
	}

	public void addTask(RTask task)
	{
		clickMainTab();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOf(addTaskBtn));
		addTaskBtn.click();
		WebElement input = driver.findElement(
				By.xpath("//form//input[@value='task']/following-sibling::div[contains(@id,'fieldRowCaption')]//input[contains(@name,'Caption')]"));
		input.clear();
		input.sendKeys(task.getName());
   		(new Select(taskTypeSelect)).selectByVisibleText(task.getType());

   		if ("".equals(task.getExecutor()))(new Select(taskExecutorSelect)).selectByIndex(1);
   		else {
		try {
			(new Select(taskExecutorSelect)).selectByVisibleText(task
					.getExecutor());
		} catch (NoSuchElementException e) {
			FILELOG.error("There is no executor: " + task.getExecutor());
		}
   		}
		driver.findElement(
				By.xpath("//form//input[@value='task']/following-sibling::div[contains(@id,'fieldRowPlanned')]//input[contains(@id,'Planned')]"))
				.sendKeys(String.valueOf(task.getEstimation()));
		driver.findElement(
				By.xpath("//form[child::input[@value='task']]//input[contains(@id,'saveEmbedded')]"))
				.click();
	}

	public void addLinkedReqs(String request, String typelink)
	{
		clickTraceTab();
		addLinkedReqsBtn.click();
		driver.findElement(
				By.xpath("//form//input[@value='requestlink']/following-sibling::div[contains(@id,'fieldRowTargetRequest')]//input[contains(@id,'TargetRequestText')]"))
				.sendKeys(request);
		autocompleteSelect(request);

		try {
			(new Select(linkedTypeSelect)).selectByVisibleText(typelink);
		} catch (NoSuchElementException e) {
			FILELOG.error("There is no link type: " + typelink);
		}
		driver.findElement(
				By.xpath("//form//input[@value='requestlink']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
				.click();
	}

	public void setRelease(String release)
	{
		clickDeadlinesTab();
		releaseSelect.clear();
		releaseSelect.sendKeys(release);
		if ( !release.equals("") ) autocompleteSelect(release);
	}

	public void addDeadline(String veha)
	{
		clickDeadlinesTab();
		addDeadlineBtn.click();
		try {
			driver.findElement(
					By.xpath("//form//input[@value='requesttracemilestone']/following-sibling::div[contains(@id,'fieldRowObjectId')]//input[contains(@id,'ObjectIdText')]"))
					.sendKeys(veha);
			autocompleteSelect(veha);
			driver.findElement(
					By.xpath("//form//input[@value='requesttracemilestone']/following-sibling::div[contains(@id,'fieldRowObjectId')]//input[contains(@id,'ObjectIdText')]"))
					.sendKeys(Keys.ESCAPE);
			driver.findElement(
					By.xpath("//form//input[@value='requesttracemilestone']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
					.click();
		} catch (NoSuchElementException e) {
			FILELOG.error("Deadline " + veha + " is not found in the system");
		}
	}

	public void addNewDeadline(String deadlineName, Spent spent)
	{
		clickDeadlinesTab();
		addDeadlineBtn.click();
		driver.findElement(
		By.xpath("//form//input[@value='requesttracemilestone']/following-sibling::div[contains(@id,'fieldRowObjectId')]//input[contains(@id,'ObjectIdText')]")).sendKeys(deadlineName);
		WebElement deadlineDate = 
				driver.findElement(
						By.xpath("//form//input[@value='requesttracemilestone']/following-sibling::div[contains(@id,'fieldRowDeadline')]//input[contains(@id,'Deadline')]"));
		deadlineDate.clear();
		deadlineDate.sendKeys(spent.date);
		deadlineDate.sendKeys(Keys.TAB);
		driver.findElement(
				By.xpath("//form//input[@value='requesttracemilestone']/following-sibling::div[contains(@id,'fieldRowDeadlineCaption')]//input[contains(@id,'DeadlineCaption')]")).sendKeys(spent.description);
		driver.findElement(
				By.xpath("//form//input[@value='requesttracemilestone']/following-sibling::div//input[contains(@id,'saveEmbedded')]")).click();
	}

	public void addRequirements(String requirements)
	{
		clickTraceTab();
		addRequirementsBtn.click();
		driver.findElement(
				By.xpath("//form//input[@value='requesttracerequirement']/following-sibling::div[contains(@id,'fieldRowObjectId')]//input[contains(@id,'ObjectIdText')]"))
				.sendKeys(requirements);
		autocompleteSelect(requirements);
		driver.findElement(
				By.xpath("//form//input[@value='requesttracerequirement']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
				.click();

	}

	public void addWatcher(String username)
	{
		clickMoreTab();
		scrollWithOffset(addWatcherBtn, 0, 100);
		addWatcherBtn.click();
		driver.findElement(
				By.xpath("//form//input[@value='watcher']/following-sibling::div//input[contains(@id,'SystemUserText')]"))
				.sendKeys(username);
		autocompleteSelect(username);
		driver.findElement(
				By.xpath("//form//input[@value='watcher']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
				.click();

	}

	public void addAttachment(File attachment)
	{
		clickMainTab();

		String codeIE = "$('input[type]').css('visibility','visible')";
		((JavascriptExecutor) driver).executeScript(codeIE);
		
		addAttachmentBtn.findElement(By.xpath(".//input")).sendKeys(attachment.getAbsolutePath());

		try {
			Thread.sleep(1000);
		} catch (InterruptedException e) {
		}
	}
}
