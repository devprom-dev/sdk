package ru.devprom.pages.project.testscenarios;

import org.openqa.selenium.By;
import org.openqa.selenium.Keys;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.items.TestScenario;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.kanban.KanbanTaskNewPage;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requests.RequestDonePage;
import ru.devprom.pages.project.tasks.TaskViewPage;
import ru.devprom.pages.project.tasks.WriteOfTimePage;
import ru.devprom.pages.scrum.ScrumTaskNewPage;

public class TestScenarioTestingPage extends SDLCPojectPageBase {
    
    @FindBy(xpath = "//a[contains(@id,'workflow') and contains(.,'Отклонить')]")
	protected WebElement rejectBtn;
        
    @FindBy(xpath = "//a[@id='workflow-development']")
	protected WebElement issueReadyBtn;

    @FindBy(xpath = "//a[contains(@class,'btn-warning') and contains(.,'Состояние')]")
	protected WebElement workflowButton;
        
        //кнопка сохранить комментарий
        @FindBy(xpath = "//*[@id='pm_ChangeRequestSubmitBtn']")
	protected WebElement saveBtn;
        
        //поле имени ошибки
        @FindBy(xpath = "//*[@id='pm_ChangeRequestCaption']")
	protected WebElement bugNameField;
        
        @FindBy(xpath = "//div[@class='filter-actions']//a[contains(@data-toggle,'dropdown')]")
    	protected WebElement statesBtn;

        //кнопка готово
    @FindBy(xpath = "//div[@class='filter-actions']//a[contains(text(),'Готово')]")
	protected WebElement readyBtn;
        
    //кнопка Ошибка+
    @FindBy(xpath = "//td[@id='attributes']//a[contains(.,'Ошибка') and contains(@class,'btn')]")
	protected WebElement addBugBtn;
        
          //добавить затраченное время на форма перехода для требования
    @FindBy(xpath = "//span[@id='pm_ChangeRequestFact']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addTimeReqBtn;  
        
        //поле добавления времени на форме перехода для требования
    @FindBy(xpath = "//span[@id='pm_ChangeRequestFact']//input[contains(@name,'_Capacity')]")
	protected WebElement addTimeReqField;  
        
        //кнопка добавить время после ввода на форме  перехода
    @FindBy(xpath = "//span[@id='pm_ChangeRequestFact']//input[contains(@id,'saveEmbedded')]")
	protected WebElement saveAddedTimeReq;
       
        //кнопка сохранить время после ввода на форме  перехода для требования
        @FindBy(xpath=".//*[@id='pm_ChangeRequestSubmitBtn']")
	protected WebElement submitAddedTimeReq;
        
        @FindBy(xpath="(//a[contains(text(),'Списать время')])[1]")
	protected WebElement markTimeItem;
        
        @FindBy(xpath="//a[@id='new-task']")
	protected WebElement createTask;
        
        @FindBy(xpath="//*[@class='btn-group pull-left']//*[@id='workflow-testingready']")
	protected WebElement ready;

	public TestScenarioTestingPage(WebDriver driver) {
		super(driver);
	}

	public TestScenarioTestingPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public TestScenarioTestingPage failTest(TestScenario testScenario) {
		driver.findElement(By.xpath("//td[@id='attributes']//a[text()='Провален']")).click();
		try {
			Thread.sleep(1500);
		} catch (InterruptedException e) {
		}
		return new TestScenarioTestingPage(driver);
	}

	public TestScenarioTestingPage passTest(TestScenario testScenario) {
		By locator = By.xpath("//td[@id='attributes']//a[text()='Пройден']");
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(locator));
		driver.findElement(locator).click();
		try {
			Thread.sleep(1500);
		} catch (InterruptedException e) {
		}
		return new TestScenarioTestingPage(driver);
	}

	public String getTestRunId(){
		String text = driver.findElement(By.xpath("//ul[contains(@class,'breadcrumb')]//button")).getText().trim();
		return text.substring(2,text.length());
	}

    public void rejectWish(String comment) {
    	workflowButton.click();
    	rejectBtn.click();
    	waitForDialog();
        (new CKEditor(driver)).typeText(comment);
        submitDialog(saveBtn);
    }

    public void readyWish(String id, String time) {
        try
        {
            Thread.sleep(3000);
            workflowButton.click();
            readyBtn.click();
	        (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(addTimeReqBtn));
	         addTimeReqBtn.click();
	         (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(addTimeReqField));
	         addTimeReqField.sendKeys(time);
	        saveAddedTimeReq.click();
	        submitDialog(submitAddedTimeReq);
        }
        catch(InterruptedException e)
        {}
    }

    public KanbanTaskNewPage createNewBug() {
        addBugBtn.click();
        waitForDialog();
        return new KanbanTaskNewPage(driver);
    }

    public RequestDonePage readyWishWithOutTime() throws InterruptedException {
        Thread.sleep(1000);
        workflowButton.click();
        issueReadyBtn.click();
        waitForDialog();
        (new CKEditor(driver)).typeText("-");
        return new RequestDonePage(driver);
    }

    public void gotoScenarioNumber(int i) throws InterruptedException {
        Thread.sleep(2000);
        driver.findElement(By.xpath(".//*[@class='pagination']//li[2]/a")).click();
    }

    public void fillCell(String row, String coloumn, String text) {
    	try {
			Thread.sleep(1000);
		} catch (InterruptedException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
    	By editableLocator = By.xpath("//div[contains(@id,'pm_TestCaseExecutionContent') and @contenteditable='true']");
    	(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(editableLocator));
        WebElement editableArea = driver.findElement(editableLocator);
        WebElement cell = editableArea.findElement(By.xpath(".//table/tbody/tr["+row+"]/td["+coloumn+"]"));
        cell.click();
        editableArea.sendKeys(text);
    }
    
    public void pasteToCell(String row, String coloumn) {
     	By editableLocator = By.xpath("//div[contains(@id,'pm_TestCaseExecutionContent') and @contenteditable='true']");
    	(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(editableLocator));
        WebElement editableArea = driver.findElement(editableLocator);
        WebElement cell = editableArea.findElement(By.xpath(".//table/tbody/tr["+row+"]/td["+coloumn+"]"));
        cell.click();
        editableArea.sendKeys(Keys.LEFT_CONTROL + "v");
    }
     
    public ScrumTaskNewPage createTask() {
        createTask.click();
    	waitForDialog();
        return new ScrumTaskNewPage(driver);
    }
     
    public WriteOfTimePage writeOfTime() {
	    driver.findElement(By.xpath("//div[contains(@id,'embeddedItems')]//*[contains(@class,'title')]")).click();
	    clickOnInvisibleElement(markTimeItem);
	    return new WriteOfTimePage(driver);
    }

    public TaskViewPage openTask(String id) {
    	driver.findElement(By.xpath("//a[contains(@class,'with-tooltip') and contains(.,'"+id+"')]")).click();
    	return new TaskViewPage(driver);
    }
}