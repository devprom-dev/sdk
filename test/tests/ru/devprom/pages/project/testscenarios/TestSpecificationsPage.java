package ru.devprom.pages.project.testscenarios;

import org.openqa.selenium.By;
import org.openqa.selenium.TimeoutException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Action;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.helpers.Configuration;
import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requirements.RequirementViewPage;

public class TestSpecificationsPage extends SDLCPojectPageBase {

	@FindBy(xpath = "//a[@id='new-doc']")
	protected WebElement createSpecificationBtn;
        
	@FindBy(xpath = "//div[contains(@class,'operation')]//a[contains(@class,'actions-button')]")
	protected WebElement actionBtn;
        
        @FindBy(xpath = ".//*[@id='modify']")
	protected WebElement editItem;
        
        @FindBy(xpath = "//a[@uid='compare-actions']")
	protected WebElement handleBtn;
        
        @FindBy(xpath = "//a[@id='restore-consistency']")
	protected WebElement repairItem;
        
        //Пункт Начать тестирование меню действий строки
        @FindBy(xpath = ".//*[@id='operations']//*[contains(text(),'Начать тестирование')]")
	protected WebElement startTestingItem;
	
	public TestSpecificationsPage(WebDriver driver) {
		super(driver);
	}

	public TestSpecificationsPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public TestSpecificationNewPage createNewSpecification(){
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(createSpecificationBtn));
		createSpecificationBtn.click();
		return new TestSpecificationNewPage(driver);
	}
	
	public TestSpecificationViewPage clickToSpecification(String id)
	{
		try {
			Thread.sleep(2000);
		} catch (InterruptedException e) {
		}
		driver.findElement(By.id("restoreTree")).click();
		By xpath = By.xpath("//td[@id='uid']/a[contains(@href,'"+id+"')]");
		try {
			(new WebDriverWait(driver, 3)).until(ExpectedConditions.presenceOfElementLocated(xpath));
		} catch(TimeoutException e) {
		}
		driver.findElement(xpath).click();
		try {
			Thread.sleep(2000);
		} catch (InterruptedException e) {
		}
		return new TestSpecificationViewPage(driver);
	}

	public TestSpecificationViewPage addContent(String content)
	{
		WebElement editableArea = driver.findElement(By.xpath("//div[starts-with(@id,'WikiPageContent') and @objectclass='TestScenario']"));
		editableArea.click();
		try {
			Thread.sleep(800);
		} catch (InterruptedException e) {
		}
		editableArea.sendKeys(content);
		sleep(Configuration.getPersistTimeout());
		return new TestSpecificationViewPage(driver);
	}

    public TestScenarioEditPage edit() {
        clickOnInvisibleElement(actionBtn);
        clickOnInvisibleElement(editItem);
        return new TestScenarioEditPage(driver);
    }

    public void clickRepair() {
        clickOnInvisibleElement(handleBtn);
        clickOnInvisibleElement(repairItem);
		try {
			Thread.sleep(3000);
		} catch (InterruptedException e) {
		}
    }

    public StartTestingPage clickStartTesting(String Id) {
        String clearID = Id.split("-")[1];
        WebElement onElement = driver.findElement(By.xpath(".//tr[@raw-id='"+clearID+"']"));
        WebElement itemAsterixBtn = driver.findElement(By.xpath("//tr[@raw-id='"+clearID+"']//*[@id='operations']/div/a"));
        (new Actions(driver)).moveToElement(onElement).click(itemAsterixBtn).build().perform();
        clickOnInvisibleElement(startTestingItem);
       return new StartTestingPage(driver);
    }

    public String getIdByName(String name) {
        String ids;
        ids = driver.findElement(By.xpath("//tr[@role='row']/td[@id='caption' and contains(.,'"+
                name+"')]/preceding-sibling::td[@id='uid']")).getText();
        String id = ids.substring(1, ids.length()-1);
        FILELOG.debug("Click to UID of requirement");
        return id; 
    }
}
