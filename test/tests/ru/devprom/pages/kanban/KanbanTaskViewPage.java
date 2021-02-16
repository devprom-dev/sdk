package ru.devprom.pages.kanban;

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
import ru.devprom.pages.project.requirements.RequirementNewPage;
import ru.devprom.pages.project.requirements.RequirementViewPage;
import ru.devprom.pages.project.tasks.TaskCompletePage;
import ru.devprom.pages.project.tasks.TaskViewPage;
import ru.devprom.pages.project.tasks.TasksPage;
import ru.devprom.pages.project.testscenarios.TestScenarioNewPage;
import ru.devprom.pages.project.testscenarios.TestScenarioTestingPage;

public class KanbanTaskViewPage extends KanbanPageBase {

	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(.,'Действия')]")
	protected WebElement actionsBtn;

	//кнопка действия
	@FindBy(xpath = "//a[contains(.,'Действия')]")
	protected WebElement actionBtn;
        
	@FindBy(id = "pm_ChangeRequestSubmitBtn")
	protected WebElement submitBtn;

	// пункт Удалить в меню Действия
	@FindBy(id = "row-delete")
	protected WebElement deleteBtn;

	//добавить затраченное время на форма перехода к статусу Анализ:готово
    @FindBy(xpath = "//div[@id='modal-form']//span[@name='pm_ChangeRequestFact']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addTimeBtn;        
        
    //поле добавления времени на форме перехода к статусу Анализ:готово
    @FindBy(xpath = "//div[@id='modal-form']//span[@name='pm_ChangeRequestFact']//input[contains(@id,'Capacity')]")
	protected WebElement addTimeField;   
        
    //кнопка добавить время после ввода на форме  перехода к статусу Анализ:готово
    @FindBy(xpath="//div[@id='modal-form']//span[@name='pm_ChangeRequestFact']//input[contains(@id,'saveEmbedded')]")
	protected WebElement saveAddedTime;  
                
	@FindBy(xpath = "//a[@id='workflow-resolved']")
	protected WebElement completeBtn;
        
        @FindBy(xpath = "//a[@id='workflow-analysisready']")
	protected WebElement analyseCompleteBtn;
        
        //разработка
        @FindBy(xpath = "//a[@id='workflow-development']")
	protected WebElement developmentBtn;

        //пункт начать тестировани меню действия
        @FindBy(xpath = ".//*[@id='run-test']")
	protected WebElement startTestingItem;
        
        //разработка готова
         @FindBy(xpath = ".//*[@id='workflow-developmentready']")
	protected WebElement developmentCompleteBtn;
        
        @FindBy(id = "pm_ChangeRequestSubmitBtn")
	protected WebElement changeRequestSubmitBtn;

        // кнопка Изменить
	@FindBy(xpath = "//a[@id='modify']")
	protected WebElement editBtn;
	
	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(.,'Действия')]/..//a[@id='new-task']")
	protected WebElement addSubtaskBtn;

	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(.,'Действия')]/..//a[@id='as-template']")
	protected WebElement saveTemplateBtn;
	
	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(.,'Действия')]/..//a[text()='Реализовать в проекте']")
	protected WebElement duplicateBtn;
        
        //подменю Тестовый сценарий пункта Создать меню Действие
	@FindBy(xpath = "//a[contains(@class,'new-at-form') and @id='testscenario']")
	protected WebElement createScenarioItem;
        
        //подменю Требование пункта Создать меню Действие
	@FindBy(xpath = "//*[@class='btn-group operation last open']//*[contains(.,'Требование')]")
	protected WebElement createRequirementItem;
        
        //пункт Создать меню Действие
	@FindBy(xpath = "//*[@class='btn-group operation last open']//*[contains(.,'Создать')]")
	protected WebElement createItem;
	
	@FindBy(xpath = "//div[@class='accordion-heading']/a[contains(.,'Свойства')]")
	protected WebElement propertiesField;
	
	@FindBy(xpath = "//div[@class='accordion-heading']/a[contains(.,'Описание')]")
	protected WebElement descriptionField;
	
	@FindBy(xpath = "//table[@class='properties-table']//tr/th[contains(.,'Приоритет:')]/following-sibling::td")
	protected WebElement priorityLabel;
	
	@FindBy(xpath = "//table[@class='properties-table']//tr/th[contains(.,'Автор:')]/following-sibling::td")
	protected WebElement authorLabel;

	@FindBy(xpath = "//table[@class='properties-table']//tr/th[contains(.,'Номер:')]/following-sibling::td")
	protected WebElement numberLabel;
	
	@FindBy(xpath = "//table[@class='properties-table']//tr/th[contains(.,'Состояние:')]/following-sibling::td/span")
	protected WebElement stateLabel;
	
	@FindBy(xpath = "//table[@class='properties-table']//tr/th[contains(.,'Исполнитель:')]/following-sibling::td")
	protected WebElement ownerLabel;
	
	
	public KanbanTaskViewPage(WebDriver driver) {
		super(driver);
	}

	public KanbanTaskViewPage(WebDriver driver, Project project) {
		super(driver, project);
	}
	
	
	public KanbanTaskEditPage editTask() {
		actionsBtn.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(editBtn));
		editBtn.click();
		return new KanbanTaskEditPage(driver);
	}
	
	public KanbanTaskViewPage saveTemplate(String templateName){
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(actionsBtn));
		actionsBtn.click();
		clickOnInvisibleElement(saveTemplateBtn);
		waitForDialog();
		WebElement caption = driver.findElement(By.xpath("//div[@id='modal-form']//input[@id='pm_ChangeRequestCaption']"));
		caption.clear();
		caption.sendKeys(templateName);
		submitDialog(driver.findElement(By.id("cms_SnapshotSubmitBtn")));	
		return new KanbanTaskViewPage(driver);
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
	
	public String readPriority() {
		if (!priorityLabel.isDisplayed())
			propertiesField.click();
		return priorityLabel.getText().trim();
	}
	
	public String readAuthor() {
		if (!authorLabel.isDisplayed())
			propertiesField.click();
		return authorLabel.getText().trim();
	}
	
	public String readNumber() {
		if (!numberLabel.isDisplayed())
			propertiesField.click();
		return numberLabel.getText().trim();
	}
	
	public String readOwner() {
		if (!ownerLabel.isDisplayed())
			propertiesField.click();
		return ownerLabel.getText().trim();
	}
	
	public String readState() {
		if (!stateLabel.isDisplayed())
			propertiesField.click();
		return stateLabel.getText().trim();
	}
	
	public String readDescription()
	{
		((JavascriptExecutor) driver).executeScript("document.evaluate(\"//div[@id='collapseTwo']\", document, null, 9, null).singleNodeValue.removeAttribute('class')");
		return driver.findElement(By.xpath("//div[contains(@id,'pm_ChangeRequestDescription') and contains(@class,'wysiwyg-text')]")).getText().trim();
	}

	protected WebElement findSubTask( String name ) 
	{
		List<WebElement> items = driver.findElements(
			By.xpath("//input[@value='task']/following-sibling::div[contains(@id,'embeddedItems')]//*[contains(@class,'title') and contains(.,'"+name+"')]"));
		return items.get(items.size() - 1);
	}
	
	public KanbanTaskExecutePage executeSubtask(String name)
	{
		openTasksSection();
		WebElement executeBtn = findSubTask(name).findElement(By.xpath("./following-sibling::ul//a[text()='Выполнить']"));
		clickOnInvisibleElement(executeBtn);
		return new KanbanTaskExecutePage(driver);
	}

	public TasksPage openRelatedTasksList()
	{
		WebElement link = driver.findElement(By.xpath("//a[contains(@class,'embedded-add-button') and contains(@class,'items-list')]"));
		clickOnInvisibleElement(link);
		return new TasksPage(driver);
	}

	public void openTasksSection()
	{
		WebElement section = driver.findElement(By.xpath("//a[contains(@href,'collapseThree')]"));
		if ( section.isDisplayed() ) {
			section.click();
			(new WebDriverWait(driver, 5)).until(ExpectedConditions
					.visibilityOf(driver.findElement(By.id("collapseThree"))));
		}
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
	
	public String duplicateInProject(String projectName){
		actionsBtn.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(duplicateBtn));
		duplicateBtn.click();
		driver.findElement(By.id("ProjectText")).sendKeys(projectName);
		autocompleteSelect(projectName);
		submitDialog(changeRequestSubmitBtn);
		return requestReadLinkedIdOnPage();
	}

    public void doAnalyseComplete(String time) {
        try
        {
        (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(analyseCompleteBtn));
        analyseCompleteBtn.click();        
        (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(changeRequestSubmitBtn));
        addTimeBtn.click();
        (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(addTimeField));
        addTimeField.sendKeys(time);
        saveAddedTime.click();
        submitDialog(changeRequestSubmitBtn);
        Thread.sleep(3000);
    }
        catch(InterruptedException e)
        {
        }
    }

    public RequirementViewPage openRequirement(String name) 
    {
    	openTracesSection();
        List<WebElement> list = driver.findElements(
        		By.xpath("//div[contains(@id,'embeddedItems')]//*[contains(@class,'title') and contains(.,'"+name+"')]")
        	);
		for (int i = 0; i < list.size(); i++) {
			if ( list.get(i).isDisplayed() ) {
				list.get(i).click();
				break;
			}
		}
		clickOnInvisibleElement(driver.findElement(By.id("open-form")));
        return new RequirementViewPage(driver);
    }

    public void doDevelopment(KanbanTask task1, KanbanTask task2) {
        (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(developmentBtn));
        developmentBtn.click();
        try {
            Thread.sleep(3000);
        }
        catch(InterruptedException e) {}
    }

    public void doDevelopmentComplete() {
        (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(developmentCompleteBtn));
        developmentCompleteBtn.click();
        (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(changeRequestSubmitBtn));
        submitDialog(changeRequestSubmitBtn);
    }

    public TestScenarioTestingPage doStartTesting(String version) {
        clickOnInvisibleElement(actionBtn);
        clickOnInvisibleElement(startTestingItem);
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(versionField));
        versionField.sendKeys(version);
        autocompleteSelect(version);
        submitDialog(saveTestingBtn);
        return new TestScenarioTestingPage(driver);
        
    }

    public TestScenarioNewPage clickActionCreateScenario() {
        clickOnInvisibleElement(createScenarioItem);
        return new TestScenarioNewPage(driver);
    }
    
     public RequirementNewPage clickActionCreateRequirement() {
    	clickOnInvisibleElement(actionBtn);
        clickOnInvisibleElement(createItem);
        clickOnInvisibleElement(createRequirementItem);
        return new RequirementNewPage(driver);
    }
     
     public String getIdRequirement (String name){
         String id = driver.findElement(By.xpath(".//*[@name='Requirement']//*[contains(@class,'title') and contains(.,"
                 + "'Студенты и преподаватели')]/a")).getText();
         String clearID = id.substring(1, id.length()-1);
         FILELOG.debug("Requirement ID = " + clearID);
         return clearID;
     }

    public void completeTask() {
        clickOnInvisibleElement(completeBtn);
        waitForDialog();
    	submitDialog(submitBtn);
    }

    public KanbanAddSubtaskPage actionAddSubtask(){
		actionsBtn.click();
		clickOnInvisibleElement(addSubtaskBtn);
		return new KanbanAddSubtaskPage(driver);
	}

	public void deleteIssue(){
		actionBtn.click();
		try {
			Thread.sleep(3000);
		}
		catch (InterruptedException e){}
	    clickOnInvisibleElement(deleteBtn);
	}
}
