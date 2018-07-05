package ru.devprom.pages.project.testscenarios;

import java.util.ArrayList;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class TestScenarioEditPage extends TestScenarioNewPage {

	@FindBy(xpath = "//button[@id='WikiPageCancelBtn']")
	protected WebElement cancelBtn;
	
	public TestScenarioEditPage(WebDriver driver) {
		super(driver);
	}

	public TestScenarioEditPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public List<String> readLinkedRequirements()
	{
		clickTraceTab();
		List<String> requirements = new ArrayList<String>();
		List<WebElement> elements = driver.findElements(By.xpath("//div[@id='fieldRowRequirement']//input[@value='wikipageinversedtrace']/following-sibling::div[contains(@id,'embeddedItems')]/div[contains(@id,'Caption')]//*[contains(@class,'title')]/a"));
		for (WebElement element:elements){
			requirements.add(element.getAttribute("href").substring(element.getAttribute("href").lastIndexOf('/')+1));
		}
		return requirements;
	}
	
	public List<String> readOriginalScenarios()
	{
		clickTraceTab();
		List<String> scenarios = new ArrayList<String>();
		List<WebElement> elements = driver.findElements(By.xpath("//div[@id='fieldRowTraceSourceScenario']//input[@value='testscenarioinversedtracetestscenario']/following-sibling::div[contains(@id,'embeddedItems')]/div[contains(@id,'Caption')]//*[contains(@class,'title')]/a"));
		for (WebElement element:elements){
			scenarios.add(element.getAttribute("href").substring(element.getAttribute("href").lastIndexOf('/')+1));
		}
		return scenarios;
	}

	public void close() {
		cancelDialog(cancelBtn);
	}
        
        

    public void addTrace(String name) {
        try
        {
        addRequests(name);
        Thread.sleep(3000);
        submitDialog(submitBtn);
        Thread.sleep(3000);
        }
        catch(InterruptedException e)
        {}
    }
    
    public void addRequests(String request)
	{
            try
        {
		clickTraceTab();
		WebElement addRequestBtn = driver.findElement(By.xpath("//span[@id='WikiPageIssues']//a[@class='dashed embedded-add-button']"));
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
					.visibilityOf(addRequestBtn));
			addRequestBtn.click();
			(new WebDriverWait(driver, waiting))
					.until(ExpectedConditions.visibilityOfElementLocated(By
							.xpath("//input[contains(@id,'ChangeRequestText')]")));
			driver.findElement(
					By.xpath("//input[contains(@id,'ChangeRequestText')]"))
					.sendKeys(request);
			autocompleteSelect(request);
			driver.findElement(
					By.xpath(".//span[@id='WikiPageIssues']//input[contains(@id,'saveEmbedded')]"))
					.click();
        Thread.sleep(3000);
            }
        catch(InterruptedException e)
        {}
        }
        
}
