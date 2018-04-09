package ru.devprom.pages.kanban;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;

import ru.devprom.items.Project;

public class KanbanTasksStatesPage extends KanbanPageBase {

	public KanbanTasksStatesPage(WebDriver driver) {
		super(driver);
	}

	public KanbanTasksStatesPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public KanbanTaskStateEditPage editTaskState(String state){
		WebElement editButton = driver.findElement(By.xpath("//td[@id='caption' and text()='"+state+"']/following-sibling::td[@id='operations']//a[text()='Изменить']"));
		clickOnInvisibleElement(editButton);
		return new KanbanTaskStateEditPage(driver);
	}
	
}
