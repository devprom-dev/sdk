package ru.devprom.pages.kanban;

import java.util.ArrayList;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;

public class KanbanTasksPage extends KanbanPageBase {

	public KanbanTasksPage(WebDriver driver) {
		super(driver);
	}

	public KanbanTasksPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public KanbanTaskNewPage addNewTask(){
		addBtn.click();
		clickOnInvisibleElement(newTaskBtn);
		waitForDialog();
		return new KanbanTaskNewPage(driver);
	}
	
	public KanbanTaskNewPage addNewTaskUserType(String taskType){
		addBtn.click();
		WebElement taskBtn = driver.findElement(By.xpath("//ul/li/a[contains(.,'"+taskType+"')]"));
		clickOnInvisibleElement(taskBtn);
		waitForDialog();		
		return new KanbanTaskNewPage(driver);
	}
	
	public boolean isTaskPresent(String id){
		return driver.findElements(By.xpath("//tr[contains(@id,'requestlist1_row')]/td[@id='uid']/a[contains(.,'["+ id + "]')]")).size()>0;
	}
	
	public KanbanTaskViewPage clickToTask(String id) {
		try {
		driver.findElement(
				By.xpath("//tr[contains(@id,'requestlist1_row')]/td[@id='uid']/a[contains(.,'["+ id + "]')]")).click();
		}
		catch (NoSuchElementException e) {
			driver.findElement(
					By.xpath("//tr[contains(@id,'requestlist1_row')]/td[@id='uid']//strike[contains(.,'" + id + "')]")).click();
		}
		return new KanbanTaskViewPage(driver);
	}
	
	
	public KanbanTasksPage showAll() {
		driver.findElement(
				By.xpath("//a[@data-toggle='dropdown' and @uid='state']"))
				.click();
		String code = "filterLocation.turnOn('state', 'all', 1)";
		((JavascriptExecutor) driver).executeScript(code);
		return new KanbanTasksPage(driver);
	}
	
	public List<String> getTemplatesList(){
		List<String> results = new ArrayList<String>();
		List<WebElement> we = driver.findElements(By.xpath("//div[@id='main']//div[@class='btn-group']"
				+ "//a[contains(.,'Добавить')]/following-sibling::ul/li[@class='divider']/following-sibling::li/a"));
	   for (WebElement el:we){
		   results.add(el.getText());
	   }
		
		return results;
	}
}
