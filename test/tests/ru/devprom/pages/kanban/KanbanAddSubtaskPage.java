package ru.devprom.pages.kanban;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.KanbanTask;
import ru.devprom.items.Project;

public class KanbanAddSubtaskPage extends KanbanPageBase {

	@FindBy(id = "pm_TaskCaption")
	protected WebElement captionEdit;
	
	@FindBy(id = "pm_TaskAssignee")
	protected WebElement ownerSelect;
	
	@FindBy(id = "pm_TaskTaskType")
	protected WebElement typeSelect;
	
	@FindBy(id = "pm_TaskSubmitBtn")
	protected WebElement saveBtn;	

	@FindBy(id = "pm_TaskSubmitOpenBtn")
	protected WebElement saveOpenBtn;	
	
	@FindBy(id = "pm_TaskCancelBtn")
	protected WebElement cancelBtn;
	
    //Вкладка Пожелание
    @FindBy(xpath = ".//*[@id='modal-form']/ul/li[3]/a")
    protected WebElement wishesTab;
	
	public KanbanAddSubtaskPage(WebDriver driver) {
		super(driver);
	}

	public KanbanAddSubtaskPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public KanbanTaskViewPage createSubtask(KanbanTask task){
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(captionEdit));
		addName(task.getName());
		selectType(task.getType());
		if (!"".equals(task.getOwner())) selectOwner(task.getOwner());
		submitDialog(saveBtn);
		return new KanbanTaskViewPage(driver);
	}
	
	
	 public void addName(String name){
	    	captionEdit.clear();
	    	captionEdit.sendKeys(name);
	    }
	 
	 public void selectOwner(String owner){
	    	(new Select(ownerSelect)).selectByVisibleText(owner);
	    }
	 
	 public void selectType(String type){
	    	(new Select(typeSelect)).selectByVisibleText(type);
	    }
	 
	    public KanbanTasksPage cancel()
	    {
		   cancelDialog(cancelBtn);
		   return new KanbanTasksPage(driver);
	    }
	    
	    public void openTabWishes()
	    {
	        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(wishesTab));
	        wishesTab.click();
	    }
}
