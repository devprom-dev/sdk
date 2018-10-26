package ru.devprom.pages.kanban;

import java.util.HashMap;
import java.util.List;
import java.util.Map;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.helpers.WebDriverPointerRobot;
import ru.devprom.items.Project;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.project.IProjectBase;
import ru.devprom.pages.project.ProjectPageBase;
import ru.devprom.pages.project.ReleaseNewPage;
import ru.devprom.pages.project.requests.RequestsBoardPage;
import ru.devprom.pages.project.requests.RequestsPage;
import ru.devprom.pages.project.requirements.RequirementNewPage;
import ru.devprom.pages.project.requirements.RequirementsDocsPage;
import ru.devprom.pages.project.requirements.RequirementsPage;
import ru.devprom.pages.project.requirements.RequirementsTracePage;
import ru.devprom.pages.project.settings.ProjectMembersPage;
import ru.devprom.pages.project.tasks.TasksStatePage;
import ru.devprom.pages.project.testscenarios.TestScenarioTestingPage;
import ru.devprom.pages.project.testscenarios.TestScenariosPage;
import ru.devprom.pages.project.testscenarios.TestSpecificationsPage;

public class KanbanPageBase extends ProjectPageBase implements IProjectBase 
{
	@FindBy(xpath = "//a[@id='navbar-quick-create']")
	protected WebElement addBtn;

	@FindBy(xpath = "//div[contains(@class,'quick-btn')]//a[@id='issue']")
	protected WebElement newTaskBtn;
        
	@FindBy(xpath = "//div[contains(@class,'quick-btn')]//a[@id='task']")
	protected WebElement addSubTaskBtn;

	public KanbanPageBase(WebDriver driver) {
		super(driver);
	}

	public KanbanPageBase(WebDriver driver, Project project){
		super(driver);
	}
	
	//поле версия на форме начать тестирование
        @FindBy(id="VersionText")
	protected WebElement versionField;
        
        //кнопка сохранить на форме начать тестирование
        @FindBy(xpath = ".//*[@id='pm_TestSubmitBtn']")
	protected WebElement saveTestingBtn;
        
        //кнопка сохранить на форме подтверждения перехода
        @FindBy(xpath = ".//*[@id='pm_ChangeRequestSubmitBtn']")
	protected WebElement saveBtn;
        
        //кнопка Еще после выделения карточек
        @FindBy(xpath = ".//*[@id='bulk-actions']/a")
	protected WebElement moreBtn;
        
        //пункт Требования в меню Еще
        @FindBy(xpath = ".//*[@id='bulk-actions']//a[contains(text(),'Требования')]")
	protected WebElement massRequirementItem;
        
        //пункт Тестовая документация в меню Еще
        @FindBy(xpath = ".//*[@id='bulk-actions']//*[contains(text(),'Тестовая документация')]")
	protected WebElement massTestDocsItem;
        
	// Navigation bar items
        //верхняя панель
        @FindBy(xpath = ".//*[@id='new-issue']")
	protected WebElement addWish;
        
        @FindBy(xpath = ".//*[@id='new-plannedrelease']")
	protected WebElement newRelaeseBtn;
        
        //разработка
        //верхнее меню "Разработка"
        @FindBy(xpath = ".//div[@id='main-sidebar']//li[@id='tab_dev']/a")
	protected WebElement developmentLink;
        
        //верхнее меню Контроль качества
        @FindBy(xpath = "//*[@id='tab_qa']/a")
	protected WebElement QCLink;
        
        //боковая панель Задачи (для Контроля качества)
        @FindBy(xpath = "//ul[@id='menu_qa']//a[@id='menu-group-tasks']")
	protected WebElement QCtasksItem;
        
        //боковая панель Задачи подпункт Доска задач (для Контроля качества)
        @FindBy(xpath = "//ul[@id='menu_qa']//a[@uid='tasksboardfortesting']")
	protected WebElement QCtaskBoardItem;
        
