package ru.devprom.pages.project.requests;

import java.util.ArrayList;
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
import ru.devprom.pages.project.testscenarios.TestScenarioTestingPage;

public class RequestViewPage extends SDLCPojectPageBase {

	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(text(),'Действия')]")
	protected WebElement actionsBtn;

	@FindBy(xpath = "//a[@id='modify']")
	protected WebElement editBtn;

	@FindBy(xpath = "//a[contains(@class,'embedded-add-button') and preceding-sibling::input[@value='activityrequest']]")
	protected WebElement addSpentTimeBtn;

	@FindBy(id = "state-label")
	protected WebElement stateLabel;

	@FindBy(xpath = "//table[@class='properties-table']/tbody/tr[@name='Type']/td")
	protected WebElement typeLabel;

	@FindBy(xpath = "//table[@class='properties-table']/tbody/tr[@name='Priority']/td")
	protected WebElement priorityLabel;

	@FindBy(xpath = "//table[@class='properties-table']/tbody/tr[@name='Estimation']/td")
	protected WebElement estimatesLabel;

	@FindBy(xpath = "//table[@class='properties-table']/tbody/tr[@name='Author']/td")
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

	@FindBy(xpath = "//ul//a[text()='Реализовать']")
	protected WebElement duplicateRequest;

	@FindBy(xpath = "//ul//a[text()='Перенести в проект']")
	protected WebElement moveRequestBtn;

	@FindBy(xpath = "//ul//a[text()='Удалить']")
	protected WebElement deleteBtn;

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
							By.xpath("//th[contains(text(),'Функция')]/following-sibling::td"))
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
						.xpath("//table[@class='properties-table']/tbody/tr/th[contains(text(),'"
								+ attributeName + "')]/following-sibling::td")));

		return driver
				.findElement(
						By.xpath("//table[@class='properties-table']/tbody/tr/th[contains(text(),'"
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

		saveBtn.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOfElementLocated(By.xpath("//div[@class='comment-text' and contains(.,'"+comment+"')]")));

		return new RequestViewPage(driver);
	}

	public RequestViewPage addCommentWithAttachment(String comment,
			String attachmentPath) {

		// turn off popup dialog
		String codeIE = "$.browser.msie = true; document.documentMode = 8;";
		((JavascriptExecutor) driver).executeScript(codeIE);

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

		driver.findElement(
				By.xpath("//div[@id='collapseComments']//input[@value='attachment']/following-sibling::a[contains(@class,'embedded-add-button')]"))
				.click();
		driver.findElement(
				By.xpath("//div[contains(@id,'pagesectioncomments')]//input[@type='file' and contains(@id,'File')]"))
				.sendKeys(attachmentPath);
		driver.findElement(
				By.xpath("//div[contains(@id,'pagesectioncomments')]//input[contains(@id,'saveEmbedded')]"))
				.click();

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
		boolean isOpens = driver.findElements(By.id("fancy_img")).size() > 0;
		if (isOpens)
			driver.findElement(By.id("fancy_img")).click();
		return isOpens;
	}

	public int readFirstInPageCommentNumber() {
		String commentId = driver.findElement(
				By.xpath(".//*[contains(@id,'comment') and child::table]"))
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
		autocompleteSelect(version);
		driver.findElement(By.id("pm_ChangeRequestSubmitBtn")).click();
		(new WebDriverWait(driver, waiting))
				.until(ExpectedConditions.presenceOfElementLocated(By
						.xpath("//span[@id='state-label' and contains(text(),'Выполнено')]")));
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
	   		autocompleteSelect(releaseNumber, true);
		}

   		driver.findElement(
				By.xpath("//button[@type='button']/span[text()='Сохранить']/.."))
				.click();
		(new WebDriverWait(driver, waiting))
				.until(ExpectedConditions.presenceOfElementLocated(By
						.xpath("//span[@id='state-label' and contains(text(),'В релизе')]")));
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
		submitDialog(driver.findElement(
				By.xpath("//button[@type='button']/span[text()='Сохранить']/..")));
		(new WebDriverWait(driver, waiting))
				.until(ExpectedConditions.presenceOfElementLocated(By
						.xpath("//span[@id='state-label' and contains(text(),'Добавлено')]")));
		return new RequestViewPage(driver);
	}

	public RequestPlanningPage planRequest() {
		actionsBtn.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOf(planRequest));
		planRequest.click();
		return new RequestPlanningPage(driver);
	}

	public TestScenarioTestingPage beginTest(String version, String environment) {
		clickOnInvisibleElement(beginTesting);
		waitForDialog();
		if (!"".equals(environment)) 	(new Select(driver.findElement(By.id("pm_TestEnvironment")))).selectByVisibleText(environment);
		driver.findElement(By.id("VersionText")).clear();
		driver.findElement(By.id("VersionText")).sendKeys(version);
		autocompleteSelect(version);
		submitDialog(driver.findElement(By.id("pm_TestSubmitBtn")));
		return new TestScenarioTestingPage(driver);
	}

	public String[] readTestResults() {
		List<WebElement> testScenarios = driver
				.findElements(By
						.xpath("//div[@id='collapseOne']//input[@value='requesttracetestexecution']/following-sibling::div[contains(@id,'embeddedItems')]//*[contains(@class,'title')]"));
		String[] results = new String[testScenarios.size()];
		for (int i = 0; i < results.length; i++) {
			results[i] = testScenarios.get(i).getText();
		}
		return results;
	}

	public Requirement[] readRequirements() {
		Requirement[] results;
		String s;
		String[] ss;
		List<WebElement> requirementsList = driver
				.findElements(By
						.xpath("//div[@id='collapseOne']//input[@value='requesttracerequirement']/following-sibling::div[contains(@id,'embeddedItems')]//div[@class='embeddedRowTitle']"));
		results = new Requirement[requirementsList.size()];
		for (int i = 0; i < results.length; i++) {
			s = requirementsList.get(i).findElement(By.className("title"))
					.getText().substring(1);
			ss = s.split("\\]");
			results[i] = new Requirement(ss[1].trim().split("\\(")[0].trim());
			results[i].setId(ss[0].trim());
		}
		return results;
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
			String[] parts = el.getText().replace(idText, "").replace(state, "").split(":");
			String type = "";
			String name = "";
			if ( parts.length > 1 ) {
				type = parts[0].trim();
				name = parts[1].trim();
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
				.xpath(".//*[contains(text(),'" + text + "')]"));
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
		((JavascriptExecutor) driver)
		.executeScript("document.evaluate(\"//div[@id='collapseTwo']\", document, null, 9, null).singleNodeValue.removeAttribute('class')");
		return driver.findElement(
				By.xpath("//div[contains(@id,'pm_ChangeRequestDescription')]")).getText().trim();
	}

	public List<Spent> readSpentRecords() 
	{
		String url = driver.getCurrentUrl();
		clickOnInvisibleElement(driver.findElement(By.id("activity-edit")));
		List<Spent> spentList = (new SpentTimePage(driver)).readSpentRecords();
		driver.navigate().to(url);
		return spentList;
	}

	public List<Commit> readCommitRecords() {
		List<WebElement> captions = driver
				.findElements(By
						.xpath("//div[@id='collapseOne']//input[@value='requesttracesourcecode']/following-sibling::div[contains(@id,'embeddedItems')]//*[contains(@class,'title')]"));
		List<Commit> commitArray = new ArrayList<Commit>();
		for (int i = 0; i < captions.size(); i++) {
			commitArray.add(new Commit(captions.get(i).getText()));
		}
		return commitArray;
	}

	public List<String> readAttachments() {
		List<String> result = new ArrayList<String>();
		List<WebElement> captions = driver
				.findElements(By
						.xpath("//div[@id='collapseOne']//input[@value='attachment']/following-sibling::div[contains(@id,'embeddedItems')]//*[contains(@class,'title')]/a"));
		for (int i = 0; i < captions.size(); i++) {
			result.add(captions.get(i).getText().trim());
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
		return driver.findElements(By.xpath("//div[contains(@id,'pm_ChangeRequestDescription')]")).size() < 1;
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
}
