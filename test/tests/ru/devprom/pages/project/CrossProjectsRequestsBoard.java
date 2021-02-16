package ru.devprom.pages.project;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.project.requests.RequestsBoardPage;

public class CrossProjectsRequestsBoard extends SDLCPojectPageBase {

	public CrossProjectsRequestsBoard(WebDriver driver) {
		super(driver);
  try {
	Thread.sleep(5000);
} catch (InterruptedException e) {
	// TODO Auto-generated catch block
	e.printStackTrace();
}
	}

	public CrossProjectsRequestsBoard(WebDriver driver, Project project) {
		super(driver, project);
	}


    public CrossProjectsRequestsBoard moveToAnotherProject(String requestNumericId, String projectName, int column){
    	int row=0;
    	
    	List<WebElement> rows = driver.findElements(By.xpath("//table[@id='requestboard1']//tr[@class='info']"));
    	for (int i=0; i<rows.size();i++){
    	if (rows.get(i).getText().contains(projectName)) row = i + 1;
    	}
    	 
    	if (row==0) {
    		FILELOG.error("Не найдена секция проекта " + projectName);
    	}
    	
      	 WebElement element = driver.findElement(By.xpath("//div[@object='"+requestNumericId+"']"));
           String srow = String.valueOf(row);
           String scolumn = String.valueOf(column);
           WebElement onElement = driver.findElement(By.xpath("//table[@id='requestboard1']//tr[@class='row-cards']["+srow+"]//td[contains(@class,'board-column')]["+scolumn+"]"));
           mouseMove(element);
           new Actions(driver).dragAndDrop(element, onElement).build().perform();
           mouseMove(onElement);
           try {
			Thread.sleep(3000);
			} catch (InterruptedException e) {
			}
          driver.navigate().refresh();
           (new WebDriverWait(driver, waiting)).until(ExpectedConditions
   				.presenceOfElementLocated(By.xpath("//table[@id='requestboard1']//tr[@class='row-cards']["+srow+"]//td[contains(@class,'board-column')]["+scolumn+"]//div[@object='"+requestNumericId+"']")));
      	 return new CrossProjectsRequestsBoard(driver);
      }
      
       public boolean isRequestInSection(String requestNumericId,  String projectName, int column){
    		int row=0;
        	
        	List<WebElement> rows = driver.findElements(By.xpath("//table[@id='requestboard1']//tr[@class='info']"));
        	for (int i=0; i<rows.size();i++){
        	if (rows.get(i).getText().contains(projectName)) row = i + 1;
        	}
        	 
        	if (row==0) {
        		FILELOG.error("Не найдена секция проекта " + projectName);
        	}
    	   
         	String srow = String.valueOf(row);
           String scolumn = String.valueOf(column);
           
          return (!driver.findElements(By.xpath("//table[@id='requestboard1']//tr[@class='row-cards']["+srow+"]//td[contains(@class,'board-column')]["+scolumn+"]//div[@object='"+requestNumericId+"']")).isEmpty());
       }
}