        //верхнее меню Анализ и проектирование
        @FindBy(xpath = "//*[@id='tab_reqs']/a")
	protected WebElement AnalyseLink;
        
        //боковая панель коммиты
        @FindBy(xpath = "//*[@uid='sourcecontrol-revision']")
	protected WebElement commitsItem;
        
        //боковая панель обнаруженные ошибки
        @FindBy(xpath = "//*[@uid='bugs']")
	protected WebElement foundBugsItem;
        
        //боковая панель тесты
        @FindBy(xpath = "//ul[@id='menu_qa']//*[@uid='testsofreleasereport']")
	protected WebElement testsItem;
        
        //боковая панель тест планы
        @FindBy(xpath = "//*[@uid='testing-docs']")
	protected WebElement testPlansItem;
        
        //боковая панель документы требований
        @FindBy(xpath = "//li/a[@uid='requirements-docs']")
	protected WebElement requirementsDoksItem;
        
        //боковая панель Реестр требований
        @FindBy(xpath = "//li/a[@uid='requirements-list']")
	protected WebElement requirementReestr;
        
        //боковая панель Сборки
         @FindBy(xpath = "//ul[@id='menu_qa']//li/a[@uid='operations-builds']")
	protected WebElement builds;
        
         //боковая панель Окружение
         @FindBy(xpath = "//ul[@id='menu_qa']//li/a[@uid='dicts-environment']")
	protected WebElement envirenmentsItem;
         
        //боковая панель "Задачи"
        @FindBy(xpath = "(//a[@id='menu-group-tasks'])[2]")
	protected WebElement tasksItem;
        
        //подменю боковой панели "Задачи" - "Доска задач"
         @FindBy(xpath = "(//li[@id='tasks-1']/a)[2]")
	protected WebElement tasksBoardSubItem;
        
	// --ИЗБРАННОЕ--
	@FindBy(xpath = ".//div[@id='main-sidebar']//li[@id='tab_favs']/a")
	protected WebElement favLink;
	
	@FindBy(xpath = ".//div[@id='sidebar']//a[@uid='kanbanboard']")
	protected WebElement boardItem;

	@FindBy(xpath = ".//div[@id='sidebar']//a[@uid='project-knowledgebase']")
	protected WebElement kbItem;
	
	@FindBy(xpath = ".//div[@id='sidebar']//a[@module='issues-backlog']")
	protected WebElement backlog;

	@FindBy(xpath = ".//div[@id='sidebar']//a[@module='tasks-list']")
	protected WebElement myTasks;

	//ОТЧЕТЫ
	
	@FindBy(xpath = ".//ul[@id='menu_favs']//a[@id='menu-group-reports']")
	protected WebElement reportsSection;

	@FindBy(xpath = ".//div[@id='sidebar']//a[@uid='project-log']")
	protected WebElement activitiesLink;
	
	//НАСТРОЙКИ
	@FindBy(xpath = ".//ul[@id='menu_favs']//a[@id='menu-group-settings']")
	protected WebElement settingsSection;
	
	
	// --НАСТРОЙКИ--
	@FindBy(xpath = "//a[@uid='settings-4-project']")
	protected WebElement settingsLink;
	
	// Участники
	@FindBy(xpath = ".//a[@uid='permissions-participants']")
	protected WebElement participantsListItem;
	
	@FindBy(xpath = ".//a[@uid='workflow-issuestate']")
	protected WebElement requestsStateItem;
	
