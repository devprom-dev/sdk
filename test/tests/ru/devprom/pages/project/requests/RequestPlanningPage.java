package ru.devprom.pages.project.requests;

import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.items.RTask;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class RequestPlanningPage extends SDLCPojectPageBase {

	@FindBy(id = "pm_ChangeRequestSubmitBtn")
	protected WebElement submitBtn;
        
        @FindBy(id = "pm_ChangeRequestEstimation")
	protected WebElement estimationField;


	public RequestPlanningPage(WebDriver driver) {
		super(driver);
	}

	public RequestPlanningPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public void hideTaskbox() {
		waitForTextPresent("taskboxClose");
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By.id("pm_ChangeRequestCaption")));
		((JavascriptExecutor) driver).executeScript("taskboxClose($('.taskbox:visible').last().attr('form-id'))");
	}

	public void addTask(RTask task){
		driver.findElement(By.xpath("//div[@id='fieldRowTasks']//a[contains(.,'Еще задачу')]")).click();
		fillTask(getTasksNumber(), task.getName(), task.getType(), task.getExecutor(), task.getEstimation());
	}
	
	public int getTasksNumber() {
		return driver.findElements(By.xpath("//div[@id='fieldRowTasks']/div/div[contains(@id,'tb') and contains(@style,'block')]")).size();
	}
	
	public void fillTask(int taskBoxNumber, String taskName, String type,
			String executorName, double estimation) {

		String xPath = ".//div[@id='fieldRowTasks']//div[contains(@class,'taskbox') and contains(@style,'block')]";
		List<WebElement> caption = driver.findElements(
				By.xpath(xPath+"//input[contains(@id,'_Caption')]"));
		List<WebElement> taskType = driver.findElements(
				By.xpath(xPath+"//select[contains(@id,'_TaskType')]"));
		List<WebElement> executorSelector = driver.findElements(
				By.xpath(xPath+"//select[contains(@id,'_Assignee')]"));
		List<WebElement> planned = driver.findElements(
				By.xpath(xPath+"//*[@class='row-fluid formvalueholder formvalue-short']//input[contains(@id,'_Planned')]"));
		
		caption.get(taskBoxNumber-1).sendKeys(taskName);
		(new Select(taskType.get(taskBoxNumber-1))).selectByVisibleText(type);
		(new Select(executorSelector.get(taskBoxNumber-1))).selectByVisibleText(executorName);
		planned.get(taskBoxNumber-1).sendKeys(String.valueOf(estimation));

	}

	public RequestViewPage savePlanned() {
		submitDialog(submitBtn);
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By.xpath("//span[@id='state-label' and contains(.,'Запланировано')]")));
		return new RequestViewPage(driver);
	}
	

	public RequestsBoardPage savePlannedOnBoard() {
		submitDialog(submitBtn);
		return new RequestsBoardPage(driver);
	}

    public void addContent(String newText) {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//div[contains(@class,'cke_')]")));
    	CKEditor editor = (new CKEditor(driver)); 
    	editor.changeText(editor.getText() + "\n" + newText);
    }

    public void addRate(String sp) {
         (new Select(estimationField)).selectByVisibleText(sp);
    }

}
