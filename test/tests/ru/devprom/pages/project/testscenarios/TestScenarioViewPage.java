package ru.devprom.pages.project.testscenarios;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requirements.RequirementAddToBaselinePage;
import ru.devprom.pages.project.requirements.RequirementEditPage;

public class TestScenarioViewPage extends SDLCPojectPageBase {

	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(.,'Действия')]")
	protected WebElement actionsBtn;
	
	@FindBy(xpath = "//a[text()='Завершить']")
	protected WebElement completeBtn;
	
	@FindBy(xpath = "//a[text()='Начать тестирование']")
	protected WebElement beginTestBtn;
	
	@FindBy(xpath = "//a[@id='new-baseline']")
	protected WebElement addToBaselineBtn;

	@FindBy(xpath = "//a[@id='new-branch']")
	protected WebElement makeBranchBtn;

	@FindBy(xpath = "//a[@id='modify']")
	protected WebElement editBtn;

	public TestScenarioViewPage(WebDriver driver) {
		super(driver);
	}

	public TestScenarioViewPage completeTestScenario(){
		clickOnInvisibleElement(completeBtn);
		return new TestScenarioViewPage(driver);
	}
	
    public StartTestingPage beginTest() {
    	clickOnInvisibleElement(actionsBtn);
		clickOnInvisibleElement(beginTestBtn);
		waitForDialog();
		return new StartTestingPage(driver);
    }
    
    public StartTestingPage startTesting() {
    	clickOnInvisibleElement(actionsBtn);
		clickOnInvisibleElement(beginTestBtn);
		waitForDialog();
        return new StartTestingPage(driver);
    }

	public TestScenarioEditPage edit()
	{
		clickOnInvisibleElement(editBtn);
		return new TestScenarioEditPage(driver);
	}

	public TestScenarioAddToBaselinePage addToBaseline()
	{
		clickOnInvisibleElement(addToBaselineBtn);
		waitForDialog();
		return new TestScenarioAddToBaselinePage(driver);
	}

	public TestScenarioAddToBaselinePage makeBranch()
	{
		clickOnInvisibleElement(makeBranchBtn);
		waitForDialog();
		return new TestScenarioAddToBaselinePage(driver);
	}

	public boolean isChildScenarioPresent(String name) {
		return !driver.findElements(By.xpath("//span[contains(@class,'fancytree-title') and contains(.,'"+name+"')]")).isEmpty();
	}

    public void addContent(String newContent) {
        try {
			Thread.sleep(2000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		driver.findElement(By.xpath("//div[starts-with(@id,'WikiPageContent') and @objectclass='TestScenario']")).sendKeys(newContent);
    }

    public TestScenarioViewPage gotoPage(String name ) {
		clickOnInvisibleElement(
				driver.findElement(By.xpath("//span[contains(@class,'fancytree-title') and contains(.,'"+name+"')]"))
		);
		try {
			Thread.sleep(5000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		return this;
	}
}
