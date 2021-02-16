package ru.devprom.pages.kanban;

import java.util.ArrayList;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.helpers.DataProviders;
import ru.devprom.items.KanbanTask;
import ru.devprom.items.Project;
import ru.devprom.pages.CKEditor;

public class KanbanTaskNewPage extends KanbanPageBase {

	@FindBy(id = "pm_ChangeRequestCaption")
	protected WebElement captionEdit;

	@FindBy(id = "pm_ChangeRequestState")
	protected WebElement stateEdit;

	@FindBy(id = "pm_ChangeRequestType")
	protected WebElement typeSelect;
	
	@FindBy(id = "pm_ChangeRequestPriority")
	protected WebElement prioritySelect;
	
	@FindBy(id = "AuthorText")
	protected WebElement authorSelect;
	
	@FindBy(id = "pm_ChangeRequestOwner")
	protected WebElement ownerSelect;
	
	@FindBy(xpath = "//span[@name='pm_ChangeRequestTags']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addTagsBtn;

	// строка Задачи - "добавить"
	@FindBy(xpath = "//span[@name='pm_ChangeRequestTasks']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addTasks;
	
	// кнопка Связи на вкладке Трассировки
	@FindBy(xpath = "//span[@name='pm_ChangeRequestLinks']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addLinkedTasksBtn;
	
	@FindBy(xpath = "//span[@name='pm_ChangeRequestWatchers']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addWatchersBtn;
	
	@FindBy(id = "pm_ChangeRequestSubmitBtn")
	protected WebElement saveBtn;	
	
	@FindBy(id = "pm_ChangeRequestCancelBtn")
	protected WebElement cancelBtn;
	
	
	public KanbanTaskNewPage(WebDriver driver) {
		super(driver);
	}

	public KanbanTaskNewPage(WebDriver driver, Project project) {
		super(driver, project);
	}
	
	
   public KanbanTasksPage createTask(KanbanTask task) {
		
    	addName(task.getName());
    	
    	if (task.getDescription()!=null && !task.getDescription().equals("")) addDescription(task.getDescription());
		
    	if (task.getPriority()!=null && !task.getPriority().equals("")) selectPriority(task.getPriority());
		
    	else task.setPriority(prioritySelect.getText());
		
		if (task.getAuthor()!=null && !task.getAuthor().equals("")) addAuthor(task.getAuthor());
		
		if (task.getOwner()!=null && !task.getOwner().equals("")) selectOwner(task.getOwner());
		
		return saveTask(task);
	}
	
    public KanbanTasksPage saveTask(KanbanTask task)
	{
		 (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(saveBtn));
		 submitDialog(saveBtn);
		 //read ID
		   (new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//td[@id='caption' and contains(.,'"+task.getName()+"')]")));
		 String uid =driver.findElement(By.xpath("//td[@id='caption' and contains(.,'"+task.getName()+"')]/preceding-sibling::td[@id='uid']/a")).getText();
		 task.setId(uid.substring(1, uid.length()-1));
  		return new KanbanTasksPage(driver);
    }

	 public void addName(String name){
	    	captionEdit.clear();
	    	captionEdit.sendKeys(name);
	}
	 
	 public void addDescription(String description) {
		(new CKEditor(driver)).changeText(description);
     }
	
    public void selectType(String type){
    		(new Select(typeSelect)).selectByVisibleText(type);
    }
	 
    public void selectPriority(String priority) {
    	(new Select(prioritySelect)).selectByVisibleText(priority);
    }

	public void selectState(String state) {
		(new Select(stateEdit)).selectByVisibleText(state);
	}
	    
	    public void addAuthor(String author){
	    	authorSelect.clear();
	    	authorSelect.sendKeys(author);
	    	autocompleteSelect(author);
	    }
	    
	    public void selectOwner(String owner){
    		(new Select(ownerSelect)).selectByVisibleText(owner);
    }

    public  void setAddTasks(String name, String assignee, String type){
		addTasks.click();
		//строка Название у Задачи
		driver.findElement(By.xpath("//input[@class='input-block-level']")).sendKeys(name);
		//строка Тип у Задачи
		Select selDr = new Select(driver.findElement(By.xpath("//select[@tabindex='106']")));
		selDr.selectByVisibleText(type);
	}
	    
		    public void addTag(String tag){
		    	addTagsBtn.click();
		    	driver.findElement(
						By.xpath("//input[@value='requesttag']/following-sibling::div[contains(@id,'fieldRowTag')]//input[contains(@id,'TagText')]"))
						.sendKeys(tag);
				driver.findElement(
						By.xpath("//input[@value='requesttag']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
						.click();
		    }
		    
		    public void addLinkedTasks(String linkedTask, String linkType){
		    	addLinkedTasksBtn.click();
		    	driver.findElement(
						By.xpath("//input[@value='requestlink']/following-sibling::div[contains(@id,'fieldRowTargetRequest')]//input[contains(@id,'TargetRequestText')]"))
						.sendKeys(linkedTask);
		    	autocompleteSelect(linkedTask);
		    	WebElement select = driver.findElement(
						By.xpath("//input[@value='requestlink']/following-sibling::div[contains(@id,'fieldRowLinkType')]//select[contains(@id,'LinkType')]"));
		    	(new Select(select)).selectByVisibleText(linkType);
		    
				driver.findElement(
						By.xpath("//input[@value='requestlink']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
						.click();
		    }
		 
			public void addWatcher(String watcher) {
				addWatchersBtn.click();

				driver.findElement(
						By.xpath("//input[@value='watcher']/following-sibling::div[contains(@id,'fieldRowSystemUser')]//input[contains(@id,'SystemUserText')]"))
						.sendKeys(watcher);
				autocompleteSelect(watcher);
				driver.findElement(
						By.xpath("//input[@value='watcher']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
						.click();

			}
			
		    public String readName(){
		     return captionEdit.getAttribute("value");
		    }
		    
		    public String readDescription() {
		    	return (new CKEditor(driver)).getText();
			}
		    
		   public String readType(){
			   return (new Select(typeSelect)).getFirstSelectedOption().getText();
		   }
		   
		   public String readPriority(){
			   return (new Select(prioritySelect)).getFirstSelectedOption().getText();
		   }
		   
		   public String readAuthor(){
			   return authorSelect.getAttribute("value");
		   }
		   
		   public String readOwner(){
			   return (new Select(ownerSelect)).getFirstSelectedOption().getText();
		   }
		   public List<String> readWatchers(){
			   List<String> results = new ArrayList<String>();
				List<WebElement> we = driver.findElements(By.xpath("//input[@value='watcher']/following-sibling::div[contains(@id,'embeddedList')]//*[contains(@class,'title')]"));
				   for (WebElement el:we){
					   results.add(el.getText());
				   }
				return results;
		   }
		   
		   /**
		    * Проверка обязательности поля, через атрибут required. 
		    * Реализовано пока только для поля Тип.
		    * @param fieldName
		    * @return
		    */
		   public boolean isRequired(String fieldName){
			   switch (fieldName) {
			case "Тип":
                return !driver.findElements(By.xpath("//select[@id='pm_ChangeRequestType' and @required]")).isEmpty();
			default:
				return false;
			}
		   }
		   
		   public KanbanTasksPage cancel(){
			   cancelDialog(cancelBtn);
			   return new KanbanTasksPage(driver);
		   }

	public void saveTaskFromBoard(KanbanTask task)
	{
        submitDialog(saveBtn);
        By idLocator = By.xpath("//div[contains(.,'"+task.getName()+"')]/preceding-sibling::*//a[contains(@class,'uid')]");
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(idLocator));
        WebElement element = driver.findElement(idLocator);
        String uid = element.getText();
        task.setId(uid.substring(1, uid.length()-1));
    }
	
	public void save() {
		submitDialog(saveBtn);
	}
}
