package ru.devprom.pages.kanban;

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
import ru.devprom.pages.project.testscenarios.TestScenarioNewPage;
import ru.devprom.pages.project.testscenarios.TestScenarioTestingPage;

public class KanbanTaskViewPage extends KanbanPageBase {

	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(text(),'Действия')]")
	protected WebElement actionsBtn;
        
        //добавить затраченное время на форма перехода к статусу Анализ:готово
        @FindBy(css = "#pm_ChangeRequestFact > div.embedded_form.form-inline > a.dashed.embedded-add-button")
	protected WebElement addTimeBtn;        
        
    //поле добавления времени на форме перехода к статусу Анализ:готово
    @FindBy(xpath = "//div[@id='modal-form']//span[@id='pm_ChangeRequestFact']//input[contains(@id,'Capacity')]")
	protected WebElement addTimeField;   
        
    //кнопка добавить время после ввода на форме  перехода к статусу Анализ:готово
    @FindBy(xpath="//div[@id='modal-form']//span[@id='pm_ChangeRequestFact']//input[contains(@id,'saveEmbedded')]")
	protected WebElement saveAddedTime;  
                
	@FindBy(xpath = "//a[@id='workflow-resolved']")
	protected WebElement completeBtn;
        
        @FindBy(xpath = "//a[@id='workflow-analysisready']")
	protected WebElement analyseCompleteBtn;
        
        //разработка
        @FindBy(xpath = "//a[@id='workflow-development']")
	protected WebElement developmentBtn;
        
        //кнопа действия
        @FindBy(xpath = "//a[contains(text(),'Действия')]")
	protected WebElement actionBtn;
        
        //пункт начать тестировани меню действия
        @FindBy(xpath = ".//*[@id='run-test']")
	protected WebElement startTestingItem;
        
        //разработка готова
         @FindBy(xpath = ".//*[@id='workflow-developmentready']")
	protected WebElement developmentCompleteBtn;
        
        @FindBy(id = "pm_ChangeRequestSubmitBtn")
	protected WebElement changeRequestSubmitBtn;
	
	@FindBy(xpath = "//a[@id='modify']")
	protected WebElement editBtn;
	
	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(text(),'Действия')]/..//a[@id='new-task']")
	protected WebElement addSubtaskBtn;

	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(text(),'Действия')]/..//a[@id='as-template']")
	protected WebElement saveTemplateBtn;
	
	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(text(),'Действия')]/..//a[text()='Реализовать в проекте']")
	protected WebElement duplicateBtn;
        
        //подменю Тестовый сценарий пункта Создать меню Действие
	@FindBy(xpath = "//a[contains(@class,'new-at-form') and @id='testscenario']")
	protected WebElement createScenarioItem;
        
        //подменю Требование пункта Создать меню Действие
	@FindBy(xpath = "//*[@class='btn-group operation last open']//*[contains(text(),'Требование')]")
	protected WebElement createRequirementItem;
        
        //пункт Создать меню Действие
	@FindBy(xpath = "//*[@class='btn-group operation last open']//*[contains(text(),'Создать')]")
	protected WebElement createItem;
	
	@FindBy(xpath = "//div[@class='accordion-heading']/a[contains(.,'Свойства')]")
	protected WebElement propertiesField;
	
	@FindBy(xpath = "//div[@class='accordion-heading']/a[contains(.,'Описание')]")
	protected WebElement descriptionField;
	
	@FindBy(xpath = "//table[@class='properties-table']/tbody/tr/th[contains(text(),'Приоритет:')]/following-sibling::td")
	protected WebElement priorityLabel;
	
	@FindBy(xpath = "//table[@class='properties-table']/tbody/tr/th[contains(text(),'Автор:')]/following-sibling::td")
	protected WebElement authorLabel;

	@FindBy(xpath = "//table[@class='properties-table']/tbody/tr/th[contains(text(),'Номер:')]/following-sibling::td")
	protected WebElement numberLabel;
	
	@FindBy(xpath = "//table[@class='properties-table']/tbody/tr/th[contains(text(),'Состояние:')]/following-sibling::td/span")
	protected WebElement stateLabel;
	
	@FindBy(xpath = "//table[@class='properties-table']/tbody/tr/th[contains(text(),'Исполнитель:')]/following-sibling::td")
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
		driver.findElement(By.id("cms_SnapshotCaption")).sendKeys(templateName);
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
		return driver.findElement(By.xpath("//div[contains(@id,'pm_ChangeRequestDescription')]")).getText().trim();
	}

	protected WebElement findSubTask( String name ) {
		return driver.findElement(
				By.xpath("//input[@value='task']/following-sibling::div[contains(@id,'embeddedItems')]//*[contains(@class,'title') and contains(.,'"+name+"')]"));
	}
	
	public String getSubTaskState( String name ) {
		WebElement subtaskElement = findSubTask(name);
		return subtaskElement.findElement(By.xpath("./span[contains(@class,'label')]")).getText();
	}
	
	public KanbanTaskExecutePage executeSubtask(String name) {
		WebElement executeBtn = findSubTask(name).findElement(By.xpath("./following-sibling::ul//a[text()='Выполнить']"));
		clickOnInvisibleElement(executeBtn);
		return new KanbanTaskExecutePage(driver);
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

    public RequirementViewPage openRequirement(String name) {
        driver.findElement(By.xpath("//div[contains(@id,'embeddedItems')]//*[contains(@class,'title') and contains(.,'"+name+"')]")).click();
        WebElement menuItem = driver.findElement(By.id("show-in-document"));
        (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(menuItem));
        menuItem.click();
        return new RequirementViewPage(driver);
    }

    public void doDevelopment(KanbanTask task1, KanbanTask task2) {
        (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(developmentBtn));
        developmentBtn.click();
        (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(changeRequestSubmitBtn));
        WebElement task1field = driver.findElement(By.xpath("//div[@class=\"taskbox span4\"][1]//input[contains(@name,'_Caption')]"));
        WebElement task2field = driver.findElement(By.xpath("//div[@class=\"taskbox span4\"][2]//input[contains(@name,'_Caption')]"));
        task1field.sendKeys(task1.getName());
        task2field.sendKeys(task2.getName());
        submitDialog(changeRequestSubmitBtn);
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

    public TaskCompletePage completeTask() {
        clickOnInvisibleElement(completeBtn);
        waitForDialog();
        return new TaskCompletePage(driver);
    }

    public KanbanAddSubtaskPage actionAddSubtask(){
		actionsBtn.click();
		clickOnInvisibleElement(addSubtaskBtn);
		return new KanbanAddSubtaskPage(driver);
	}
}
