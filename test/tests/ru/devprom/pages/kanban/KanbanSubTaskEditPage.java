package ru.devprom.pages.kanban;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.project.tasks.TaskViewPage;

public class KanbanSubTaskEditPage extends KanbanAddSubtaskPage{

        @FindBy(xpath = "//div[@id='modal-form']//span[@id='pm_TaskSourceCode']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addSourceCodeBtn;

	public KanbanSubTaskEditPage(WebDriver driver) {
		super(driver);
	}

	public KanbanSubTaskEditPage(WebDriver driver, Project project) {
		super(driver, project);
	}
        
        public void addSourceCode(String requirements)
	{
		clickTraceTab();
		addSourceCodeBtn.click();
		driver.findElement(
				By.xpath("//div[@id='modal-form']//input[@value='tasktracesourcecode']/following-sibling::div[contains(@id,'fieldRowObjectId')]//input[contains(@id,'ObjectIdText')]"))
				.sendKeys(requirements);
		autocompleteSelect(requirements);
		driver.findElement(
				By.xpath("//div[@id='modal-form']//input[@value='tasktracesourcecode']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
				.click();

	}
        
        public void clickTraceTab()
	{
		clickTab("trace");
	}
        
        public TaskViewPage saveChanges()
	{
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(saveBtn));
		submitDialog(saveBtn);
		return new TaskViewPage(driver);
	}
}
