package ru.devprom.pages.project;

import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.kanban.KanbanTasksPage;
import ru.devprom.pages.project.requests.RequestsBoardPage;
import ru.devprom.pages.project.tasks.TaskViewPage;

public class CrossProjectsTasksBoard extends SDLCPojectPageBase
{
	@FindBy(id = "filter-settings")
	protected WebElement asterixBtn;

	public CrossProjectsTasksBoard(WebDriver driver) {
		super(driver);
		 try {
				Thread.sleep(5000);
			} catch (InterruptedException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		 }

	public CrossProjectsTasksBoard(WebDriver driver, Project project) {
		super(driver, project);
	}
	

	   public CrossProjectsTasksBoard moveToAnotherProject(String taskNumericId, String projectName, int column){
	    	int row=0;
	    	
	    	List<WebElement> rows = driver.findElements(By.xpath("//table[contains(@id,'taskboardlist')]//tr[@class='info']"));
	    	for (int i=0; i<rows.size();i++){
	    	if (rows.get(i).getText().contains(projectName)) row = i + 1;
	    	}
	    	 
	    	if (row==0) {
	    		FILELOG.error("Не найдена секция проекта " + projectName);
	    	}
	    	
	      	 WebElement element = driver.findElement(By.xpath("//div[@object='"+taskNumericId+"']"));
	           String srow = String.valueOf(row);
	           String scolumn = String.valueOf(column);
	           WebElement onElement = driver.findElement(By.xpath("//table[contains(@id,'taskboardlist')]//tr[@class='row-cards']["+srow+"]//td[contains(@class,'board-column')]["+scolumn+"]"));
	           mouseMove(element);
	           new Actions(driver).dragAndDrop(element, onElement).build().perform();
	           mouseMove(onElement);
	          
	           (new WebDriverWait(driver, waiting*2)).until(ExpectedConditions
	   				.presenceOfElementLocated(By.xpath("//table[contains(@id,'taskboardlist')]//tr[@class='row-cards']["+srow+"]//td[contains(@class,'board-column')]["+scolumn+"]//div[@object='"+taskNumericId+"']")));
	      	 return new CrossProjectsTasksBoard(driver);
	      }
	      
	       public boolean isRequestInSection(String taskNumericId,  String projectName, int column){
	    		int row=0;
	        	
	        	List<WebElement> rows = driver.findElements(By.xpath("//table[contains(@id,'taskboardlist')]//tr[@class='info']"));
	        	for (int i=0; i<rows.size();i++){
	        	if (rows.get(i).getText().contains(projectName)) row = i + 1;
	        	}
	        	 
	        	if (row==0) {
	        		FILELOG.error("Не найдена секция проекта " + projectName);
	        	}
	    	   
	         	String srow = String.valueOf(row);
	           String scolumn = String.valueOf(column);
	           
	          return (!driver.findElements(By.xpath("//table[contains(@id,'taskboardlist')]//tr[@class='row-cards']["+srow+"]//td[contains(@class,'board-column')]["+scolumn+"]//div[@object='"+taskNumericId+"']")).isEmpty());
	       }
	
	       
   public TaskViewPage clickToTask(String id){
	   driver.findElement(By.xpath("//a[contains(@class,'with-tooltip') and text()='["+id+"]']")).click();
	   return new TaskViewPage(driver);
   }
}
