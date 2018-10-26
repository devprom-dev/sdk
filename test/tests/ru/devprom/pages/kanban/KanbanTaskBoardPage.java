package ru.devprom.pages.kanban;

import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.helpers.WebDriverPointerRobot;
import ru.devprom.items.Project;
import ru.devprom.pages.project.requirements.RequirementNewPage;

public class KanbanTaskBoardPage extends KanbanPageBase {

	@FindBy(xpath = "//a[@id='navbar-quick-create']")
	protected WebElement addBtn;
	
	@FindBy(xpath = "//a[contains(.,'Задача')]")
	protected WebElement newTaskBtn;
        
        //фильтер Статус
        @FindBy(xpath = "//a[contains(.,'Статус: Не завершено')]")//"//div[@class='filter hidden-print']/div[2]/a")//"//a[contains(.,'Статус')]")
	protected WebElement statusFilterBtn;
        
         //добавить затраченное время на форма перехода
        @FindBy(xpath = ".//*[@id='pm_TaskFact']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addTimeBtn;        
        
        //добавить затраченное время на форма перехода для требования
        @FindBy(xpath = "//span[@name='pm_ChangeRequestFact']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addTimeReqBtn;  
        
        //поле добавления времени на форме перехода для требования
        @FindBy(xpath = "//span[@name='pm_ChangeRequestFact']//input[contains(@name,'_Capacity')]")
	protected WebElement addTimeReqField;  
        
        //поле добавления времени на форме перехода
        @FindBy(xpath = "//span[@name='pm_TaskFact']//input[contains(@name,'_Capacity')]")
	protected WebElement addTimeField;   
        
        //кнопка добавить время после ввода на форме  перехода
        @FindBy(xpath="//span[@name='pm_TaskFact']//input[contains(@id,'saveEmbedded')]")
	protected WebElement saveAddedTime;
        
        //кнопка добавить время после ввода на форме  перехода
        @FindBy(xpath="//span[@name='pm_ChangeRequestFact']//input[contains(@id,'saveEmbedded')]")
	protected WebElement saveAddedTimeReq;
        
        //кнопка сохранить время после ввода на форме  перехода
        @FindBy(xpath=".//*[@id='pm_TaskSubmitBtn']")
	protected WebElement submitAddedTime;
       
        //кнопка сохранить время после ввода на форме  перехода для требования
        @FindBy(xpath=".//*[@id='pm_ChangeRequestSubmitBtn']")
	protected WebElement submitAddedTimeReq;
	
	public KanbanTaskBoardPage(WebDriver driver) {
		super(driver);
	}

	public KanbanTaskBoardPage(WebDriver driver, Project project) {
		super(driver, project);
	}
	
	public KanbanTaskViewPage clickToTask(String id) {
		try {
		driver.findElement(
				By.xpath("//table[contains(@id,'requestboard')]//a[contains(.,'["+ id + "]')]")).click();
		}
		catch (NoSuchElementException e) {
			driver.findElement(
					By.xpath("//table[contains(@id,'requestboard')]//strike[contains(.,'" + id + "')]")).click();
		}
		return new KanbanTaskViewPage(driver);
	}
	
	public boolean isTaskPresent(String numericId) {
		return !driver.findElements(By.xpath("//div[@object='"+numericId+"']")).isEmpty();
	}

    public void setFilterByStatus(String statusType) {
        try
        {
        (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(statusFilterBtn));
        statusFilterBtn.click();
        WebElement menuItem = driver.findElement(By.xpath("(//a[contains(.,'" + statusType + "')])[2]"));
        (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(menuItem));
        menuItem.click();
        Thread.sleep(3000);
        statusFilterBtn.click();
        Thread.sleep(3000);
        }
        catch(InterruptedException e)
        {
            FILELOG.debug("Error in setFilterByStatus" + e);
        }
    }

    public void clickToContextMenuItem(String taskID, String menuItemName) {
        FILELOG.debug("clickToContextMenuItem Started");
        try {
			Thread.sleep(3000);
		} catch (InterruptedException e) {
		}
        String taskNumber = taskID.substring(2);
        WebElement onElement = driver.findElement(By.xpath("//a[contains(.,'[" +taskID+ "]')]/../.."));
        Actions contextClick = new Actions(driver);
        mouseMove(onElement);
        contextClick.contextClick(onElement).build().perform();
        clickOnInvisibleElement(
        		driver.findElement(By.xpath("//div[contains(@id,'context-menu-"+taskNumber+
        				"')]//a[text()='"+menuItemName+"']"))
        	);
        try {
			Thread.sleep(1000);
		} catch (InterruptedException e) {
		}
    }

    public KanbanSubTaskEditPage doubleClickOnTask(String taskName) {
        try
        {
        WebElement onElement = driver.findElement(By.xpath("//div[contains(.,'"+taskName+"')]"));
         Actions action = new Actions(driver);
         action.doubleClick(onElement).build().perform();
         Thread.sleep(3000);
         return new KanbanSubTaskEditPage(driver);
    }
        catch(InterruptedException e)
        {
            return null;
        }
    }

    public void setTime(String time) {
       try
        {      
         (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(addTimeBtn));
         addTimeBtn.click();
         (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(addTimeField));
         addTimeField.sendKeys(time);
        saveAddedTime.click();
        submitDialog(submitAddedTime);
        Thread.sleep(3000);
        FILELOG.debug("Set time" + time);
    }
        catch(InterruptedException e)
        {
            FILELOG.debug("Error in setTime");
        }
        
    }
    
    public void setTimeRequirement(String time) {
         (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(addTimeReqBtn));
         addTimeReqBtn.click();
         (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(addTimeReqField));
         addTimeReqField.sendKeys(time);
        saveAddedTimeReq.click();
        submitDialog(submitAddedTimeReq);
        FILELOG.debug("Set time" + time);
    }

    public String getIDTaskByName(String taskName) {
        FILELOG.debug("getIDTaskByName started");
        String IDtask;
        WebElement element = driver.findElement(By.xpath("//div[contains(.,'"+taskName+"')]/preceding-sibling::*/div/a"));
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(element));
        String uid = element.getText();
        IDtask = uid.substring(1, uid.length()-1);
        return IDtask;
    }

    public KanbanTaskNewPage createTaskInCell(int release, String status) {
        int itemCount = driver.findElements(By.xpath("//table[contains(@id,'requestboard')]/tbody/tr[contains(@class,'board-columns')]/th")).size(); 
        int column = 1;
	for(int i = 1; i<=itemCount; i++)
	{
            if(driver.findElement(By.xpath("//table[contains(@id,'requestboard')]/tbody/tr[contains(@class,'board-columns')]/th["+i+"]"))
                    .getText().contains(status))	
               column = i;
        }
          int rowNum = release+1;
       WebElement onElement = driver.findElement(By.xpath(
               ".//table[contains(@id,'requestboard')]//tr[contains(@class,'row-cards')]["+rowNum+"]//td["+column+"]"));
       
       (new Actions(driver))
       		.moveToElement(onElement)
       		.click(onElement.findElement(By.xpath(".//*[contains(@class,'append-card')]")))
       		.build()
       		.perform();
       return new KanbanTaskNewPage(driver);
    }
	
	
}