	// Методология
	@FindBy(xpath = ".//a[@uid='methodology']")
	protected WebElement methodologyItem;
	
	
	public KanbanTasksPage gotoKanbanTasks() {
		favLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(backlog));
		backlog.click();
		return new KanbanTasksPage(driver);
	}
	
	public KanbanTaskBoardPage gotoKanbanBoard() {
		favLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(boardItem));
		boardItem.click();
		return new KanbanTaskBoardPage(driver);
	}

	public MethodologyPage gotoMethodology(){
		settingsLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(methodologyItem));
		methodologyItem.click();
		return new MethodologyPage(driver);
	}
	
	public KanbanActivitiesPage gotoActivities(){
		favLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(reportsSection));
		reportsSection.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(activitiesLink));
		activitiesLink.click();
		return new KanbanActivitiesPage(driver);
	}

	public KanbanTasksStatesPage gotoTasksStates(){
		settingsLink.click();
		requestsStateItem.click();
		return new KanbanTasksStatesPage(driver);
	}

	public ProjectMembersPage gotoMembers() {
		settingsLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(participantsListItem));
		participantsListItem.click();
		return new ProjectMembersPage(driver);
	}

    public KanbanTaskNewPage goToAddWish() {
        addWish.click();
        return new KanbanTaskNewPage(driver);
    }
