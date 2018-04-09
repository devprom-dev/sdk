package ru.devprom.pages.kanban;

import org.openqa.selenium.By;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;

import ru.devprom.items.Project;

public class KanbanActivitiesPage extends KanbanPageBase {

	public KanbanActivitiesPage(WebDriver driver) {
		super(driver);
	}

	public KanbanActivitiesPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public KanbanTaskViewPage clickToLastTask(){
		driver.findElement(By.xpath("//td[@id='content']/a[contains(text(),'I')]")).click();
		return new KanbanTaskViewPage(driver);
	}
	
	public KanbanTaskViewPage clickToTask(String id){
		try {
			driver.findElement(By.xpath("//td[@id='content']/a[contains(text(),'["+ id + "]')]")).click();
		} catch (NoSuchElementException e) {
			driver.findElement(
					By.xpath("//td[@id='content']/a/strike[contains(text(),'" + id + "')]/..")).click();
		}
		return new KanbanTaskViewPage(driver);
	}
}
