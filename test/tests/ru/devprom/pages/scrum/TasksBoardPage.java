package ru.devprom.pages.scrum;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.ScrumTask;
import ru.devprom.pages.project.tasks.TaskViewPage;

public class TasksBoardPage extends ScrumPageBase{
	
	@FindBy(id = "append-task")
	protected WebElement addTask;
	
	public TasksBoardPage(WebDriver driver) {
		super(driver);
	}


	public TasksBoardPage addNewTaskScrum (ScrumTask task) {
		addTask.click();
		(new WebDriverWait(driver, waiting))
		.until(ExpectedConditions.visibilityOfElementLocated(
				By.id("pm_TaskCaption")));
		driver.findElement(By.id("pm_TaskCaption")).sendKeys(task.getName());
		new Select(driver.findElement(By.id("pm_TaskTaskType"))).selectByVisibleText(task.getType());
		if (!"".equals(task.getExecutor())) 
			new Select(driver.findElement(By.id("pm_TaskAssignee"))).selectByVisibleText(task.getExecutor());
		if (!"".equals(task.getPriority())) 
			new Select(driver.findElement(By.id("pm_TaskPriority"))).selectByVisibleText(task.getPriority());
		submitDialog(driver.findElement(By.id("pm_TaskSubmitBtn")));
		String uid = driver.findElement(By.xpath("//div[contains(@class,'bi-cap') and contains(.,'"+task.getName()+"')]/preceding-sibling::div//a")).getText();
		task.setId(uid.substring(1, uid.length()-1));
    	FILELOG.debug("Created Scrum Task: " + task.getId());
		return new TasksBoardPage(driver);
	}
        
    public void clickToContextMenuItem(String taskID, String menuItemName) {
        FILELOG.debug("clickToContextMenuItem Started");
        try {
			Thread.sleep(2000);
		} catch (InterruptedException e) {
		}
        String taskNumber = taskID.substring(2);
        WebElement onElement = driver.findElement(
                By.xpath("//a[contains(.,'[" +taskID+ "]')]/ancestor::div[contains(@class,'board_item')]"));
        Actions contextClick = new Actions(driver);
        mouseMove(onElement);
        contextClick.contextClick(onElement).build().perform();
        WebElement menuItem = driver.findElement(
                By.xpath("//div[contains(@id,'context-menu-"+taskNumber+"')]//a[text()='"+menuItemName+"']"));
        clickOnInvisibleElement(menuItem);
        try {
			Thread.sleep(1000);
		} catch (InterruptedException e) {
		}
    }

    public String getIdByName(String name) {
        FILELOG.debug("getIDTaskByName started");
        String IDtask;
        WebElement element = driver.findElement(By.xpath("//div[contains(.,'"+name+"')]/preceding-sibling::*/div/a"));
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(element));
        String uid = element.getText();
        IDtask = uid.substring(1, uid.length()-1);
        return IDtask;
    }
    
    public void clickToContextSubMenuItem(String taskID, String menuItemName, String subMenuItemName) {
        FILELOG.debug("clickToContextMenuItem Started");
        try {
			Thread.sleep(2000);
		} catch (InterruptedException e) {
		}
        String taskNumber = taskID.substring(2);
        WebElement menuItem = driver.findElement(By.xpath("//div[contains(@id,'context-menu-"+taskNumber+
                "')]//a[text()='"+menuItemName+"']"));
        WebElement subMenuItem = driver.findElement(By.xpath("//div[contains(@id,'context-menu-"+taskNumber+
                "')]//a[text()='"+subMenuItemName+"']"));
        WebElement onElement = driver.findElement(By.xpath("//a[contains(.,'[" +taskID+ "]')]/../.."));
        Actions contextClick = new Actions(driver);
        mouseMove(onElement);
        contextClick.contextClick(onElement).build().perform();
        clickOnInvisibleElement(menuItem);
        clickOnInvisibleElement(subMenuItem);
        try {
			Thread.sleep(3000);
		} catch (InterruptedException e) {
		}
    }

    public TaskViewPage openTask(String id) {
        driver.findElement(By.xpath("//a[contains(@class,'with-tooltip') and contains(.,'"+id+"')]")).click();
        return new TaskViewPage(driver);
    }
	
}