//перенос в другой релиз с указанием позиции цикла, нет релиза =-1
    public void moveToAnotherRelease(String requestNumericId, int releaseNumber, String columnName)
    {
    	RequestsBoardPage board = new RequestsBoardPage(driver);
    	try {
			Thread.sleep(2000);
		} catch (InterruptedException e) {
		}
    	board.moveToAnotherSection(requestNumericId, String.valueOf(releaseNumber), columnName);
    }

    public ReleaseNewPage clickNewRelease() {
        newRelaeseBtn.click();
        waitForDialog();
        return new ReleaseNewPage(driver);
    }

    public RequirementNewPage clickToContextSubMenuItem(String idWish, String menuItemString, String submenuItemString) {
    	return clickToContextSubMenuItemAndWait(idWish, menuItemString, submenuItemString, 3000);
    }

    public RequirementNewPage clickToContextSubMenuItemAndWait(String idWish, String menuItemString, String submenuItemString, int waitSeconds)
    {
        String wishNumber = idWish.substring(2);
        WebElement menuItem = driver.findElement(By.xpath("//div[contains(@id,'context-menu-"+wishNumber+"')]//a[text()='"+menuItemString+"']"));
        WebElement submenuItem = driver.findElement(By.xpath("//div[contains(@id,'context-menu-"+wishNumber+"')]//a[text()='"+submenuItemString+"']"));
        WebElement onElement = driver.findElement(By.xpath("//table[contains(@id,'requestboard')]//a[contains(.,'[" +idWish+ "]')]/../.."));
        Actions contextClick = new Actions(driver);
        mouseMove(onElement);
        contextClick.contextClick(onElement).build().perform();
        clickOnInvisibleElement(menuItem);
        clickOnInvisibleElement(submenuItem);
        return new RequirementNewPage(driver);
    }

    public KanbanTaskViewPage openTask(String idTask) {
        try{
           driver.findElement(By.xpath("//table[contains(@id,'requestboard')]//a[contains(.,'[" +idTask+ "]')]")).click();
           Thread.sleep(3000);
           return new KanbanTaskViewPage(driver); 
        }
        catch(InterruptedException e)
        {
            return null;
        }
        
    }

    public KanbanTaskBoardPage gotoTaskBoard() {
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(developmentLink));
        developmentLink.click();
        clickOnInvisibleElement(tasksItem);
        clickOnInvisibleElement(tasksBoardSubItem);
        return new KanbanTaskBoardPage(driver);
    }
    
       public KanbanBuildsPage gotoBuilds() {
        clickOnInvisibleElement(QCLink);
        clickOnInvisibleElement(builds);
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//table[@uid='operations/builds']")));
        return new KanbanBuildsPage(driver);
    }

    public void clickToContextMenuItem(String idWish, String menuItemString) {
        try{
        Thread.sleep(3000);
        String wishNumber = idWish.substring(2);
        WebElement onElement = driver.findElement(By.xpath("//table[contains(@id,'requestboard')]//a[contains(.,'[" +idWish+ "]')]/../.."));
        Actions contextClick = new Actions(driver);
        mouseMove(onElement);
        contextClick.contextClick(onElement).build().perform();
        Thread.sleep(1000);
        WebElement menuItem = driver.findElement(By.xpath("//div[contains(@id,'context-menu-"+wishNumber+"')]//a[text()='"+menuItemString+"']"));
      //  clickOnInvisibleElement(menuItem);
        menuItem.click();
        FILELOG.debug("Context menu click " + menuItemString);
        }
        catch(InterruptedException e)
        {}
    }

    public TestScenarioTestingPage startTesting(String string) {
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(versionField));
        versionField.sendKeys(string);
        autocompleteSelect(string);
        submitDialog(saveTestingBtn);
        FILELOG.debug("Save button had clicked on start testing form");
        return new TestScenarioTestingPage(driver);
    }

    public void clickSubmit() {
    	waitForDialog();
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(saveBtn));
        submitDialog(saveBtn);
        try {
			Thread.sleep(1000);
		} catch (InterruptedException e) {
		}
    }

    public RequirementsPage goRequirementReestr() {
        try
        {
            Thread.sleep(3000);
            AnalyseLink.click();
            (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(requirementReestr));
            requirementReestr.click();
            FILELOG.debug("Requirement Reeste Item clicked");
            return new RequirementsPage(driver);
        }
        catch(InterruptedException e)
        {
            FILELOG.debug("Requirement Reeste Item didn't click");
            return null;
        }
        
    }

    public void gotoCommits() {
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(developmentLink));
        developmentLink.click();
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(commitsItem));
        commitsItem.click();
    }

    public void selectWish(String idWish) {
    	clickOnInvisibleElementWithCtrl(
			driver.findElement(
				By.xpath("//table[contains(@id,'requestboard')]//a[contains(.,'[" +idWish+ "]')]/ancestor::div[contains(@class,'board_item')]")     
			)
    	);
    }

    public RequirementsTracePage massRequirements() {
        clickOnInvisibleElement(moreBtn);
        clickOnInvisibleElement(massRequirementItem);
        return new RequirementsTracePage(driver);
    }
    
     public TestScenariosPage  massTestDocs() {
        clickOnInvisibleElement(moreBtn);
        clickOnInvisibleElement(massTestDocsItem);
        return new TestScenariosPage(driver);
    }

    public TestSpecificationsPage gotoTestPlans() {
        clickOnInvisibleElement(QCLink);
        clickOnInvisibleElement(testPlansItem);
        return new TestSpecificationsPage(driver);
    }
    
     public RequirementsDocsPage gotoRequirementsDocs() {
         clickOnInvisibleElement(AnalyseLink);
         clickOnInvisibleElement(requirementsDoksItem);
        return new RequirementsDocsPage(driver);
    }

    public KanbanEnvirenmentsPage gotoEnvironments() {
        clickOnInvisibleElement(QCLink);
        clickOnInvisibleElement(envirenmentsItem);
        return new KanbanEnvirenmentsPage(driver);
    }

    public RequestsPage gotoFoundBugs() {
        clickOnInvisibleElement(QCLink);
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(foundBugsItem));
        clickOnInvisibleElement(foundBugsItem);
        return new RequestsPage(driver);
    }

    public KanbanTestsPage gotoTests() {
        clickOnInvisibleElement(QCLink);
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(testsItem));
        clickOnInvisibleElement(testsItem);
        return new KanbanTestsPage(driver);
        
    }

    public KanbanTasksPage gotoQATaskBoard() {
        clickOnInvisibleElement(QCLink);
        clickOnInvisibleElement(QCtasksItem);
        clickOnInvisibleElement(QCtaskBoardItem);
        return new KanbanTasksPage(driver);
    }

	public KanbanAddSubtaskPage addSubtask(){
		addBtn.click();
		addSubTaskBtn.click();
		return new KanbanAddSubtaskPage(driver);
	}
}
