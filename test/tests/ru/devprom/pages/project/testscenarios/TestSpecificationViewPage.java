package ru.devprom.pages.project.testscenarios;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Action;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.helpers.DataProviders;
import ru.devprom.items.Project;
import ru.devprom.items.TestScenario;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requirements.RequirementViewPage;

public class TestSpecificationViewPage extends TestScenarioViewPage {

	
	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(@class, 'actions-button')]")
	protected WebElement asterixBtn;

	@FindBy(xpath = "//a[@id='append-child-page-scenario']")
	protected WebElement addSectionBtn;
        
    @FindBy(xpath = "//a[@id='new-scenario']")
	protected WebElement insirtSectionBtn;
	
	@FindBy(xpath = "//a[@uid='compareto']")
	protected WebElement compareWithBtn;
	
	@FindBy(xpath = "//a[@uid='baseline' and contains(@class,'dropdown-toggle')]")
	protected WebElement versionBtn;
	
	@FindBy(xpath = "//a[@id='reintegrate']")
	protected WebElement copySectionBtn;
	
	@FindBy(xpath = "//a[@id='reintegrate']/../../preceding-sibling::a")
	protected WebElement processChangesBtn;
        
         //кнопка со звездочкой корневого документа
          @FindBy(xpath = ".//*[@id='pmwikidocumentlist1_row_1']//*[@class='icon-asterisk icon-gray']")
	protected WebElement asterixRootBtn;
         
         //кнопка Состояние
         @FindBy(xpath = ".//*[@uid='state']")
	protected WebElement stateBtn;
         
         //пункт готово к тестированию меню Состояние
         @FindBy(xpath = ".//*[contains(.,'Готово к тестированию')]")
	protected WebElement readyForTestingItem;
         
         //пункт готово к тестированию меню кнопки со звездочкой
         @FindBy(xpath = "//*[@id='pmwikidocumentlist1_row_1']//*[@id='workflow-ready']")
	protected WebElement readyForTestingStatusItem;
         
	
	public TestSpecificationViewPage(WebDriver driver) {
		super(driver);
	}

	public TestScenarioNewPage addSection(){
		clickOnInvisibleElement(addSectionBtn);
		return new TestScenarioNewPage(driver);
	}
	
	public TestSpecificationViewPage showBaseline(String version){
		clickOnInvisibleElement(versionBtn);
		clickOnInvisibleElement(versionBtn.findElement(By.xpath("./following-sibling::ul//a[contains(.,'"+version+"')]")));
		return new TestSpecificationViewPage(driver);
	}
	
	public TestSpecificationViewPage compareWithVersion(String version)
	{
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.elementToBeClickable(compareWithBtn));
		clickOnInvisibleElement(compareWithBtn);
		WebElement compareRow = compareWithBtn.findElement(By.xpath("./following-sibling::ul//a[contains(.,'"+version+"')]"));
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(compareRow));
		compareRow.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.presenceOfElementLocated(
				By.xpath("//a[@uid='compareto' and contains(.,'"+version.substring(0, Math.min(20, version.length()))+"')]")));
		return new TestSpecificationViewPage(driver);
	}
	
	public boolean isAlertPresent(){
		return !driver.findElements(By.xpath("//td[@id='content']//a[@uid='compare-actions']")).isEmpty();
	}
	
	public TestSpecificationViewPage copySection() {
		clickOnInvisibleElement(copySectionBtn);
		waitForDialog();
		submitDialog(driver.findElement(By.id("SubmitBtn")));
		return new TestSpecificationViewPage(driver);
	}
	
	public TestScenarioViewPage addNewTestScenario(TestScenario testScenario)
	{
		clickOnInvisibleElement(insirtSectionBtn);
		try {
			Thread.sleep(2000);
		} catch (InterruptedException e) {
		}
		By xpath = By.xpath("//div[contains(@class,'wysiwyg-text') and contains(.,'<Тестовый')]");
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.presenceOfElementLocated(xpath));
		
		WebElement titleElement = driver.findElement(xpath);
		String uid = "S-" + titleElement.getAttribute("objectid");
		testScenario.setId(uid);
		titleElement.click();
		titleElement.clear();
		titleElement.sendKeys(testScenario.getName());
		try {
			Thread.sleep(2000);
		} catch (InterruptedException e) {
		}
		return new TestScenarioViewPage(driver);
	}

    public TestScenarioNewPage insirtNewSection() {
        int count = driver.findElements(By.xpath(".//tr[contains(@id,'pmwikidocumentlist1_row_')]")).size();
        if(count>1)count--;
        FILELOG.debug("Count = " + count);
        clickOnInvisibleElement(driver.findElement(By.xpath(".//*[@id='pmwikidocumentlist1_row_"+count+"']")));
        clickOnInvisibleElement(insirtSectionBtn);
        return new TestScenarioNewPage(driver);
    }
    
    public void readyForTesting() {
        clickOnInvisibleElement(asterixRootBtn);
        clickOnInvisibleElement(readyForTestingStatusItem);
    }

    public TestScenarioNewPage addSubSectionTo(String id) {
        WebElement element = driver.findElement(By.xpath("//tr[contains(@id,'pmwikidocumentlist1_row_') and @object-id='"+id+"']"));
        clickOnInvisibleElement(element);
        clickOnInvisibleElement(element.findElement(By.xpath(".//*[@class='dashed' and contains(.,'новый сценарий')]")));
        return new TestScenarioNewPage(driver);
    }
    
    public TestScenarioNewPage addSubSectionTo() {
        WebElement element = driver.findElement(By.xpath("//tr[contains(@id,'pmwikidocumentlist1_row_2')]"));
        clickOnInvisibleElement(element);
        clickOnInvisibleElement(element.findElement(By.xpath("//*[@class='dashed' and contains(.,'новый сценарий')]")));
        return new TestScenarioNewPage(driver);
    }

    public void gotoRoot() {
        driver.findElement(By.xpath("//*[@id='wikitree']/li/div[2]/a/span")).click();
    }
	
}
