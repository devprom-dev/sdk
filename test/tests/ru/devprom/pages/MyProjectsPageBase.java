package ru.devprom.pages;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;

import ru.devprom.pages.project.tasks.MyTasksPage;
import ru.devprom.pages.project.tasks.TasksPage;

public class MyProjectsPageBase extends PageBase {

	public MyProjectsPageBase(WebDriver driver) {
		super(driver);
	}

	public MyTasksPage gotoMyTasks(){
		driver.findElement(By.xpath("//a[text()='Мои задачи']")).click();
		return new MyTasksPage(driver);
	}
	
}
