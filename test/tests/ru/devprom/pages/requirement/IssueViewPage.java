package ru.devprom.pages.requirement;

import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import ru.devprom.items.KanbanTask;

import ru.devprom.items.Project;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.project.ProjectPageBase;
import ru.devprom.pages.project.requirements.RequirementNewPage;
import ru.devprom.pages.project.requirements.RequirementViewPage;
import ru.devprom.pages.project.tasks.TaskCompletePage;
import ru.devprom.pages.project.tasks.TaskViewPage;
import ru.devprom.pages.project.testscenarios.TestScenarioNewPage;
import ru.devprom.pages.project.testscenarios.TestScenarioTestingPage;

public class IssueViewPage extends ProjectPageBase {

	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(.,'Действия')]")
	protected WebElement actionsBtn;
        
	@FindBy(id = "pm_ChangeRequestSubmitBtn")
	protected WebElement submitBtn;

	@FindBy(xpath = "//a[@id='workflow-resolved']")
	protected WebElement completeBtn;
        
    @FindBy(xpath = "//a[@id='workflow-inprogress']")
	protected WebElement analyseBtn;
        
	@FindBy(xpath = "//a[@id='modify']")
	protected WebElement editBtn;
	
	public IssueViewPage(WebDriver driver) {
		super(driver);
	}

	public IssueViewPage(WebDriver driver, Project project) {
		super(driver, project);
	}
	
	public String readID() {
		String id = driver
				.findElement(
						By.xpath("//ul[contains(@class,'breadcrumb')]/li/a[contains(@class,'with-tooltip')]"))
				.getText().trim();
		return id.substring(1, id.length() - 1);
	}
	
	public String readName() {
		return driver.findElement(By.xpath("//*[contains(@id,'pm_ChangeRequestCaption')]")).getText();
	}
	
	public String readDescription()
	{
		((JavascriptExecutor) driver).executeScript("document.evaluate(\"//div[@id='collapseTwo']\", document, null, 9, null).singleNodeValue.removeAttribute('class')");
		return driver.findElement(By.xpath("//div[contains(@id,'pm_ChangeRequestDescription') and contains(@class,'wysiwyg-text')]")).getText().trim();
	}

	protected WebElement findSubTask( String name ) {
		return driver.findElement(
				By.xpath("//input[@value='task']/following-sibling::div[contains(@id,'embeddedItems')]//*[contains(@class,'title') and contains(.,'"+name+"')]"));
	}
	
	public RequirementViewPage openRequirement(String name) {
        driver.findElement(By.xpath("//div[contains(@id,'embeddedItems')]//*[contains(@class,'title') and contains(.,'"+name+"')]")).click();
        WebElement menuItem = driver.findElement(By.id("open-form"));
        (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(menuItem));
        menuItem.click();
        return new RequirementViewPage(driver);
    }

     public String getIdRequirement (String name)
     {
    	 openTracesSection();
         List<WebElement> list = driver.findElements(By.xpath(".//*[@name='Requirement']//*[contains(@class,'title') and contains(.,"
                 + "'Студенты и преподаватели')]/a"));
         for( int i = 0; i < list.size(); i++ ) {
        	 String id = list.get(i).getText();
        	 if ( !id.equals("") ) {
                 String clearID = id.substring(1, id.length()-1);
                 FILELOG.debug("Requirement ID = " + clearID);
                 return clearID;
        	 }
         }
         return "";
     }

    public void completeTask() {
        clickOnInvisibleElement(completeBtn);
        waitForDialog();
    	submitDialog(submitBtn);
    }
    
	public void openTracesSection()
	{
		WebElement section = driver.findElement(By.xpath("//a[contains(@href,'collapseFive')]"));
		if ( section.isDisplayed() ) {
			section.click();
			(new WebDriverWait(driver, 5)).until(ExpectedConditions
					.visibilityOf(driver.findElement(By.id("collapseFive"))));
		}
	}
}
