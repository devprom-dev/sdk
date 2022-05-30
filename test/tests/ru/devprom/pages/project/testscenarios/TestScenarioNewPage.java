package ru.devprom.pages.project.testscenarios;

import java.util.ArrayList;
import org.openqa.selenium.By;
import org.openqa.selenium.Keys;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.helpers.Configuration;
import ru.devprom.items.Project;
import ru.devprom.items.TestScenario;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requirements.RequirementViewPage;

public class TestScenarioNewPage extends SDLCPojectPageBase {

	@FindBy(id = "WikiPageCaption")
	protected WebElement captionEdit;

	@FindBy(id = "WikiPageSubmitBtn")
	protected WebElement submitBtn;
	
	@FindBy(id = "ParentPageText")
	protected WebElement parentPageInput;
        
	public TestScenarioNewPage(WebDriver driver) {
		super(driver);
	}

	public TestScenarioNewPage(WebDriver driver, Project project) {
		super(driver, project);
	}
	
	public TestScenarioViewPage createNewScenarioShort(TestScenario testScenario, TestScenario parentPage)
	{
		clickMainTab();
		captionEdit.sendKeys(testScenario.getName());

		setParentPageUID(parentPage.getId());		
		submitDialog(submitBtn);
		
		By locator = By.xpath("//td[@id='caption' and contains(.,'"+testScenario.getName()+"')]/preceding-sibling::td[@id='uid']");
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(locator));

		String uid =driver.findElement(locator).getText();
		testScenario.setId(uid.substring(1, uid.length()-1));
		return clickToTestScenario(testScenario.getId());
	}
	
	public TestScenarioViewPage createNewScenarioShort(TestScenario testScenario, String parentPageName)
	{
		clickMainTab();
		captionEdit.sendKeys(testScenario.getName());

		setParentPageText(parentPageName);		
		submitDialog(submitBtn);
		
		By locator = By.xpath("//td[@id='caption' and contains(.,'"+testScenario.getName()+"')]/preceding-sibling::td[@id='uid']");
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(locator));

		String uid =driver.findElement(locator).getText();
		testScenario.setId(uid.substring(1, uid.length()-1));
		return clickToTestScenario(testScenario.getId());
	}
        
    public void createNewScenarioWithTemplate(TestScenario testScenario)
	{
		clickMainTab();
		captionEdit.sendKeys(testScenario.getName());
		if(testScenario.getTemplate() != null) {
			CKEditor editor = new CKEditor(driver);
			editor.typeTemplate(testScenario.getTemplate());
		}
		submitDialog(submitBtn);
		sleep(Configuration.getPersistTimeout());
	}
        
    public void createNewScenario(TestScenario testScenario)
	{
		clickMainTab();
		captionEdit.sendKeys(testScenario.getName());
		if(testScenario.getContent() != null) {
			CKEditor editor = new CKEditor(driver);
			editor.typeText(testScenario.getContent());
		}
		setParentPage(testScenario);
		submitDialog(submitBtn);
	}

	public void addRequirement(String requirement, String version)
	{
		clickTraceTab();
		driver.findElement(By.xpath("//span[@name='WikiPageRequirement']//a[contains(@class,'embedded-add-button')]")).click();
		WebElement requirementInput = driver.findElement(By.xpath("//div[@id='fieldRowRequirement']//input[contains(@id,'SourcePageText')]"));
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(requirementInput));
		requirementInput.sendKeys(requirement);
		autocompleteSelect(requirement);
		
	    if (!"".equals(version)) {
	    	(new Select(driver.findElement(By.xpath("//div[@id='fieldRowRequirement']//select[contains(@id,'SourcePageSourceBaseline')]")))).selectByVisibleText(version);
	    }
		driver.findElement(By.xpath("//span[@name='WikiPageRequirement']//input[@value='Добавить']")).click();
		
	}

	public TestScenarioViewPage clickToTestScenario(String id) {
		driver.findElement(
				By.xpath("//tr[contains(@id,'testingdocslist1_row')]/td[@id='uid']/a[contains(@href,'"
						+ id + "')]")).click();
		return new TestScenarioViewPage(driver);
	}
	
	public void clickMoreTab()
	{
		clickTab("additional");
	}
	
	public void clickMainTab()
	{
		clickTab("main");
	}
	
	public void clickTraceTab()
	{
		clickTab("trace");
	}

    public void createNewScenarioWithTestPlan(TestScenario testScenary) {
        clickMainTab();
		captionEdit.sendKeys(testScenary.getName());
        
        WebElement textFrame = driver.findElement(By
		.xpath(".//div[@role='presentation']/iframe"));
		driver.switchTo().frame(textFrame);
        driver.findElement(By.xpath("html/body/table/tbody/tr[1]/td[2]")).sendKeys("действие1");
        driver.switchTo().defaultContent();

        setParentPage(testScenary);
		submitDialog(submitBtn);
		
		By locator = By.xpath("//td[@id='caption' and contains(.,'"+testScenary.getName()+"')]/preceding-sibling::td[@id='uid']");
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(locator));

		String uid =driver.findElement(locator).getText();
		testScenary.setId(uid.substring(1, uid.length()-1));
		//return clickToTestScenario(testScenary.getId());
    }
    
    public void createScenarioWithNewTestPlan(TestScenario scenario)
    {
        clickMainTab();
        captionEdit.sendKeys(scenario.getName());
        
        setParentPage(scenario);
        submitDialog(submitBtn);
    }
    
    public void createScenarioWithTable(TestScenario scenario, ArrayList<String> table)
    {
        clickMainTab();
        captionEdit.sendKeys(scenario.getName());
        
    	if ( Configuration.robotPointerUsed() ) {
    		CKEditor editor = new CKEditor(driver);
    		editor.fillCell("1","2", table.get(0));
    		editor.fillCell("2","2", table.get(1));
    		editor.fillCell("3","2", table.get(2));
    		editor.fillCell("1","3", table.get(3));
    		editor.fillCell("2","3", table.get(4));
    		editor.fillCell("3","3", table.get(5));
    	}

        setParentPage(scenario);
        submitDialog(submitBtn);
    }
    
    protected void setParentPage( TestScenario scenario ) {
    	if ( scenario.getParentPage() == null ) return;
        String parentName = scenario.getParentPage().getName();
        if ( parentName.isEmpty() ) return;
        setParentPageText(parentName);
    }

    protected void setParentPageText( String name ) {
        if ( name.isEmpty() ) return;
    	if ( !parentPageInput.isDisplayed() ) {
        	clickMoreTab();
    	}
        parentPageInput.clear();
        parentPageInput.sendKeys(name);
    }
    
    protected void setParentPageUID( String uid ) {
    	if ( !parentPageInput.isDisplayed() ) {
        	clickMoreTab();
    	}
        parentPageInput.clear();
        parentPageInput.sendKeys(uid);
        autocompleteSelect(uid);
    }
}
