package ru.devprom.pages.project.tasks;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebDriver;

import ru.devprom.items.Project;
import ru.devprom.pages.kanban.KanbanTasksPage;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class TasksBoardPage extends SDLCPojectPageBase {

	public TasksBoardPage(WebDriver driver) {
		super(driver);
		// TODO Auto-generated constructor stub
	}

	public TasksBoardPage(WebDriver driver, Project project) {
		super(driver, project);
		// TODO Auto-generated constructor stub
	}

	
	public boolean isTaskPresent(String taskId){
		return !driver.findElements(By.xpath("//a[contains(@class,'with-tooltip') and contains(.,'"+taskId+"')]")).isEmpty();
	}
	
	public TaskViewPage clickToTask(String taskId){
		driver.findElement(By.xpath("//a[contains(@class,'with-tooltip') and contains(.,'"+taskId+"')]")).click();
		return new TaskViewPage(driver);
	}
	
	public TaskViewPage clickToTaskByName(String name) {
		driver.findElement(
				By.xpath("//div[contains(@class,'bi-cap') and contains(.,'"+name+"')]/preceding-sibling::div//a")).click();
		return new TaskViewPage(driver);
	}

	public TasksBoardPage showAll() {
		driver.findElement(By.xpath("//a[@data-toggle='dropdown' and @uid='state']")).click();
		((JavascriptExecutor) driver).executeScript("filterLocation.turnOn('state', 'all', 0)");
		driver.findElement(By.xpath("//a[@data-toggle='dropdown' and contains(.,'Исполнитель')]")).click();
	    try {
			Thread.sleep(3000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		return new TasksBoardPage(driver);
	}
}
