package ru.devprom.pages.project.settings;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.Keys;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class TerminologyPage extends SDLCPojectPageBase {

	@FindBy(id="filter-settings")
	protected WebElement filterBtn;
	
	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(.,'Действия')]")
	protected WebElement actionsBtn;
	
	@FindBy(xpath = "//input[@valueparm='searchsystem']")
	protected WebElement filterInput;
	
	@FindBy(xpath = "//a[text()='Очистить значения']")
	protected WebElement resetBtn;
	
	public TerminologyPage(WebDriver driver) {
		super(driver);
	}

	public TerminologyPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public void changeTerm(String termOldName, String termNewName) throws InterruptedException{
		WebElement element = driver.findElement(By.xpath("//tr[contains(@id,'terminologylist1_row_') and @object-id='"+termOldName+"']/td[@id='customvalue']/textarea"));
		scrollToElement(element);
		element.clear();
		element.sendKeys(termNewName);
		element.sendKeys(Keys.TAB);
		Thread.sleep(10000);
		
	}
	
	public TerminologyPage resetToDefaults(){
		actionsBtn.click();
		(new WebDriverWait(driver, 2)).until(ExpectedConditions.visibilityOf(resetBtn));
		try {
			Thread.sleep(500);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		resetBtn.click();
		try {
			Thread.sleep(10000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		return new TerminologyPage(driver);
	}
	
	public TerminologyPage showAll(){
		filterBtn.click();
		String code = "filterLocation.setup( 'rows=all', 0 );";
		((JavascriptExecutor) driver).executeScript(code);
		filterBtn.click();
		return new TerminologyPage(driver);
	}
	
	public TerminologyPage filterBy(String text){
		filterInput.clear();
		filterInput.sendKeys(text + "\n");
		try {
			Thread.sleep(5000);
		} catch (InterruptedException e) {
		}
		return new TerminologyPage(driver);
	}
	
}
