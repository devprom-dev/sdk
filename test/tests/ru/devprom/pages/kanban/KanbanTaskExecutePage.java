package ru.devprom.pages.kanban;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;

public class KanbanTaskExecutePage extends KanbanPageBase {

	@FindBy(id = "pm_TaskSubmitBtn")
	protected WebElement submitBtn;
	
	
	public KanbanTaskExecutePage(WebDriver driver) {
		super(driver);
	}

	public KanbanTaskExecutePage(WebDriver driver, Project project) {
		super(driver, project);
	}

	
	public KanbanTaskViewPage subtaskExecute(){
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(submitBtn));
		submitDialog(submitBtn);
		return new KanbanTaskViewPage(driver);
	}
	
	public KanbanTaskViewPage subtaskExecute(double estimates){
		driver.findElement(By.xpath("//span[@name='pm_TaskFact']//a[contains(@class,'embedded-add-button')]")).click();
		driver.findElement(By.xpath("//span[@name='pm_TaskFact']//input[contains(@id,'Capacity')]")).sendKeys(String.valueOf(estimates));
		driver.findElement(By.xpath("//span[@name='pm_TaskFact']//input[contains(@id,'saveEmbedded')]")).click();
		submitDialog(submitBtn);
		return new KanbanTaskViewPage(driver);
	}
}
