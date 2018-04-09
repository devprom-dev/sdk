package ru.devprom.pages.scrum;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.project.IProjectBase;
import ru.devprom.pages.project.ProjectPageBase;
import ru.devprom.pages.project.kb.KnowledgeBasePage;
import ru.devprom.pages.project.requests.RequestsBoardPage;
import ru.devprom.pages.project.settings.ProjectMembersPage;

public class ScrumPageBase extends ProjectPageBase implements IProjectBase
{
	@FindBy(xpath = "//li[@id='tab_stg']/a")
	protected WebElement settingsLink;

	@FindBy(xpath = "//li[@id='tab_favs']/a")
	protected WebElement favsLink;

	@FindBy(xpath = "//li[@id='tab_reqs']/a")
	protected WebElement analysisLink;
	
	@FindBy(xpath = "//ul[@id='menu_reqs']//a[@id='menu-group-features']")	
	protected WebElement productAnMenu;
	
	@FindBy(xpath = "//ul[@id='menu_reqs']//a[@id='menu-group-tasks']")	
	protected WebElement tasksMenu;
	
	// Доска историй
	@FindBy(xpath = "//ul[@id='menu_reqs']//a[@uid='issues-board']")
	protected WebElement issuesBoardItem;
		
	// Доска историй
	@FindBy(xpath = "//ul[@id='menu_favs']//a[@module='tasks-board']")
	protected WebElement tasksBoardItem;
	
	// Участники
	@FindBy(xpath = "//ul[@id='menu_stg']//a[text()='Участники']")
	protected WebElement participantsListItem;
        
        // Бэклог
	@FindBy(xpath = "//*[@class='root ']//*[@uid='productbacklog']")
	protected WebElement backlogItem;
        
        // доска историй
	@FindBy(xpath = "//*[@id='menu_favs']//*[@uid='issues-board']")
	protected WebElement historyBoardItem;
        
        // отчеты
	@FindBy(xpath = "//*[@id='menu-group-reports']")
	protected WebElement reportsItem;
        
         // отчет BurnDown
	@FindBy(xpath = "//*[@uid='iterationburndown']")
	protected WebElement burnDownItem;
        
         // отчет скорость разаботки
	@FindBy(xpath = "//*[@uid='velocitychart']")
	protected WebElement developmentSpeedItem;
        
        // база знаний
	@FindBy(xpath = "//*[@id='menu_favs']//*[@uid='project-knowledgebase']")
	protected WebElement knowledgeBaseItem;
	
	public ScrumPageBase(WebDriver driver) {
		super(driver);
	}

	public ScrumPageBase(WebDriver driver, Project project) {
		super(driver, project);
	}

	
	public ProjectMembersPage gotoMembers() {
		settingsLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(participantsListItem));
		participantsListItem.click();
		return new ProjectMembersPage(driver);
	}
	
	
	public IssuesBoardPage gotoIssuesBoard() {
		analysisLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(productAnMenu));
		if (!issuesBoardItem.isDisplayed())
			productAnMenu.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(issuesBoardItem));
		issuesBoardItem.click();
		return new IssuesBoardPage(driver);
	}
	
	public TasksBoardPage gotoTasksBoard() {
		favsLink.click();
		tasksBoardItem.click();
		return new TasksBoardPage(driver);
	}

    public BackLogPage gotoBackLog() {
        clickOnInvisibleElement(favsLink);
        clickOnInvisibleElement(backlogItem);
        return new BackLogPage(driver);
    }

    public RequestsBoardPage gotoHistoryBoard() {
        clickOnInvisibleElement(favsLink);
        clickOnInvisibleElement(historyBoardItem);
        return new RequestsBoardPage(driver);
    }

    public void gotoBurnDown() {
    	reportsItem.click();
    	burnDownItem.click();
    }

    public void gotoDevelopmentSpeed() {
    	if ( !developmentSpeedItem.isDisplayed() ) {
    		reportsItem.click();
    	}
        developmentSpeedItem.click();
    }

    public KnowledgeBasePage gotoKnowledgeBase() {
        clickOnInvisibleElement(favsLink);
        clickOnInvisibleElement(knowledgeBaseItem);
        return new KnowledgeBasePage(driver);
    }
}
