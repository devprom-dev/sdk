package ru.devprom.pages.project.tasks;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.items.RTask;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class TaskCompletePage extends SDLCPojectPageBase {

	@FindBy(xpath = "//div[@role='dialog']//button[@type='button']/span[text()='Сохранить']/..")
	protected WebElement submitBtn;
	
	@FindBy(xpath = "//div[@role='dialog']//span[@id='pm_TaskFact']//input[contains(@id,'Capacity')]")
	protected WebElement timeInput;
	
	@FindBy(xpath = "//div[@role='dialog']//span[@id='pm_TaskFact']//textarea[contains(@id,'Description')]")
	protected WebElement commentInput;
	
	@FindBy(xpath = "//div[@role='dialog']//span[@id='pm_TaskFact']//input[contains(@id,'saveEmbedded')]")
	protected WebElement saveSpentTime;

	public TaskCompletePage(WebDriver driver) {
		super(driver);
	}

	public TaskCompletePage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public TaskViewPage complete(RTask task) {
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//div[@role='dialog']//button[@type='button']/span[text()='Сохранить']/..")));
		submitBtn.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By.xpath("//span[contains(@class,'label') and contains(text(),'Выполнена')]")));
		return new TaskViewPage(driver);
	}
        
        public TaskViewPage complete(String coment) {
		(new CKEditor(driver)).typeText(coment);
                submitDialog(submitBtn);
		return new TaskViewPage(driver);
	}
	
	public TaskViewPage complete(RTask task, double time, String coment) {
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//div[@role='dialog']//button[@type='button']/span[text()='Сохранить']/..")));
		driver.findElement(By.xpath("//div[@role='dialog']//span[@id='pm_TaskFact']//a[contains(@class,'embedded-add-button')]")).click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(timeInput));
		
		timeInput.sendKeys(String.valueOf(time));
		commentInput.sendKeys(coment);
		saveSpentTime.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOfElementLocated(By.xpath("//div[@role='dialog']//span[@id='pm_TaskFact']//a[contains(@class,'embedded-add-button')]")));
		
		submitBtn.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By.xpath("//span[contains(@class,'label') and contains(text(),'Выполнена')]")));
		return new TaskViewPage(driver);
	}
        
        public void complete(double time, String coment) {
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//div[@role='dialog']//button[@type='button']/span[text()='Сохранить']/..")));
		driver.findElement(By.xpath("//div[@role='dialog']//span[@id='pm_TaskFact']//a[contains(@class,'embedded-add-button')]")).click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(timeInput));
		
		timeInput.sendKeys(String.valueOf(time));
		commentInput.sendKeys(coment);
		saveSpentTime.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOfElementLocated(By.xpath("//div[@role='dialog']//span[@id='pm_TaskFact']//a[contains(@class,'embedded-add-button')]")));
		submitDialog(submitBtn);
	}
}