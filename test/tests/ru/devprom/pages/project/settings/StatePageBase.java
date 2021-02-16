package ru.devprom.pages.project.settings;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public abstract class StatePageBase extends SDLCPojectPageBase {

	@FindBy(xpath = "//a[contains(.,'Добавить') and contains(@class,'append-btn')]")
	protected WebElement addBtn;
	
	@FindBy(xpath = "//a[@id='bulk-delete']")
	protected WebElement deleteBtn;
	
	@FindBy(id = "SubmitBtn")
	protected WebElement submitBtn;
	
	public StatePageBase(WebDriver driver) {
		super(driver);
	}

	public StatePageBase(WebDriver driver, Project project) {
		super(driver, project);
	}
	

	public TransitionEditPage clickChangeTransition(String stateName, String transitionName) {
		WebElement element = driver.findElement(By.xpath("//td[@id='caption' and contains(.,'"+stateName+"')]/following-sibling::td[@id='transitions']//*[contains(@class,'title') and text()='"+transitionName+"']/../following-sibling::ul[@role='menu']//a[text()='Изменить']"));
		clickOnInvisibleElement(element);
		waitForDialog();
		return new TransitionEditPage(driver);
	}
	
	public TransitionNewPage clickAddTransition(String stateName) {
		WebElement element = driver.findElement(By.xpath("//td[@id='caption' and contains(.,'"+stateName+"')]/following-sibling::td//a[text()='Добавить переход']"));
		clickOnInvisibleElement(element);
		waitForDialog();
		return new TransitionNewPage(driver);
	}
	
	public StateNewPage addState(){
		addBtn.click();
		waitForDialog();
		return new StateNewPage(driver);
	}

	public void selectState(String stateName){
		Assert.assertTrue(driver.findElements(By.xpath("//td[@id='caption' and text()='"+stateName+"']/preceding-sibling::td/input[contains(@class,'checkbox')]")).size()>0, "There is no state named "+stateName+" or it is in use and can't be edited");
		driver.findElement(By.xpath("//td[@id='caption' and text()='"+stateName+"']/preceding-sibling::td/input[contains(@class,'checkbox')]")).click();
	}
    
	public void deleteState(String stateName){
		selectState(stateName);
		clickOnInvisibleElement(deleteBtn);
		waitForDialog();
        submitDialog(submitBtn);
		waitForFilterActivity();
	}
	
	public StateEditPage editState(String requestState){
		WebElement changeLink = driver.findElement(By.xpath("//td[@id='caption' and text()='"+requestState+"']/following-sibling::td[@id='operations']//a[text()='Изменить']"));
		clickOnInvisibleElement(changeLink);
		waitForDialog();
		return new StateEditPage(driver);
	}
	
	
	public StateEditPage modifyCompleted() {
		clickOnInvisibleElement(driver
				.findElement(By
						.xpath("//td[@id='caption' and text()='Выполнена']/following-sibling::td[@id='operations']//a[text()='Изменить']")));
		waitForDialog();
		return new StateEditPage(driver);
	}

}
