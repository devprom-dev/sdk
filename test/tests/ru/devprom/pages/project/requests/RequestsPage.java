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

	@FindBy(id = "append-issue")
	protected WebElement addRequestBtn;
        
        @FindBy(xpath = ".//*[@id='tablePlaceholder']//*[@id='to_delete_allrequestlist1']")
	protected WebElement checkAllChBx;
        
        @FindBy(xpath = ".//*[@id='bulk-actions']/a")
	protected WebElement moreBtn;
        
        @FindBy(xpath = ".//*[@id='bulk-actions']//*[contains(text(),'Начать тестирование')]")
	protected WebElement startTestingItem;

	@FindBy(id = "append-issue-bug")
	protected WebElement addBugBtn;

	@FindBy(id = "append-issue-enhancement")
	protected WebElement addEnhancementBtn;

	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(text(),'Действия')]")
	protected WebElement actionsBtn;
	
	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(text(),'Добавить')]")
	protected WebElement addBtn;

	@FindBy(id="filter-settings")
	protected WebElement filterBtn;
	
	@FindBy(xpath = "//li[@uid='append-issue-enhancement']/a")
	protected WebElement newCRBtn;

	@FindBy(xpath = "//li[@uid='append-issue-bug']/a")
	protected WebElement newBugBtn;

	@FindBy(xpath = "//ul//a[text()='Печать карточек']")
	protected WebElement printCardsBtn;
	
	@FindBy(xpath = "//ul//a[@id='import-excel']")
	protected WebElement importBtn;

	@FindBy(xpath = "//ul//a[@id='export-html']")
	protected WebElement printListBtn;

	@FindBy(xpath = "//ul//a[@id='export-excel']")
	protected WebElement excelBtn;

	@FindBy(xpath = "//li[@class='dropdown-submenu']/a[text()='Фильтры']")
	protected WebElement filtersSubmenu;
	
	@FindBy(xpath = "//div[contains(@class,'btn-group')]//a[contains(text(),'Выполнить')]")
	protected WebElement massCompleteBtn;
	
	@FindBy(xpath = "//div[contains(@class,'btn-group')]//a[contains(text(),'Включить в релиз')]")
	protected WebElement massIncludeInReleaseBtn;
	
	@FindBy(xpath = "//div[contains(@class,'btn-group')]//a[contains(text(),'Удалить')]")
	protected WebElement massDeleteBtn;
        
        //поле версия на форме начать тестирование
        @FindBy(xpath = ".//*[@id='VersionText']")
	protected WebElement versionField;
        
        //поле окружение на форме начать тестирование
        @FindBy(xpath = ".//*[@id='EnvironmentText']")
	protected WebElement envirenmentField;
        
        //кнопка сохранить на форме начать тестирование
        @FindBy(xpath = ".//*[@id='pm_TestSubmitBtn']")
	protected WebElement saveTestingBtn;
	

	public RequestsPage(WebDriver driver) {
		super(driver);
	}

	public RequestsPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public RequestNewPage clickNewCR() {
        addRequestBtn.click();		
        waitForDialog();
		return new RequestNewPage(driver);
	}

	public RequestNewPage clickNewBug() {
        addBugBtn.click();		
		waitForDialog();
		return new RequestNewPage(driver);
	}
	
	
	public RequestNewPage clickNewRequestUserType(String userType){
		try {
			addBtn.click();
		}
		catch(NoSuchElementException e) {
		}
		WebElement reqBtn = driver.findElement(By.xpath("//a[contains(@id,'append') and contains(.,'"+userType+"')]"));
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(reqBtn));
		reqBtn.click();
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

	public RequestsPage showAll() {
		driver.navigate().to(driver.getCurrentUrl()+"&state=all");
		return new RequestsPage(driver);
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
						.xpath("//tr[contains(@id,'requestlist1_row_')]/td[@id='uid']/a[contains(text(),'["
								+ id + "]')]/../..")).getAttribute("id"));
	}
	
	
	public Request findRequestByName(String name) {
		return parseRequestRow(
				driver
					.findElement(By
						.xpath("//tr[contains(@id,'requestlist1_row_')]/td[@id='caption' and contains(text(),'"
								+ name + "')]/..")).getAttribute("id"));
	}
	

	public RequestViewPage clickToRequest(String id) {
		try {
			driver.findElement(
					By.xpath("//tr[contains(@id,'requestlist1_row_')]/td[@id='uid']/a[contains(text(),'["
							+ id + "]')]")).click();
		} catch (NoSuchElementException e) {
			driver.findElement(
					By.xpath("//tr[contains(@id,'requestlist1_row_')]/td[@id='uid']/a/strike[contains(text(),'"
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
							.xpath("//tr[contains(@id,'requestlist1_row_')]/td[@id='uid']/a[contains(text(),'"
									+ requestID + "')]/../.."));
		}
		catch (NoSuchElementException e) {
			request = driver
					.findElement(By
							.xpath("//tr[contains(@id,'requestlist1_row_')]/td[@id='uid']/a/strike[contains(text(),'"
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
				.xpath("//tr[contains(@id,'requestlist1_row_')]/td[@id='uid']/a[contains(text(),'"
						+ requestID + "')]/../../td[@id='links']//strike[contains(.,'"+linkedID+"')]")).size()>0;
	}
	
	
	public void checkRequest(String id) {
		driver.findElement(
				By.xpath("//tr[contains(@id,'requestlist1_row_')]/td[@id='uid']/a[contains(text(),'"
						+ id
						+ "')]/../preceding-sibling::td/input[@class='checkbox']"))
				.click();
	}

	public boolean isRequestPresent(String id) {
         return	!driver.findElements(By.xpath("//tr[contains(@id,'requestlist1_row_')]/td[@id='uid']/a[contains(text(),'"
							+ id + "')]")).isEmpty();
	}
	
	public boolean isRequestPresentByName(String name) {
		   return	!driver.findElements(By.xpath("//tr[contains(@id,'requestlist1_row_')]/td[@id='caption' and contains(text(),'"
					+ name + "')]")).isEmpty();
	}

	public void addFilter(String filtername) {
		String code = "filterLocation.setup( '" + filtername + "=all', 1 );";
		((JavascriptExecutor) driver).executeScript(code);
		(new WebDriverWait(driver, waiting)).until(
				ExpectedConditions.presenceOfElementLocated(
						By.xpath("//div[contains(@class,'filter')]//*[@uid='"+filtername+"']")
						)
				);
	}

	public void removeFilter(String filtername) {
		WebElement element = driver.findElement(By.xpath("//div[contains(@class,'filter')]//*[@uid='"+filtername+"']"));
		String code = "filterLocation.setup( '" + filtername + "=hide', 1 );";
		((JavascriptExecutor) driver).executeScript(code);
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.stalenessOf(element));
	}
	/**
	 * 
	 * @param filtername - английское имя фильтра (например state)
	 * @param value - видимое значение (например "В релизе")
	 * @return
	 */
	public RequestsPage selectFilterValue (String filtername, String value){
		driver.findElement(By.xpath("//a[@data-toggle='dropdown' and @uid='"+filtername+"']")).click();
		driver.findElement(By.xpath("//a[@data-toggle='dropdown' and @uid='"+filtername+"']/following-sibling::ul//a[text()='"+value+"']")).click();
		try {
			Thread.sleep(200);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		driver.findElement(By.xpath("//a[@data-toggle='dropdown' and @uid='"+filtername+"']")).click();
		if (!value.equals("Все"))
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//a[@data-toggle='dropdown' and @uid='"+filtername+"' and contains(@class,'btn-info')]")));
		else
			(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//a[@data-toggle='dropdown' and @uid='"+filtername+"' and not (contains(@class,'btn-info'))]")));
		return new RequestsPage(driver);
	}
	

	public RequestsPage turnOnFilter(String value,
			String russianFilterName) throws InterruptedException {

		driver.findElement(
				By.xpath("//a[@data-toggle='dropdown' and contains(text(),'"
						+ russianFilterName + "')]")).click();

		driver.findElement(
				By.xpath("//a[@data-toggle='dropdown' and contains(text(),'"
						+ russianFilterName + "')]/following-sibling::ul/li/a[text()='" + value + "']")).click();
	Thread.sleep(600);
		driver.findElement(
				By.xpath("//a[@data-toggle='dropdown' and contains(text(),'"
						+ russianFilterName + "')]")).click();
		
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//a[@data-toggle='dropdown' and contains(@class,'btn-info') and contains(text(),'"
						+ russianFilterName + "')]")));
		
		return new RequestsPage(driver);
	}

	public RequestsPage turnOffFilter(String filtername, String value,
			String russianName) throws InterruptedException {

		driver.findElement(
				By.xpath("//a[@data-toggle='dropdown' and contains(text(),'"
						+ russianName + "')]")).click();
		
		
		driver.findElement(
				By.xpath("//a[@data-toggle='dropdown' and contains(text(),'"
						+ russianName + "')]/following-sibling::ul/li/a[text()='" + value + "']")).click();
		Thread.sleep(600);
		driver.findElement(
				By.xpath("//a[@data-toggle='dropdown' and contains(text(),'"
						+ russianName + "')]")).click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//a[@data-toggle='dropdown' and @class='btn btn-small dropdown-toggle' and contains(text(),'"
				+ russianName + "')]")));
		
		return new RequestsPage(driver);
	}

	public String[] readHistoryChangesForRequest(String requestId){
		List<WebElement> historyRecords = driver.findElements(By.xpath("//td[@id='uid' and child::a/text()='["+requestId+"]']/following-sibling::td[@id='history']/div"));
		if (historyRecords.size()<1) return null;
		String[] result = new String[1];
		result[0]=historyRecords.get(0).getText();
		return result;
	}
	

	public void addColumn(String columnname) {
		String code = "filterLocation.showColumn('" + columnname + "', 0)";
		filterBtn.click();
		((JavascriptExecutor) driver).executeScript(code);
		filterBtn.click();
		try {
			Thread.sleep(3000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		driver.navigate().refresh();
	}

	public void removeColumn(String columnname) {
		String code = "filterLocation.hideColumn('" + columnname + "', 0)";
		filterBtn.click();
		((JavascriptExecutor) driver).executeScript(code);
		filterBtn.click();
		try {
			Thread.sleep(3000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		driver.navigate().refresh();
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
		autocompleteSelect(version);
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
		WebElement duplicateBtn = driver.findElement(By.xpath("//div[@id='bulk-actions']//a[text()='Реализовать в проекте']"));
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
