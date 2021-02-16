package ru.devprom.pages.project.tasks;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;

import ru.devprom.items.Spent;

public class MyTasksPage extends TasksPage
{
	public MyTasksPage(WebDriver driver) {
		super(driver);
	}

	public TaskViewPage clickToTask(String id) {
		try {
			clickOnInvisibleElement(
				driver.findElement(
					By.xpath("//tr[contains(@id,'workitemlist1_row_')]/td[@id='uid']/a[contains(.,'["+ id + "]')]")
				)
			);
		}
		catch (NoSuchElementException e) {
			clickOnInvisibleElement(
				driver.findElement(
					By.xpath("//tr[contains(@id,'workitemlist1_row_')]/td[@id='uid']//strike[contains(.,'" + id + "')]")
					)
				);
		}
		return new TaskViewPage(driver);
	}
	
	public MyTasksPage addSpentRecord(Spent spent, String taskId)
	{
		super.addSpentRecord(spent, taskId);
		return new MyTasksPage(driver);
	}
}
