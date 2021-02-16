package ru.devprom.pages.project.testscenarios;

import java.util.logging.Level;
import java.util.logging.Logger;
import org.openqa.selenium.By;
import org.openqa.selenium.Keys;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requests.RequestViewPage;

public class TestScenariosPage extends SDLCPojectPageBase {

    @FindBy(xpath = ".//*[@id='bulk-actions']/a")
	protected WebElement moreBtn;

    @FindBy(xpath = "//a[@id='create-scenario']")
	protected WebElement addNewTestScenarioBtn;
	
	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains (text(),'Действия')]")
	protected WebElement actionsBtn;
	
	@FindBy(xpath = "//a[text()='Включить в тест-план']")
	protected WebElement includeToTestPlanItem;

	@FindBy(xpath = "//a[@uid='state']")
	protected WebElement statusFilterBtn;
        
        //чекбокс выделить все
        @FindBy(xpath = "//div[contains(@class,'wishes')]//*[@class='for-chk visible']/input")
	protected WebElement checkAllChBx;


	public TestScenariosPage(WebDriver driver) {
		super(driver);
	}

	public TestScenariosPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public TestScenarioNewPage clickNewTestScenario() {
		clickOnInvisibleElement(addNewTestScenarioBtn);
		return new TestScenarioNewPage(driver);
	}
	
	
	public boolean isNotification(String testScenarioId){
		return driver.findElements(By.xpath("//td[@id='uid']/a[contains(@href,'"+testScenarioId+"')]/../following-sibling::td[@id='caption']/a/img")).size()>0;
	}

	public void checkTestScenario(String id){
		driver.findElement(By.xpath("//tr[contains(@id,'testingdocslist1_row_')]/td[@id='uid']/a[contains(@href,'"
						+ id + "')]/../preceding-sibling::td/input[contains(@class,'checkbox')]")).click();
	}

	public TestScenarioViewPage clickToTestScenario(String id) {
			driver.findElement(
					By.xpath("//tr[contains(@id,'testingdocslist1_row_')]/td[@id='uid']/a[contains(@href,'"
							+ id + "')]")).click();
		return new TestScenarioViewPage(driver);
	}

	public TestScenarioViewPage massIncludeToTestPlan(String parentTestPlan){
		clickOnInvisibleElement(moreBtn);
		clickOnInvisibleElement(includeToTestPlanItem);
		new WebDriverWait(driver,waiting).until(ExpectedConditions.presenceOfElementLocated(By.id("ParentPageText")));
		driver.findElement(By.id("ParentPageText")).sendKeys(parentTestPlan);
		autocompleteSelect(parentTestPlan);
		submitDialog(driver.findElement(By.id("WikiPageSubmitBtn")));
		return new TestScenarioViewPage(driver);
	}
        
    public TestScenariosPage massIncludeToNewTestPlan(String parentTestPlan){
    	clickOnInvisibleElement(moreBtn);
		clickOnInvisibleElement(includeToTestPlanItem);
		new WebDriverWait(driver,waiting).until(ExpectedConditions.presenceOfElementLocated(By.id("ParentPageText")));
		WebElement parentPage = driver.findElement(By.id("ParentPageText"));
		parentPage.sendKeys(parentTestPlan);
		parentPage.sendKeys(Keys.TAB);
		submitDialog(driver.findElement(By.id("WikiPageSubmitBtn")));
		return new TestScenariosPage(driver);
	}

    public TestSpecificationsPage clickAttention() {
    	WebElement attentionBtn = driver.findElement(By.xpath("//*[@class='trace-state']/.."));
        (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(attentionBtn));
    	(new Actions(driver)).click(attentionBtn).build().perform();
        return new TestSpecificationsPage(driver);
    }

    public void checkAll() {
        clickOnInvisibleElement(checkAllChBx);
    }

    public String getIDByName(String name) {
        String ids;
        ids = driver.findElement(By.xpath("//tr[contains(@id,'testingdocslist1_row_')]/td[@id='caption' and contains(.,'"+
                name+"')]/preceding-sibling::td[@id='uid']")).getText();
        String id = ids.substring(1, ids.length()-1);
        FILELOG.debug("Click to UID of requirement");
        return id; 
    }

	public boolean isScenariosPresent(String name) {
		return !driver.findElements(By.xpath("//td[@id='content']//div[contains(.,'"+name+"')]")).isEmpty();
	}

	public boolean isScenarioPresent(String name) {
		return driver.findElements(By.xpath("//*[@id='tablePlaceholder']//div[contains(.,'"+name+"')]")).size() > 0;
	}
}