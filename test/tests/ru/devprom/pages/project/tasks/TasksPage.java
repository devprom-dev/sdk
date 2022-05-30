package ru.devprom.pages.project.tasks;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.util.List;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.xpath.XPathExpressionException;

import org.openqa.selenium.By;
import org.openqa.selenium.ElementNotVisibleException;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.Keys;
import org.openqa.selenium.NoSuchElementException;
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
import ru.devprom.items.RTask;
import ru.devprom.items.Spent;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.SpentTimePage;

public class TasksPage extends SDLCPojectPageBase {

	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(.,'Действия')]")
	protected WebElement actionsBtn;
	
	@FindBy(xpath = "//a[@id='append-task']")
	protected WebElement newTaskBtn;

	@FindBy(id="filter-settings")
	protected WebElement filterBtn;
	
	@FindBy(xpath = "//a[text()='Печать карточек']")
	protected WebElement printCardsBtn;

	@FindBy(xpath = "//a[@id='export-html']")
	protected WebElement printListBtn;

	@FindBy(xpath = "//a[@id='export-excel']")
	protected WebElement excelBtn;

	@FindBy(xpath = "//div[@id='bulk-modify-actions']/a")
	protected WebElement massChangeBtn;
	
	@FindBy(xpath = "//div[contains(@class,'btn-group') and not (@style='display: none;')]/a[contains(.,'Выполнить')]")
	protected WebElement massCompleteBtn;
	
	@FindBy(xpath = "//div[contains(@class,'btn-group')]//a[@id='bulk-delete']")
	protected WebElement massDeleteBtn;
	
	
	
	public TasksPage(WebDriver driver) {
		super(driver);
	}

	public TasksPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public TaskViewPage clickToTask(String id) {
		try {
		driver.findElement(
				By.xpath("//tr[contains(@id,'tasklist1_row_')]/td[@id='uid']/a[contains(.,'["+ id + "]')]")).click();
		}
		catch (NoSuchElementException e) {
			driver.findElement(
					By.xpath("//tr[contains(@id,'tasklist1_row_')]/td[@id='uid']//strike[contains(.,'" + id + "')]")).click();
		}
		return new TaskViewPage(driver);
	}
	
	public TaskViewPage clickToTaskByName(String name) {
		driver.findElement(
				By.xpath("//td[@id='caption' and text()='"+name+"']/preceding-sibling::td[@id='uid']/a")).click();
		return new TaskViewPage(driver);
	}

	
	public TaskNewPage createNewTask(){
        driver.findElement(By.id("append-task")).click();
        waitForDialog();
        return new TaskNewPage(driver);
	}
	
	
	public String getTaskProperty(String id, String propertyName) {
		WebElement task;
		try {
			task = driver
					.findElement(By
							.xpath("//tr[contains(@id,'tasklist1_row_')]/td[@id='uid']/a[contains(.,'"
									+ id + "')]/../.."));
		}
		// strike element (completed request)
		catch (NoSuchElementException e) {
			task = driver
					.findElement(By
							.xpath("//tr[contains(@id,'tasklist1_row_')]/td[@id='uid']/a/strike[contains(.,'"
									+ id + "')]/../../.."));
		}
		switch (propertyName) {
		case "name": {
			return task.findElement(By.id("caption")).getText();
		}
		case "type": {
			return task.findElement(By.id("tasktype")).getText();
		}
		case "state":
			return task.findElement(By.id("state")).getText();
		case "priority":
			return task.findElement(By.id("priority")).getText().trim();
		case "testscenario":
			Pattern pattern = Pattern.compile("\\[(.*?)\\]");
			Matcher matcher = pattern.matcher(task.findElement(By.id("testscenario")).getText());
			return matcher.find() ? matcher.group(1) : "";	
		default:
			return "error property";
		}
	}
	
	public List<Spent> readSpentRecords(String taskID)
	{
		String url = driver.getCurrentUrl();
		clickOnInvisibleElement(
				driver.findElement(
						By.xpath("//td[@id='uid']//a[contains(.,'"+taskID+"')]/../following-sibling::td[@id='spent']//a[@id='activity-edit']")
						)
				);
		List<Spent> spentList = (new SpentTimePage(driver)).readSpentRecords();
		driver.navigate().to(url);
		return spentList;
	}

	public RTask[] readAllTasks() {
		RTask[] tasksList = new RTask[driver.findElements(By.id("uid"))
				.size()];
		for (int i = 0; i < tasksList.length; i++) {
			WebElement row = driver.findElement(By.xpath("//tr[contains(@id,'_row_" + (i + 1) + "')]"));
			String id = row.findElement(By.id("uid")).getText();
			id = id.substring(1, id.length() - 1);
			String name = row.findElement(By.id("caption")).getText();
			String state = row.findElement(By.id("state")).getText();
			String type = row.findElement(By.id("tasktype")).getText();
			String parts[] = row.findElement(By.xpath(".//td[@id='priority']//a")).getText().trim().split(" ");
			String priority = parts[parts.length - 1];
			tasksList[i] = new RTask(id, name, type, priority, state);
		}
		return tasksList;
	}
	
	

	public TaskPrintCardsPage clickPrintCards() {
		actionsBtn.click();
		printCardsBtn.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.className("taskcard")));
		return new TaskPrintCardsPage(driver);

	}

	public TasksPrintListPage clickPrintList() {
		actionsBtn.click();
		clickOnInvisibleElement(printListBtn);
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.className("table-bordered")));
		return new TasksPrintListPage(driver);

	}
	
   public RTask[] exportToExcel() throws XPathExpressionException,
		ParserConfigurationException, SAXException, IOException,
		InterruptedException {
		   RTask[] t = null;
		int attemptscount = 5;
		FileOperations.removeExisted("Текущие задачи.xls");
		actionsBtn.click();
		clickOnInvisibleElement(excelBtn);
	
		File excelTable = FileOperations.downloadFile("Текущие задачи.xls");
		while (true)
			if (attemptscount == 0)
				break;
			else {
				try {
					attemptscount--;
					t = XLTableParser.getTasks(excelTable, new String[]{""});
					break;
				} catch (FileNotFoundException e) {
					Thread.sleep(2000);
				}
			}
		return t;
	}

	public void checkTask(String id) {
		driver.findElement(
				By.xpath("//tr[contains(@id,'tasklist1_row_')]/td[@id='uid']/a[contains(.,'"
						+ id + "')]/../preceding-sibling::td/input[contains(@class,'checkbox')]"))
				.click();		
	}

	public TasksPage addSpentRecord(Spent spent, String taskId){
		WebElement spentRow = driver.findElement(By.xpath("//a[text()='["+taskId+"]']/../following-sibling::td[@id='spent']"));
		spentRow.findElement(By.xpath(".//a[contains(@class,'embedded-add-button')]")).click();
		spentRow.findElement(By.xpath(".//input[contains(@id,'_Capacity')]")).sendKeys(String.valueOf(spent.hours));
		WebElement reportDate = spentRow.findElement(By.xpath(".//input[contains(@id,'_ReportDate')]"));
		reportDate.clear();
		reportDate.sendKeys(String.valueOf(spent.date));
		reportDate.sendKeys(Keys.TAB);
		spentRow.findElement(By.xpath(".//textarea[contains(@id,'_Description')]")).sendKeys(spent.description);
		spentRow.findElement(By.xpath(".//input[contains(@id,'saveEmbedded')]")).click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOfElementLocated(By.xpath("//a[text()='["+taskId+"]']/../following-sibling::td[@id='spent']//a[contains(@class,'embedded-add-button')]")));
		return new TasksPage(driver);
	}

	public boolean isTaskPresent(String taskId){
		return (!driver.findElements(By.xpath("//td[@id='uid']/a[text()='["+taskId+"]']")).isEmpty() ||  !driver.findElements(By.xpath("//td[@id='uid']/a/strike[contains(.,'"	+ taskId + "')]")).isEmpty());
	}
	
	public TasksPage massChangeType(String type) {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(massChangeBtn));
		massChangeBtn.click();
		WebElement changeTypeBtn = driver.findElement(By.xpath("//div[@id='bulk-modify-actions']//a[text()='Тип']"));
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(changeTypeBtn));
		changeTypeBtn.click();
		waitForDialog();
		(new Select(driver.findElement(By.xpath("//form//select[@name='TaskType']")))).selectByVisibleText(type);
		submitDialog(driver.findElement(By.id("SubmitBtn")));		
		return new TasksPage(driver);
	}
}