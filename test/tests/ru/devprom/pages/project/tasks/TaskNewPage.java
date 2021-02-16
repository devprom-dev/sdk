package ru.devprom.pages.project.tasks;

import java.io.File;
import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.TimeUnit;

import org.openqa.selenium.By;
import org.openqa.selenium.Keys;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.items.RTask;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class TaskNewPage extends SDLCPojectPageBase {

	@FindBy(id = "pm_TaskCaption")
	protected WebElement captionEdit;

	@FindBy(id = "ReleaseText")
	protected WebElement iterationSelect2;
	
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

	@FindBy(id = "pm_TaskSubmitBtn")
	protected WebElement saveBtn;
	
	@FindBy(id = "pm_TaskCancelBtn")
	protected WebElement cancelBtn;

	
	public TaskNewPage(WebDriver driver) {
		super(driver);
	}

	public TaskNewPage(WebDriver driver, Project project) {
		super(driver, project);
	}
	
	
	public boolean isFieldVisibleByLabel(String fieldName){
		WebElement e = driver.findElement(By.xpath("//label[text()='"+fieldName+"']"));
		return e == null ? false : e.isDisplayed();
	}
	
	public boolean isFieldNotVisibleByLabel(String fieldName){
		driver.manage().timeouts()
			.implicitlyWait(1, TimeUnit.MILLISECONDS);
		boolean result = driver.findElements(By.xpath("//label[text()='"+fieldName+"']")).isEmpty();
		driver.manage().timeouts()
			.implicitlyWait(timeoutValue, TimeUnit.SECONDS);
		return result;
	}

	public TasksPage cancel(){
		submitDialog(cancelBtn);
		return new TasksPage(driver);
	}
	
    public TasksPage createTask(RTask task)
    {
    	clickMainTab();
    	(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(captionEdit));
    	addName(task.getName());
		if ( !task.getExecutor().equals("")) selectExecutor(task.getExecutor());
		addEstimation(task.getEstimation());
		
		if (!task.getType().equals("")) selectType(task.getType());
		if (!task.getPriority().equals("")) selectPriority(task.getPriority());
	    if (!task.getIteration().equals("")) selectIteration(task.getIteration());
		
        submitDialog(saveBtn);
    	//read ID
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//td[@id='caption' and contains(.,'"+task.getName()+"')]")));
    	String uid =driver.findElement(By.xpath("//td[@id='caption' and contains(.,'"+task.getName()+"')]/preceding-sibling::td[@id='uid']")).getText();
    	task.setId(uid.substring(1, uid.length()-1));
		return new TasksPage(driver);
	}
    
    public void addName(String name)
    {
    	clickMainTab();
    	captionEdit.clear();
    	captionEdit.sendKeys(name);
    }
    
    public void selectType(String type)
    {
    	clickMainTab();
    	if (!type.equals("Разработка")) 
    		(new Select(typeSelect)).selectByVisibleText(type);
    }
    
    public void selectPriority(String priority)
    {
    	clickMainTab();
    	try {
			(new Select(prioritySelect)).selectByVisibleText(priority);
		} catch (NoSuchElementException e) {
			FILELOG.error("There is no priority: " + priority);
		}
    }
    
    public void addEstimation(double estimation)
    {
    	clickMainTab();
    	estimationEdit.clear();
    	estimationEdit.sendKeys(String.valueOf(estimation));
    }
    
    public void selectExecutor(String executor)
    {
    	clickMainTab();
    	try {
			(new Select(executorList)).selectByVisibleText(executor);
		} catch (NoSuchElementException e) {
			FILELOG.error("There is no user: " + executor);
		}
    }
   
    
    public void createEmbeddedTask(RTask task)
    {
    	clickMainTab();
    	(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(captionEdit));
    	addName(task.getName());
		if (!"".equals(task.getExecutor())) selectExecutor(task.getExecutor());
		addEstimation(task.getEstimation());
		
		if (task.getType()!=null && !task.getType().equals("")) selectType(task.getType());
		if (task.getPriority()!=null && !task.getPriority().equals("")) selectPriority(task.getPriority());
		
		submitDialog(saveBtn);
	}
    
    public List<String> getExecutorCandidatesList()
    {
    	clickMainTab();
    	List<String> list = new ArrayList<String>();
    	List<WebElement> ellist = driver.findElements(By.xpath("//select[@id='pm_TaskAssignee']/option[not (@disabled)]"));
    	for (WebElement el:ellist){
    	  if (!"".equals(el.getText())) list.add(el.getText());
    	}
    	return list;
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
	
	public void clickAdditionalTab()
	{
		clickTab("additional");
	}

	public void clickTraceTab()
	{
		clickTab("trace");
	}

	public void clickIssueTab()
	{
		clickTab("source-issue");
	}

	public void selectIteration(String iteration)
	{
		clickDeadlinesTab();
		iterationSelect2.clear();
		iterationSelect2.sendKeys(iteration);
		autocompleteSelect(iteration);
	}
}
