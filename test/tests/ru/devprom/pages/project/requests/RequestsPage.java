package ru.devprom.pages.project.requests;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.util.ArrayList;
import java.util.List;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.xpath.XPathExpressionException;

import org.openqa.selenium.By;
import org.openqa.selenium.ElementNotVisibleException;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.StaleElementReferenceException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.xml.sax.SAXException;

import ru.devprom.helpers.FileOperations;
import ru.devprom.helpers.XLTableParser;
import ru.devprom.items.Project;
import ru.devprom.items.Request;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.testscenarios.StartTestingPage;
import ru.devprom.pages.project.testscenarios.TestScenarioTestingPage;

public class RequestsPage extends SDLCPojectPageBase {

	@FindBy(id = "new-issue")
	protected WebElement addRequestBtn;
        
        @FindBy(xpath = ".//*[@id='tablePlaceholder']//*[@id='to_delete_allrequestlist1']")
	protected WebElement checkAllChBx;
        
        @FindBy(xpath = ".//*[@id='bulk-actions']/a")
	protected WebElement moreBtn;
        
        @FindBy(xpath = ".//*[@id='bulk-actions']//*[contains(text(),'Начать тестирование')]")
	protected WebElement startTestingItem;

	@FindBy(id = "new-issue-bug")
	protected WebElement addBugBtn;

	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(.,'Действия')]")
	protected WebElement actionsBtn;
	
	@FindBy(xpath = "//ul//a[text()='Печать карточек']")
	protected WebElement printCardsBtn;
	
	@FindBy(xpath = "//ul//a[@id='import-excel']")
	protected WebElement importBtn;

	@FindBy(xpath = "//ul//a[@id='export-html']")
	protected WebElement printListBtn;

	@FindBy(xpath = "//ul//a[@id='export-excel']")
	protected WebElement excelBtn;

	@FindBy(xpath = "//div[contains(@class,'btn-group') and contains(@object-state,'submitted-')]//a[contains(.,'Выполнить')]")
	protected WebElement massCompleteBtn;
	
	@FindBy(xpath = "//div[contains(@class,'btn-group')]//a[@id='bulk-delete']")
	protected WebElement massDeleteBtn;
        
	public RequestsPage(WebDriver driver) {
		super(driver);
	}

	public RequestsPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public RequestNewPage clickNewCR() {
		clickOnInvisibleElement(addRequestBtn);
        waitForDialog();
		return new RequestNewPage(driver);
	}

	public RequestNewPage clickNewBug() {
		clickOnInvisibleElement(addBugBtn);
		waitForDialog();
		return new RequestNewPage(driver);
	}
	
	
	public RequestNewPage clickNewRequestUserType(String userType){
		clickOnInvisibleElement(driver.findElement(By.xpath("//a[contains(@id,'new-') and contains(.,'"+userType+"')]")));
		waitForDialog();
		return new RequestNewPage(driver);
	}
	
	public RequestPrintCardsPage clickPrintCards() {
		actionsBtn.click();
		try {
			printCardsBtn.click();
		} catch (ElementNotVisibleException e) {
			clickOnInvisibleElement(printCardsBtn);
		}
		(new WebDriverWait(driver, 30)).until(ExpectedConditions.presenceOfElementLocated(By.className("taskcard")));
		return new RequestPrintCardsPage(driver);
	}

	public RequestPrintListPage clickPrintList() {
		actionsBtn.click();
		try {
			printListBtn.click();
		} catch (ElementNotVisibleException e) {
			clickOnInvisibleElement(printListBtn);
		}
		(new WebDriverWait(driver, 30)).until(ExpectedConditions.presenceOfElementLocated(By.className("table-bordered")));
		return new RequestPrintListPage(driver);
	}

	public Request[] exportToExcel() throws XPathExpressionException,
			ParserConfigurationException, SAXException, IOException,
			InterruptedException {
		Request[] r = null;
		int attemptscount = 5;
		FileOperations.removeExisted("Бэклог.xls");
		actionsBtn.click();
		clickOnInvisibleElement(excelBtn);

		File excelTable = FileOperations.downloadFile("Бэклог.xls");
		while (true)
			if (attemptscount == 0)
				break;
			else {
				try {
					attemptscount--;
					r = XLTableParser.getRequests(excelTable, new String[]{""});
					break;
				} catch (FileNotFoundException e) {
					Thread.sleep(2000);
				}
			}
		return r;
	}

	public int getCount() {
		return driver.findElements(
				By.xpath("//tr[contains(@id,'requestlist1_row_')]")).size();
	}

	protected Request parseRequestRow( String rowId ) throws NoSuchElementException
	{
		WebElement row = driver.findElement(By.id(rowId));
		String id = row.findElement(By.id("uid")).getText();
		id = id.substring(1, id.length()-1);
		String caption = row.findElement(By.id("caption")).getText();
		String type = null;
		List<WebElement> typeList = row.findElements(By.id("type"));
		if ( typeList.size() > 0 ) {
			type = typeList.get(0).getText();
		}
		else {
			String[] splitted = caption.split(":");
			if ( splitted.length > 1 ) {
				String prefix = splitted[0]+":";
				caption = caption.replaceAll(prefix, "");
				type = splitted[0].trim();
			}
			caption = caption.trim();
		}
		String state = row.findElement(By.id("state")).getText();
		String priority = row.findElement(By.id("priority")).getText();
		priority = priority.trim();
		return new Request(id, caption, type, state, priority);
	}
	
	public Request findRequestById(String id) {
		return parseRequestRow(
				driver
					.findElement(By
						.xpath("//tr[contains(@id,'requestlist1_row_')]/td[@id='uid']/a[contains(.,'["
								+ id + "]')]/../..")).getAttribute("id"));
	}
	
	
	public Request findRequestByName(String name) {
		return parseRequestRow(
				driver
					.findElement(By
						.xpath("//tr[contains(@id,'requestlist1_row_')]/td[@id='caption' and contains(.,'"
								+ name + "')]/..")).getAttribute("id"));
	}
	

	public RequestViewPage clickToRequest(String id) {
		try {
			driver.findElement(
					By.xpath("//tr[contains(@id,'requestlist1_row_')]/td[@id='uid']/a[contains(.,'["
							+ id + "]')]")).click();
		} catch (NoSuchElementException e) {
			driver.findElement(
					By.xpath("//tr[contains(@id,'requestlist1_row_')]/td[@id='uid']/a/strike[contains(.,'"
							+ id + "')]/..")).click();
		}
		return new RequestViewPage(driver);
	}

	public Request[] readAllRequests()
	{
		ArrayList<Request> requestList = new ArrayList<Request>();
		for( WebElement row : driver.findElements(By.xpath("//tr[contains(@id,'requestlist1_row_')]")) ) {
			try {
				requestList.add(parseRequestRow(row.getAttribute("id")));
			}
			catch( NoSuchElementException e ) {
			}
			catch( StaleElementReferenceException e ) {
			}
		}
		return requestList.toArray(new Request[requestList.size()]);
	}

	public String getRequestProperty(String requestID, String propertyName) {
		WebElement request;
		try {
			request = driver
					.findElement(By
							.xpath("//tr[contains(@id,'requestlist1_row_')]/td[@id='uid']/a[contains(.,'"
									+ requestID + "')]/../.."));
		}
		catch (NoSuchElementException e) {
			request = driver
					.findElement(By
							.xpath("//tr[contains(@id,'requestlist1_row_')]/td[@id='uid']/a/strike[contains(.,'"
									+ requestID + "')]/../../.."));
		}
		switch (propertyName) {
		case "caption": {
			String caption = request.findElement(By.id("caption")).getText();
			String[] splitted = caption.split(":");
			String prefix = splitted[0]+":";
			return caption.replaceAll(prefix, "").trim();
		}
		case "type": {
			String caption = request.findElement(By.id("caption")).getText();
			String[] splitted = caption.split(":");
			return splitted[0].trim();
		}
		case "state":
			return request.findElement(By.id("state")).getText();
		case "priority":
			return request.findElement(By.id("priority")).getText().trim();
		case "linked issues":
			Matcher matcher = Pattern.compile("(I-[0-9]+)").matcher(request.findElement(By.id("links")).getText());
			return matcher.find() ? matcher.group(1) : "";
		default:
			return "error property";
		}
	}

	public Boolean isLinkedIssueCompleted(String requestID, String linkedID){
		return driver
		.findElements(By
				.xpath("//tr[contains(@id,'requestlist1_row_')]/td[@id='uid']/a[contains(.,'"
						+ requestID + "')]/../../td[@id='links']//strike[contains(.,'"+linkedID+"')]")).size()>0;
	}
	
	
	public void checkRequest(String id) {
		driver.findElement(
				By.xpath("//tr[contains(@id,'requestlist1_row_')]/td[@id='uid']/a[contains(.,'"
						+ id
						+ "')]/../preceding-sibling::td/input[contains(@class,'checkbox')]"))
				.click();
	}

	public boolean isRequestPresent(String id) {
         return	!driver.findElements(By.xpath("//tr[contains(@id,'requestlist1_row_')]/td[@id='uid']/a[contains(.,'"
							+ id + "')]")).isEmpty();
	}
	
	public boolean isRequestPresentByName(String name) {
		   return	!driver.findElements(By.xpath("//tr[contains(@id,'requestlist1_row_')]/td[@id='caption' and contains(.,'"
					+ name + "')]")).isEmpty();
	}

	public RequestsPage deleteAll(){
		if (driver.findElements(By.xpath("//tr[contains(@id,'requestlist1_row_')]")).size()>0){
			driver.findElement(By.xpath("//input[contains(@id,'to_delete_allrequestlist')]")).click();
			(new WebDriverWait(driver, 3)).until(ExpectedConditions.visibilityOf(massDeleteBtn));
			massDeleteBtn.click();
			waitForDialog();
			submitDialog(driver.findElement(By.id("SubmitBtn")));
		}
		return new RequestsPage(driver);
	}

	public RequestsImportPage gotoImportRequests() {
		actionsBtn.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(importBtn));
		importBtn.click();
		return  new RequestsImportPage(driver);
	}
	
	
	public RequestsPage massChangeType(String type){
		WebElement changeTypeBtn = driver.findElement(By.xpath("//div[@id='bulk-modify-actions']//a[text()='Тип']"));
		clickOnInvisibleElement(changeTypeBtn);
		waitForDialog();
		(new Select(driver.findElement(By.id("Type")))).selectByVisibleText(type);
		submitDialog(driver.findElement(By.id("SubmitBtn")));
		driver.navigate().refresh();
		return new RequestsPage(driver);
	}
	

	public RequestsPage massComplete(String version, String comment){
		clickOnInvisibleElement(massCompleteBtn);
		waitForDialog();
		driver.findElement(By.id("ClosedInVersionText")).sendKeys(version);
		CKEditor we = new CKEditor(driver);
		we.typeText(comment);		
		submitDialog(driver.findElement(By.id("SubmitBtn")));
		driver.navigate().refresh();
		return new RequestsPage(driver);
	}
	
	public RequestsPage massChangeProject(String projectName){
		WebElement changeProjectBtn = driver.findElement(By.xpath("//div[@id='bulk-modify-actions']//a[text()='Проект']"));
		clickOnInvisibleElement(changeProjectBtn);
		waitForDialog();
		driver.findElement(By.id("ProjectText")).sendKeys(projectName);
		autocompleteSelect(projectName);
		submitDialog(driver.findElement(By.id("SubmitBtn")));
		driver.navigate().refresh();
		return new RequestsPage(driver);
	}
	
	public RequestsPage massDuplicateInProject(String projectName){
		WebElement duplicateBtn = driver.findElement(By.xpath("//div[@id='bulk-actions']//a[text()='Создать реализацию']"));
		clickOnInvisibleElement(duplicateBtn);
		waitForDialog();
		driver.findElement(By.id("ProjectText")).sendKeys(projectName);
		autocompleteSelect(projectName);
		submitDialog(driver.findElement(By.id("SubmitBtn")));
		driver.navigate().refresh();
		return new RequestsPage(driver);
	}

    public void checkAll() {
        clickOnInvisibleElement(checkAllChBx);
    }

    public StartTestingPage moreStartTesting() {
        clickOnInvisibleElement(moreBtn);
        clickOnInvisibleElement(startTestingItem);
        return new StartTestingPage(driver);
    }
	
}
