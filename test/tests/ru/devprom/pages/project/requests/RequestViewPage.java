package ru.devprom.pages.project.requests;

import java.util.ArrayList;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.Keys;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.helpers.DateHelper;
import ru.devprom.items.Commit;
import ru.devprom.items.Project;
import ru.devprom.items.RTask;
import ru.devprom.items.Request;
import ru.devprom.items.Requirement;
import ru.devprom.items.Spent;
import ru.devprom.items.Milestone;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.SpentTimePage;
import ru.devprom.pages.project.requirements.RequirementNewPage;
import ru.devprom.pages.project.testscenarios.TestScenarioTestingPage;

public class RequestViewPage extends SDLCPojectPageBase
{
	@FindBy(xpath = "//a[@id='workflow-inprogress']")
	protected WebElement analyseBtn;

	@FindBy(xpath = "//a[contains(@id,'requirement')]")
	protected WebElement createRequirementBtn;

	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(.,'Действия')]")
	protected WebElement actionsBtn;

	@FindBy(xpath = "//a[@id='modify']")
	protected WebElement editBtn;

	@FindBy(xpath = "//span[@name='pm_ChangeRequestFact']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addSpentTimeBtn;

	@FindBy(id = "state-label")
	protected WebElement stateLabel;

	@FindBy(xpath = "//table[@class='properties-table']//tr[@name='Type']/td")
	protected WebElement typeLabel;

	@FindBy(xpath = "//table[@class='properties-table']//tr[@name='Priority']/td")
	protected WebElement priorityLabel;

	@FindBy(xpath = "//table[@class='properties-table']//tr[@name='Estimation']/td")
	protected WebElement estimatesLabel;

	@FindBy(xpath = "//table[@class='properties-table']//tr[@name='Author']/td")
	protected WebElement originatorLabel;

	@FindBy(xpath = "//input[@value='requesttracemilestone']/following-sibling::div[contains(@id,'embeddedItems')]//*[contains(@class,'title')]")
	protected WebElement vehaLabel;

	@FindBy(xpath = "//div[@class='accordion-heading']/a[contains(.,'Свойства')]")
	protected WebElement propertiesField;

	@FindBy(xpath = "//div[@class='accordion-heading']/a[contains(.,'Описание')]")
	protected WebElement descriptionField;

	@FindBy(xpath = "//div[@class='accordion-heading']/a[contains(.,'Задачи')]")
	protected WebElement tasksField;

	@FindBy(xpath = "//div[@class='accordion-heading']/a[contains(.,'Комментарии и изменения')]")
	protected WebElement commentsField;

	@FindBy(xpath = "//a[@href='#collapseComments']")
	protected WebElement collapseComments;

	@FindBy(xpath = "//div[@class='comment']/a")
	protected WebElement addComment;

	@FindBy(xpath = "//a[@id='workflow-resolved']")
	protected WebElement completeRequest;

	@FindBy(xpath = "//a[@id='workflow-inrelease']")
	protected WebElement includeToReleaseLink;

	@FindBy(xpath = "//a[@id='workflow-submitted']")
	protected WebElement backToJournalLink;

	@FindBy(id = "btn")
	protected WebElement saveBtn;

	@FindBy(xpath = "//a[@id='workflow-submitted']")
	protected WebElement rejectRequest;

	@FindBy(xpath = "//a[@id='workflow-planned']")
	protected WebElement planRequest;

	@FindBy(xpath = "//ul//a[@id='run-test']")
	protected WebElement beginTesting;

	@FindBy(xpath = "//ul//a[@id='implement']")
	protected WebElement duplicateRequest;

	@FindBy(xpath = "//ul//a[text()='Перенести в проект']")
	protected WebElement moveRequestBtn;

	@FindBy(xpath = "//ul//a[text()='Удалить']")
	protected WebElement deleteBtn;

	@FindBy(id = "pm_ChangeRequestSubmitBtn")
	protected WebElement submitBtn;

	@FindBy(id = "collapseTwo")
	protected WebElement captionEdit;

	public RequestViewPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public RequestViewPage(WebDriver driver) {
		super(driver);
	}

	public RequestEditPage gotoEditRequest() {
		actionsBtn.click();
		clickOnInvisibleElement(editBtn);
		waitForDialog();
		return new RequestEditPage(driver);
	}

	public String readID() {
		return driver.findElement(By.xpath("//ul[contains(@class,'breadcrumb')]//button")).getText().trim();
	}

	public String readName() {
		return driver.findElement(By.xpath("//*[contains(@id,'pm_ChangeRequestCaption')]")).getText();
	}

	public String readType() {
		if (!typeLabel.isDisplayed())
			propertiesField.click();
		return typeLabel.getText();
	}

	public String readPriority() {
		if (!priorityLabel.isDisplayed())
			propertiesField.click();
		return priorityLabel.getText().trim();
	}

	public double readEstimates() {
		if (!estimatesLabel.isDisplayed())
			propertiesField.click();
		if (estimatesLabel.getText().equals(null)
				|| estimatesLabel.getText().equals(""))
			return 0.0;
		else
			return Double.parseDouble(estimatesLabel.getText().trim());
	}

	public String readFunctionName() {
		try {
			String name = driver
					.findElement(
							By.xpath("//th[contains(.,'Функция')]/following-sibling::td"))
					.getText().trim();
			return name.split("\\]")[1].trim();
		} catch (NoSuchElementException e) {
			FILELOG.debug("No function found");
			return "";
		}
	}

	public String readOriginator() {
		if (!originatorLabel.isDisplayed())
			propertiesField.click();
		return originatorLabel.getText().trim();
	}

	// TODO add reading of multiple Milestone's
	public Milestone readDeadine() {
		if (!vehaLabel.isDisplayed())
			propertiesField.click();
		String text = vehaLabel.getText();
		return new Milestone("date", text);
	}

	public String readState() {
		if (!stateLabel.isDisplayed())
			propertiesField.click();
		return stateLabel.getText().trim();
	}

	public String readUserAttribute(String attributeName) {
		(new WebDriverWait(driver, waiting))
				.until(ExpectedConditions.presenceOfElementLocated(By
						.xpath("//table[@class='properties-table']//th[contains(.,'"
								+ attributeName + "')]/following-sibling::td")));

		return driver
				.findElement(
						By.xpath("//table[@class='properties-table']//th[contains(.,'"
								+ attributeName + "')]/following-sibling::td"))
				.getText().trim();
	}

	public Request readRequest() {
		Request r = new Request(readID(), readName(), readType(), readState(),
				readPriority());
		r.setOriginator(readOriginator());
		r.setEstimation(readEstimates());
		r.setPfunction(readFunctionName());
		// TODO fill with other parameters
		return r;
	}

	public RequestViewPage addComment(String comment) {
		scrollToElement(addComment);
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.elementToBeClickable(addComment));
		
		addComment.click();
		(new WebDriverWait(driver, waiting)).
			until(ExpectedConditions.presenceOfElementLocated(
					By.xpath("//div[contains(@id,'comments-form')]")));

		CKEditor we = new CKEditor(driver);
		we.typeText(comment);

		saveBtn.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOfElementLocated(By.xpath("//div[@class='comment-text' and contains(.,'"+comment+"')]")));

		return new RequestViewPage(driver);
	}

	public RequestViewPage addCommentWithAttachment(String comment, String attachmentPath) {

		if (!addComment.isDisplayed()) {
			collapseComments.click();
		}
		scrollToElement(addComment);
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.elementToBeClickable(addComment));
		
		addComment.click();
		(new WebDriverWait(driver, waiting)).
			until(ExpectedConditions.presenceOfElementLocated(
					By.xpath("//div[contains(@id,'comments-form')]")));

		CKEditor we = new CKEditor(driver);
		we.typeText(comment);

		String codeIE = "$('input[type]').css('visibility','visible')";
		((JavascriptExecutor) driver).executeScript(codeIE);
		
		driver.findElement(
				By.xpath("//div[@id='collapseComments']//*[contains(@class,'file-browse')]//input")).sendKeys(attachmentPath);

		try {
			Thread.sleep(1000);
		} catch (InterruptedException e) {
		}
		
		saveBtn.click();
		
		return new RequestViewPage(driver);
	}

	public String readComment(int number) throws InterruptedException {
		FILELOG.debug("Reeading first comment number: " + number);
		Thread.sleep(4000);
		if (!driver
				.findElements(
						By.xpath("//a[@id='comments-section' and contains(@class,'collapsed')]"))
				.isEmpty()) {
			driver.findElement(
					By.xpath("//a[@id='comments-section' and contains(@class,'collapsed')]"))
					.click();
			Thread.sleep(1000);
		}
		return driver.findElement(
				By.xpath(".//*[@id='comment" + number + "']//div[@class='comment-text']//div[contains(@class,'wysiwyg')]"))
				.getText();
	}

	public boolean isPictureFromCommentOpens(String text) {
		driver.findElement(
				By.xpath("//div[contains(@class,'comment-text') and contains(.,'"+text+"')]//a[contains(@class,'_attach')]")).click();
		boolean isOpens = driver.findElements(By.xpath("//img[@class='fancybox-image']")).size() > 0;
		driver.navigate().back();
		return isOpens;
	}

	public int readFirstInPageCommentNumber() {
		String commentId = driver.findElement(
				By.xpath(".//*[contains(@id,'comment') and contains(@class,'comment-line')]"))
				.getAttribute("id");
		String[] id = commentId.split("t");
		return Integer.parseInt(id[1]);
	}

	public RequestViewPage addAnswerToComment(int commentNumber, String answer) {
		try {
			Thread.sleep(6000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		driver.findElement(By.xpath(".//div[contains(@class,'comments-reply') and @object-id='" + commentNumber + "']//*[contains(@class,'btn')]")).click();

		CKEditor we = new CKEditor(driver);
		we.typeText(answer);

		saveBtn.click();
		return new RequestViewPage(driver);
	}

	public RequestDonePage completeRequest() {
		actionsBtn.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOf(completeRequest));
		completeRequest.click();
		waitForDialog();
		return new RequestDonePage(driver);
	}

	public RequestViewPage completeRequest(String version) {
		actionsBtn.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOf(completeRequest));
		completeRequest.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By.id("ClosedInVersionText")));
		driver.findElement(By.id("ClosedInVersionText")).clear();
		driver.findElement(By.id("ClosedInVersionText")).sendKeys(version);
		driver.findElement(By.id("pm_ChangeRequestSubmitBtn")).click();
		(new WebDriverWait(driver, waiting))
				.until(ExpectedConditions.presenceOfElementLocated(By
						.xpath("//span[@id='state-label' and contains(.,'Выполнено')]")));
		return new RequestViewPage(driver);
	}

	public RequestRejectPage rejectRequest() {
		actionsBtn.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOf(rejectRequest));
		rejectRequest.click();
		waitForDialog();;
		return new RequestRejectPage(driver);
	}

	public RequestViewPage includeToRelease(String releaseNumber) {
		actionsBtn.click();
		clickOnInvisibleElement(includeToReleaseLink);
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By
						.id("PlannedReleaseText")));

		WebElement input = driver.findElement(By.id("PlannedReleaseText"));
		if ( !input.getAttribute("value").equals(releaseNumber) ) {
			input.clear();
			input.sendKeys(releaseNumber); 
	   		autocompleteSelect(releaseNumber);
		}

		submitDialog(submitBtn);
		(new WebDriverWait(driver, waiting))
				.until(ExpectedConditions.presenceOfElementLocated(By
						.xpath("//span[@id='state-label' and contains(.,'В релизе')]")));
		return new RequestViewPage(driver);

	}

	public RequestIncludeToReleasePage includeToReleaseEx() {
		actionsBtn.click();
		clickOnInvisibleElement(includeToReleaseLink);
		waitForDialog();
		return new RequestIncludeToReleasePage(driver);
	}

	public RequestViewPage backToJournal(String comment) {
		actionsBtn.click();
		clickOnInvisibleElement(backToJournalLink);
		waitForDialog();
		(new CKEditor(driver)).typeText(comment);
		submitDialog(submitBtn);
		(new WebDriverWait(driver, waiting))
				.until(ExpectedConditions.presenceOfElementLocated(By
						.xpath("//span[@id='state-label' and contains(.,'Добавлено')]")));
		return new RequestViewPage(driver);
	}

	public RequestPlanningPage planRequest() {
		actionsBtn.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOf(planRequest));
		planRequest.click();
		return new RequestPlanningPage(driver);
	}

	public TestScenarioTestingPage beginTest(String version, String environment) 
	{
		clickOnInvisibleElement(beginTesting);
		waitForDialog();
		if (!"".equals(environment)) 	(new Select(driver.findElement(By.id("pm_TestEnvironment")))).selectByVisibleText(environment);
		driver.findElement(By.id("VersionText")).clear();
		driver.findElement(By.id("VersionText")).sendKeys(version);
		autocompleteSelect(version);
		submitDialog(driver.findElement(By.id("pm_TestSubmitOpenBtn")));
		return new TestScenarioTestingPage(driver);
	}

	public String[] readTestResults() 
	{
		openTracesSection();
		
		List<WebElement> testScenarios = driver
				.findElements(By
						.xpath("//span[@name='pm_ChangeRequestTestExecution']//div[contains(@class,'transparent-btn')]"));
		
		ArrayList<String> results = new ArrayList<String>();
		for (int i = 0; i < testScenarios.size(); i++) {
			String title = testScenarios.get(i).getText();
			if ( title.equals("") ) continue;
			results.add(title);
		}
		return results.toArray(new String[results.size()]);
	}

	public Requirement[] readRequirements() 
	{
		openTracesSection();
		
		String[] ss;
		String s;
		List<WebElement> requirementsList = driver
				.findElements(By
						.xpath("//input[@value='requesttracerequirement']/following-sibling::div[contains(@id,'embeddedItems')]//div[@class='embeddedRowTitle']"));
		
		ArrayList<Requirement> results = new ArrayList<Requirement>();
		for (int i = 0; i < requirementsList.size(); i++) {
			s = requirementsList.get(i).findElement(By.className("title")).getText();
			if ( s.equals("") ) continue;
			ss = s.split(" ");
			Requirement t = new Requirement(ss[ss.length - 1].trim());
			t.setId(s.split("]")[0].replace('[', ' ').trim());
			results.add(t);
		}
		return results.toArray(new Requirement[results.size()]);
	}

	public void openTracesSection()
	{
		WebElement section = driver.findElement(By.xpath("//a[contains(@href,'collapseFive')]"));
		if ( section.isDisplayed() ) {
			section.click();
			(new WebDriverWait(driver, 5)).until(ExpectedConditions
					.visibilityOf(driver.findElement(By.id("collapseFive"))));
		}
	}
	
	public List<RTask> readTasks() {
		List<RTask> results = new ArrayList<RTask>();
		((JavascriptExecutor) driver)
				.executeScript("document.evaluate(\"//div[@id='collapseThree']\", document, null, 9, null).singleNodeValue.removeAttribute('class')");
		List<WebElement> tasksList = driver
				.findElements(By
						.xpath("//div[@id='collapseThree']//div[contains(@id,'embeddedList')]//div[contains(@id,'Caption')]//*[contains(@class,'title')]"));
		for (WebElement el : tasksList) {
			String idText = el.findElement(By.xpath("./a[contains(@class,'uid')]")).getText();
			String id = idText.replaceAll("\\[", "").replaceAll("\\]", "");
			String state = el.findElement(By.xpath("./span[contains(@class,'label')]")).getText();
			String[] parts = el.getText().replace(idText, "").replace(state, "").replace("\u25CF","").split(":");
			String type = "";
			String name = "";
			if ( parts.length > 1 ) {
				type = parts[0].trim();
				name = parts[1].trim();
				String[] nameParts = name.split("\\[");
				if ( nameParts.length > 1 ) {
					name = nameParts[0].trim();
				}
			} else {
				type = parts[0].trim();
			}
			results.add(new RTask(id, name, type, "", state));
		}
		return results;
	}

	/**
	 * Use this method to get tag text decoration information (only controlled
	 * by tags, no css). The method searches the text in KB content and reads
	 * all the style tags for this text: bold, em, etc.
	 */
	public List<String> getStyleTagsForText(String text) {
		List<String> tags = new ArrayList<String>();

		((JavascriptExecutor) driver)
				.executeScript("document.evaluate(\"//div[@id='collapseTwo']\", document, null, 9, null).singleNodeValue.removeAttribute('class')");

		WebElement p = captionEdit.findElement(By
				.xpath(".//*[contains(.,'" + text + "')]"));
		String tag = p.getTagName();
		while (!tag.equals("div")) {
			tags.add(tag);
			p = p.findElement(By.xpath("./.."));
			tag = p.getTagName();
		}
		return tags;
	}

	public RequestViewPage duplicateRequest(String projectName) {
		actionsBtn.click();
		(new WebDriverWait(driver, 5)).until(ExpectedConditions
				.visibilityOf(duplicateRequest));
		duplicateRequest.click();

		(new WebDriverWait(driver, 20)).until(ExpectedConditions
				.presenceOfElementLocated(By.id("ProjectText")));
		driver.findElement(By.id("ProjectText")).clear();
		driver.findElement(By.id("ProjectText")).sendKeys(projectName);
		autocompleteSelect(projectName);
		submitDialog(driver.findElement(By.id("pm_ChangeRequestSubmitBtn")));
		return new RequestViewPage(driver);
	}

	public String readDescription() {
		return driver.findElement(By.xpath("//div[contains(@id,'pm_ChangeRequestDescription') and contains(@class,'wysiwyg-text')]")).getText().trim();
	}

	public List<Spent> readSpentRecords() 
	{
		String url = driver.getCurrentUrl();
		clickOnInvisibleElement(driver.findElement(By.id("activity-edit")));
		List<Spent> spentList = (new SpentTimePage(driver)).readSpentRecords();
		driver.navigate().to(url);
		return spentList;
	}

	public List<Commit> readCommitRecords() 
	{
		openTracesSection();
		
		List<WebElement> captions = driver
				.findElements(By
						.xpath("//span[@name='pm_ChangeRequestSourceCode']//span[contains(@class,'title')]"));
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

	public List<String> readAttachments() 
	{
		List<String> result = new ArrayList<String>();
		List<WebElement> captions = driver
				.findElements(By
						.xpath("//div[contains(@class,'attachment-items')]//a[contains(@class,'_attach')]"));
		for (int i = 0; i < captions.size(); i++) {
			String title = captions.get(i).getText().trim();
			if ( !title.equals("") ) result.add(title);
		}
		return result;
	}

	public RequestsPage deleteRequest() {
		actionsBtn.click();
		clickOnInvisibleElement(deleteBtn);
		safeAlertAccept();
		try {
			Thread.sleep(2000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		return new RequestsPage(driver);
	}

	public RequestsPage moveRequest(String projectName) {
		actionsBtn.click();
		(new WebDriverWait(driver, 5)).until(ExpectedConditions
				.visibilityOf(moveRequestBtn));
		moveRequestBtn.click();
		waitForDialog();
		driver.findElement(By.id("ProjectText")).sendKeys(projectName);
		autocompleteSelect(projectName);
		submitDialog(driver.findElement(By.id("SubmitBtn")));

		return new RequestsPage(driver);
	}

	public Boolean isSpentTimeVisible() {
		return driver
				.findElements(
						By.xpath("//input[@value='activityrequest']/following-sibling::div[contains(@id,'embeddedItems')]//*[contains(@class,'title')]"))
				.size() > 0;
	}

	public Boolean isDescriptionEditable() {
		return driver.findElements(By.xpath("//div[contains(@id,'pm_ChangeRequestDescription') and contains(@class,'wysiwyg-text')]")).size() < 1;
	}

	public RequestViewPage applySimpleTransition(String transitionName) {
		actionsBtn.click();
		WebElement transitionBtn = driver.findElement(By
				.xpath("//a[contains(@class,'btn') and contains(.,'" + transitionName + "')]"));
		(new WebDriverWait(driver, 3)).until(ExpectedConditions
				.visibilityOf(transitionBtn));
		transitionBtn.click();
		(new WebDriverWait(driver, 3)).until(ExpectedConditions
				.stalenessOf(transitionBtn));
		return new RequestViewPage(driver);
	}

	public Boolean isOperationAvailable(String operationName) {
		return driver.findElements(
				By.xpath("//ul//a[text()='"
						+ operationName + "']")).size() > 0;
	}

	public void addSpentTimeRecord(Spent spent) {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOf(addSpentTimeBtn));
		addSpentTimeBtn.click();
		WebElement reportDate = driver.findElement(By.xpath("//input[@value='activityrequest']/following-sibling::div[contains(@id,'fieldRowReportDate')]//input[contains(@id,'ReportDate')]"));
		reportDate.clear();
		reportDate.sendKeys(spent.date);
		reportDate.sendKeys(Keys.TAB);
		driver.findElement(
				By.xpath("//input[@value='activityrequest']/following-sibling::div//input[contains(@id,'Capacity')]"))
				.sendKeys(String.valueOf(spent.hours));
		driver.findElement(
				By.xpath("//input[@value='activityrequest']/following-sibling::div[contains(@id,'fieldRowDescription')]//textarea[contains(@id,'Description')]"))
				.sendKeys(spent.description);
		driver.findElement(
				By.xpath("//input[@value='activityrequest']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
				.click();
		waitForFilterActivity();
	}

	public RequirementNewPage clickActionCreateRequirement()
	{
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(createRequirementBtn));
		createRequirementBtn.click();
		waitForDialog();
		return new RequirementNewPage(driver);
	}

	public void doAnalyse(String time) {
		try
		{
			(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(analyseBtn));
			analyseBtn.click();
			Thread.sleep(2000);
		}
		catch(InterruptedException e)
		{
		}
	}

}
