package ru.devprom.pages.project;

import java.util.ArrayList;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.project.attributes.AttributeSettingsPage;
import ru.devprom.pages.project.blogs.BlogPage;
import ru.devprom.pages.project.documents.DocumentsPage;
import ru.devprom.pages.project.functions.FunctionsPage;
import ru.devprom.pages.project.kb.KnowledgeBasePage;
import ru.devprom.pages.project.milestones.MilestonesPage;
import ru.devprom.pages.project.repositories.RepositoryCommitsPage;
import ru.devprom.pages.project.repositories.RepositoryConnectPage;
import ru.devprom.pages.project.repositories.RepositoryFilesPage;
import ru.devprom.pages.project.requests.RequestViewPage;
import ru.devprom.pages.project.requests.RequestsBoardPage;
import ru.devprom.pages.project.requests.RequestsPage;
import ru.devprom.pages.project.requests.RequestsStatePage;
import ru.devprom.pages.project.requests.RequestsTypesPage;
import ru.devprom.pages.project.requirements.RequirementsPage;
import ru.devprom.pages.project.requirements.RequirementsTypesPage;
import ru.devprom.pages.project.requirements.TraceMatrixPage;
import ru.devprom.pages.project.settings.*;
import ru.devprom.pages.project.tasks.TasksBoardPage;
import ru.devprom.pages.project.tasks.TasksPage;
import ru.devprom.pages.project.tasks.TasksStatePage;
import ru.devprom.pages.project.testscenarios.TestScenariosPage;
import ru.devprom.pages.project.testscenarios.TestSpecificationsPage;
import ru.devprom.pages.project.ReleasesIterationsPage;

public class SDLCPojectPageBase extends ProjectPageBase implements IProjectBase{

	// Navigation bar items

	@FindBy(xpath = ".//li[@id='tab_favs']/a")
	protected WebElement favLink;

	@FindBy(xpath = ".//li[@id='tab_mgmt']/a")
	protected WebElement manageLink;

	@FindBy(xpath = ".//li[@id='tab_reqs']/a")
	protected WebElement analysisLink;

	@FindBy(xpath = ".//li[@id='tab_dev']/a")
	protected WebElement devLink;

	@FindBy(xpath = ".//li[@id='tab_qa']/a")
	protected WebElement qcLink;

	@FindBy(xpath = ".//li[@id='tab_docs']/a")
	protected WebElement docLink;

	// ИЗБРАННОЕ menu items
	@FindBy(xpath = ".//li[@id='setup']/a")
	protected WebElement menuReqsCustomItem;
	
	@FindBy(xpath = ".//li[@id='setup']/a")
	protected WebElement menuFavsCustomItem;

	@FindBy(xpath = "//a[@uid='project-settings']")
	protected WebElement projectSettingsLink;

	// УПРАВЛЕНИЕ ПРОЕКТОМ menu items

	// root items
	// База знаний
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[text()='База знаний']")
	protected WebElement knowledgeBaseItem;

	// Графики и отчеты
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[@id='menu-group-reports']")	
	protected WebElement reportsMenu;
	
	// УПРАВЛЕНИЕ ПРОЕКТОМ menu subitems

	// Активности
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[@uid='project-log']")
	protected WebElement activitiesItem;

	// Блог
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[text()='Блог']")
	protected WebElement blogItem;

	// Релизы и итерации
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[@uid='projectplan']")
	protected WebElement releaseItem;

	// Вехи
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[text()='Вехи']")
	protected WebElement milestonesItem;

	// Функции
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[@uid='features-list']")
	protected WebElement featuresItem;

	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[@uid='productbacklog']")
	protected WebElement backlogItem;

	// Доска пожеланий
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[text()='Доска пожеланий']")
	protected WebElement boardItem;

	// Планирование релизов
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[text()='Планирование релизов']")
	protected WebElement releasePlanItem;

	// Планирование итераций
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[text()='Планирование итераций']")
	protected WebElement iterationPlanningBoardItem;

	// Трассировка задач
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[@uid='currenttasks']")
	protected WebElement tasksItem;

	// График релизов
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[@uid='ee-delivery']")
	protected WebElement releaseChartItem;

	// Затраченное время
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[text()='Затраченное время']")
	protected WebElement spentTimeItem;

	// Загрузка задачами
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[text()='Загрузка задачами']")
	protected WebElement resourceLoadItem;

	// Все отчеты
	@FindBy(xpath = ".//a[@id='profile-my-reports']")
	protected WebElement reportsItem;

	// Проект
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[text()='Проект']")
	protected WebElement projectItem;

	// Справочники
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[text()='Справочники']")
	protected WebElement dictsItem;

	// Шаблоны базы знаний
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[text()='Шаблоны базы знаний']")
	protected WebElement templatesItem;

	// Снимки
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[text()='Версионирование']")
	protected WebElement snapshotsItem;

	// Нумерация версий
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[text()='Нумерация версий']")
	protected WebElement versionSettingsItem;

	// АНАЛИЗ И ПРОЕКТИРОВАНИЕ menu items
	// root items
	// Документы
	@FindBy(xpath = ".//ul[@id='menu_reqs']//a[@uid='requirements-docs']")
	protected WebElement requirementsDocsItem;

	// Разделы требований
	@FindBy(xpath = ".//ul[@id='menu_reqs']//a[@uid='requirementsnotimpl']")
	protected WebElement requirementsListItem;

	// Матрица трассируемости
	@FindBy(xpath = ".//ul[@id='menu_reqs']//a[@uid='requirementsmatrix']")
	protected WebElement traceMatrixListItem;
 
	// Left menu head items for АНАЛИЗ И ПРОЕКТИРОВАНИЕ

	// Графики и отчеты
	@FindBy(xpath = ".//ul[@id='menu_reqs']//a[@id='menu-group-charts']")	
	protected WebElement reportAnMenu;

	// Настройки
	@FindBy(xpath = ".//ul[@id='menu_reqs']//a[@id='menu-group-settings']")	
	protected WebElement settingsAnMenu;

	// АНАЛИЗ И ПРОЕКТИРОВАНИЕ menu subitems

	// Мои задачи
	@FindBy(xpath = ".//ul[@id='menu_reqs']//a[text()='Мои задачи']")
	protected WebElement myTasksListItem;

	// Доска задач
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[@uid='tasks-board']")
	protected WebElement tasksBoardItem;
	
	// Настройки
	@FindBy(xpath = ".//ul[@id='menu_reqs']//a[text()='Шаблоны требований']")
	protected WebElement requirementsTemplatesItem;
	

	// РАЗРАБОТКА menu items

	// Коммиты
	
	@FindBy(xpath = ".//ul[@id='menu_dev']//a[@uid='sourcecontrol-revision']")
	protected WebElement commitsItem;
	
	// Файлы в репозитории
	
	@FindBy(xpath = ".//ul[@id='menu_dev']//a[@uid='sourcecontrol-files']")
	protected WebElement repositoryFilesItem;
	
	
	// Подключение к репозиторию
	@FindBy(xpath = ".//ul[@id='menu_dev']//a[@uid='sourcecontrol-connection']")
	protected WebElement repositoryConnectItem;
	
	// КОНТРОЛЬ КАЧЕСТВА menu items

	@FindBy(xpath = ".//ul[@id='menu_qa']//a[@uid='testingdocinprogress']")
	protected WebElement testScenariosItem;
	
	@FindBy(xpath = ".//ul[@id='menu_qa']//a[@uid='testing-docs']")
	protected WebElement testPlansItem;

	// ДОКУМЕНТИРОВАНИЕ menu items

	@FindBy(xpath = ".//ul[@id='menu_docs']//a[@uid='helpdocs-docs']")
	protected WebElement docsItem;

	// НАСТРОЙКИ

	// Атрибуты
	@FindBy(xpath = ".//div[contains(@class,'project-settings')]//a[@uid='dicts-pmcustomattribute']")
	protected WebElement dictsAttrItem;

	// Состояния пожеланий
	@FindBy(xpath = ".//div[contains(@class,'project-settings')]//a[@uid='workflow-issuestate']")
	protected WebElement requestsStateItem;

	// Состояния задач
	@FindBy(xpath = ".//div[contains(@class,'project-settings')]//a[@uid='workflow-taskstate']")
	protected WebElement tasksStateItem;

	// Тэги
	@FindBy(xpath = ".//a[@uid='tags']")
	protected WebElement tagsItem;

	// Участники
	@FindBy(xpath = "//div[contains(@class,'project-settings')]//a[@uid='permissions-participants']")
	protected WebElement participantsListItem;

	// Тип требования
	@FindBy(xpath = ".//div[contains(@class,'project-settings')]//a[@uid='dicts-requirementtype']")
	protected WebElement requirementTypeItem;
	
	// Тип пожелания
	@FindBy(xpath = ".//div[contains(@class,'project-settings')]//a[@uid='dicts-requesttype']")
	protected WebElement requestTypeItem;
	
	// Права доступа
	@FindBy(xpath = ".//a[@id='menu-group-permissions']")	
	protected WebElement permissionsMenu;
	
	@FindBy(xpath = ".//a[@uid='permissions-settings']")
	protected WebElement permissionsItem;
	
	// Методология
	@FindBy(xpath = ".//a[@uid='methodology']")
	protected WebElement methodologyItem;
	
	// Терминология
	@FindBy(xpath = ".//a[@uid='process-terminology']")
	protected WebElement terminologyItem;
	
	// Общие настройки
	@FindBy(xpath = ".//a[@uid='project-settings']")
	protected WebElement commonSettingsItem;
	
	@FindBy(xpath = ".//a[@uid='ee-projectlinks']")
	protected WebElement linkedProjectsItem;
	
	@FindBy(xpath = ".//a[@uid='process-import']")
	protected WebElement loadTemplateItem;

	@FindBy(xpath = ".//a[@uid='dicts-projectrole']")
	protected WebElement projectRolesItem;

	@FindBy(xpath = ".//a[@uid='process-export']")
	protected WebElement saveTemplateItem;
	
	@FindBy(xpath = ".//a[@uid='profile']")
	protected WebElement mySettingsItem;
	
		
    // --------------Другие элементы------------------ //
	@FindBy(id = "filter-settings")
	protected WebElement asterixBtn;
	
	@FindBy(xpath = "//div[contains(@class,'filter')]//a[@id='save-report']")
	protected WebElement saveReportBtn;
		
		
	public SDLCPojectPageBase(WebDriver driver) {
		super(driver);

	}

	public SDLCPojectPageBase(WebDriver driver, Project project) {
		super(driver, project);
	}

	// GOTO Methods

	public ProjectMembersPage gotoMembers() {
		gotoSettingsPage();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(participantsListItem));
		participantsListItem.click();
		return new ProjectMembersPage(driver);
	}

	public RequestsPage gotoRequests() {
		manageLink.click();
		clickOnInvisibleElement(backlogItem);
		return new RequestsPage(driver);
	}
	
	public RequestsBoardPage gotoRequestsBoard() {
		manageLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(boardItem));
		boardItem.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(driver.findElement(By.xpath("//table[@id='requestboard1']"))));
		return new RequestsBoardPage(driver);
	}
	
	
	
	public FunctionsPage gotoFunctions() {
		manageLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(featuresItem));
		featuresItem.click();
		return new FunctionsPage(driver);
	}

	public DocumentsPage gotoDocuments() {
		docLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(docsItem));
		docsItem.click();
		return new DocumentsPage(driver);
	}

	public TasksPage gotoTasks() {
		manageLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(driver.findElement(By.xpath("//ul[@id='menu_mgmt']"))));
		clickOnInvisibleElement(tasksItem);
		return new TasksPage(driver);
	}

	public TasksBoardPage gotoTasksBoard() {
		manageLink.click();
		clickOnInvisibleElement(tasksBoardItem);
		TasksBoardPage page = new TasksBoardPage(driver);
		page.showAll();
		return page;
	}
	
	public RequestsStatePage gotoRequestsStatePage() {
		gotoSettingsPage();
		clickOnInvisibleElement(requestsStateItem);
		return new RequestsStatePage(driver);
	}

	public TasksStatePage gotoTasksStatePage() {
		gotoSettingsPage();
		clickOnInvisibleElement(tasksStateItem);
		return new TasksStatePage(driver);
	}

	public AttributeSettingsPage gotoAttributeSettings() {
		gotoSettingsPage();
		clickOnInvisibleElement(dictsAttrItem);
		return new AttributeSettingsPage(driver);
	}
	
	
	public RequirementsPage gotoRequirements() {
		analysisLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(requirementsListItem));
		requirementsListItem.click();
		return new RequirementsPage(driver);
	}
	
	public RequirementDocumentsPage gotoRequirementDocuments() {
		analysisLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(requirementsDocsItem));
		requirementsDocsItem.click();
		return new RequirementDocumentsPage(driver);
	}
	
	
	// Removed functionality
	/*
	 * public String createNewTag (String name) { String youCameFrom=
	 * driver.getCurrentUrl(); manageLink.click(); if (! tagsItem.isDisplayed())
	 * settingsMenu.click(); tagsItem.click(); driver.findElement(By.xpath(
	 * "//a[@data-toggle='dropdown' and contains(.,'Действия')]")).click();
	 * driver.findElement(By.xpath(
	 * "//a[contains(.,'Добавить') and contains(@href,'/pm/devprom_webtest/project/tags?class=metaobject&entity=Tag')]"
	 * )).click(); driver.findElement(By.id("TagCaption")).sendKeys(name);
	 * driver.findElement(By.id("TagSubmitBtn")).click();
	 * driver.navigate().to(youCameFrom); return name; }
	 */

	public String[] getTagsList() {
		String youCameFrom = driver.getCurrentUrl();
		gotoSettingsPage();
		clickOnInvisibleElement(tagsItem);
		String[] tagsList = new String[driver.findElements(
				By.xpath("//tr[contains(@id,'taglist1_row_')]//td[@id='caption']")).size()];
		for (int i = 0; i < tagsList.length; i++) {
			tagsList[i] = driver
					.findElements(
							By.xpath("//tr[contains(@id,'taglist1_row_')]//td[@id='caption']"))
					.get(i).getText();
		}
		driver.navigate().to(youCameFrom);
		return tagsList;
	}

	/*public String getLastActivityID(char category) {
		String id;
		String youCameFrom = driver.getCurrentUrl();
		manageLink.click();
		if (!activitiesItem.isDisplayed())
			projectMenu.click();
		activitiesItem.click();
		if (category != '0')
			id = driver
					.findElement(
							By.xpath("//tr[contains(@id,'projectloglist1_row_')]/td[@id='content']//a[contains(.,'["
									+ category + "')]")).getText();
		else
			id = driver
					.findElement(
							By.xpath("//tr[contains(@id,'projectloglist1_row_')]/td[@id='content']//a"))
					.getText();
		id = id.substring(1, id.length() - 1);
		driver.navigate().to(youCameFrom);
		return id;
	}*/
	
	public String getLastActivityID(char category){
		String id;
		String youCameFrom= driver.getCurrentUrl();
		manageLink.click();
		activitiesItem.click();	
		if (category!='0') 
		 id = driver.findElement(By.xpath("//tr[contains(@id,'projectloglist1_row_')]/td[@id='content']/a[contains(.,'["+category+"')]")).getText();
		else  id = driver.findElement(By.xpath("//tr[contains(@id,'projectloglist1_row_')]/td[@id='content']/a")).getText();
		id = id.substring(1, id.length()-1);
	    driver.navigate().to(youCameFrom);
	    return id;
	}

	public TestScenariosPage gotoTestScenarios() {
		qcLink.click();
		clickOnInvisibleElement(testScenariosItem);
		return new TestScenariosPage(driver);
	}

	
	
	public TextTemplatesPage gotoTextTemplates() {
		gotoSettingsPage();
		(new WebDriverWait(driver,waiting)).until(
				ExpectedConditions.presenceOfElementLocated(By.xpath("//a[@uid='dicts-texttemplate']"))
				);
		clickOnInvisibleElement(driver.findElement(By.xpath("//a[@uid='dicts-texttemplate']")));
		return new TextTemplatesPage(driver);
	}

	public RequirementsTypesPage gotoRequirementsTypes() {
		gotoSettingsPage();
		clickOnInvisibleElement(requirementTypeItem);
		return new RequirementsTypesPage(driver);
	}
	
	public RequestsTypesPage gotoRequestsTypes() {
		gotoSettingsPage();
		clickOnInvisibleElement(requestTypeItem);
		return new RequestsTypesPage(driver);
	}
	
	
	public ProjectCommonSettingsPage gotoCommonSettings(){
		gotoSettingsPage();
		clickOnInvisibleElement(commonSettingsItem);
		return new ProjectCommonSettingsPage(driver);
	}
	
	
	public KnowledgeBasePage gotoKnowledgeBase(){
		manageLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(knowledgeBaseItem));
		knowledgeBaseItem.click();
		return new KnowledgeBasePage(driver);
	}
	
	public BlogPage gotoBlog(){
		manageLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(blogItem));
		blogItem.click();
		return new BlogPage(driver);
	}
	
	public AllReportsPage gotoAllReports(){
		clickOnInvisibleElement(reportsItem);
		return new AllReportsPage(driver);
	}
	
	public TimetablePage gotoTimetablePage(){
		clickOnInvisibleElement(driver.findElement(By.xpath("//a[@uid='activitiesreport']")));
		return new TimetablePage(driver);
	}

	public TimetablePage gotoTasksTimetablePage(){
		clickOnInvisibleElement(driver.findElement(By.xpath("//a[@uid='activitiesreport']")));
		clickOnInvisibleElement(driver.findElement(By.id("activitiesreporttasks")));
		return new TimetablePage(driver);
	}

	public void gotoCodeArea() {
		devLink.click();
	}

	public RepositoryFilesPage gotoRepositoryFilesPage(){
		devLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(repositoryFilesItem));
		repositoryFilesItem.click();
		return new RepositoryFilesPage(driver);
	}
	
	public RepositoryCommitsPage gotoRepositoryCommitsPage(){
		devLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(commitsItem));
		commitsItem.click();
		return new RepositoryCommitsPage(driver);
	}
	
	public RepositoryConnectPage gotoRepositoryConnectPage(){
		devLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(repositoryConnectItem));
		repositoryConnectItem.click();
		return new RepositoryConnectPage(driver);
	}
	
	
	public PermissionsPage gotoPermissionsPage(){
		gotoSettingsPage();
		if (!permissionsItem.isDisplayed()) permissionsMenu.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(permissionsItem));
		permissionsItem.click();
		return new PermissionsPage(driver);
	}

	public TerminologyPage gotoTerminologyPage() {
		gotoSettingsPage();
		clickOnInvisibleElement(terminologyItem);
		return new TerminologyPage(driver);
	}
	
	public String checkLinkName(String partOfURL){
		return driver.findElement(By.xpath("//a[contains(@href,'"+partOfURL+"')]")).getText();
	}

	public ReleasesIterationsPage gotoReleasesIterations() {
		manageLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(releaseItem));
		releaseItem.click();
		return new ReleasesIterationsPage(driver);
	}

	public LinkedProjectsPage gotoLinkedProjects() {
		gotoSettingsPage();
		clickOnInvisibleElement(linkedProjectsItem);
		return new LinkedProjectsPage(driver);
	}
	
	
	//Search functionality
	@FindBy(xpath = "//input[@id='quick-search']")
	protected WebElement searchInput;
	
	@FindBy(xpath = "//div[contains(@class,'input-append')]//button[@type='submit']")
	protected WebElement searchBtn;
	
	public SearchResultsPage searchByKeyword(String keyword){
		favLink.click();
		(new WebDriverWait(driver,3)).until(ExpectedConditions.visibilityOf(searchInput));
		searchInput.clear();
		searchInput.sendKeys(keyword);
		searchBtn.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.presenceOfElementLocated(By.id("searchlist1")));
		return new SearchResultsPage(driver);
	}
	
	
public RequestViewPage searchByRequestId(String id) {
	favLink.click();
	(new WebDriverWait(driver,3)).until(ExpectedConditions.visibilityOf(searchInput));
	searchInput.clear();
	searchInput.sendKeys(id);
	searchBtn.click();
	(new WebDriverWait(driver,waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//ul[contains(@class,'breadcrumb')]/li/a[text()='Доска пожеланий']")));
		return new RequestViewPage(driver);
	}


public ProjectActivitiesPage gotoProjectActivities() {
	manageLink.click();
	(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(activitiesItem));
	activitiesItem.click();
	return new ProjectActivitiesPage(driver);
}

public MilestonesPage gotoMilestones() {
	manageLink.click();
	(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(milestonesItem));
	milestonesItem.click();
	return new MilestonesPage(driver);
}


public TraceMatrixPage gotoTraceMatrix() {
	analysisLink.click();
	(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(traceMatrixListItem));
	traceMatrixListItem.click();
	return new TraceMatrixPage(driver);
}

public TestSpecificationsPage gotoTestPlans() {
	qcLink.click();
	(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(testPlansItem));
	testPlansItem.click();
	return new TestSpecificationsPage(driver);
}

public MenuCustomizationPage gotoMenuReqsCustomization() {
	analysisLink.click();
	(new WebDriverWait(driver,waiting))
		.until(ExpectedConditions.visibilityOf(menuReqsCustomItem));
	menuReqsCustomItem.click();
	(new WebDriverWait(driver,waiting))
		.until(ExpectedConditions.visibilityOfElementLocated(By.xpath("//button[@id='menu-reset']")));
	try {
		Thread.sleep(5000);
	} catch (InterruptedException e) {
	}
	driver.findElement(By.xpath(".//section[@id='functional-group-selector']//a[@uid='reqs']")).click();
	try {
		Thread.sleep(5000);
	} catch (InterruptedException e) {
	}
	return new MenuCustomizationPage(driver);
}

public MenuCustomizationPage gotoMenuFavsCustomization() {
	favLink.click();
	(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(menuFavsCustomItem));
	menuFavsCustomItem.click();
	(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOfElementLocated(By.xpath("//div[@id='restore-control']")));
	return new MenuCustomizationPage(driver);
}


public boolean isMenuItemExist(String name){
	return !driver.findElements(By.xpath("//a[text()='"+name+"']")).isEmpty();
}

public List<String> getAllVisibleMenuItems(){
	List<String> items = new ArrayList<String>();
	List<WebElement> list = driver.findElements(By.xpath("//ul[contains(@class,'vertical-menu') and not (contains(@style,'display: none'))]//a"));
	for (WebElement l:list){
		if (!"".equals(l.getText())) items.add(l.getText());
	}
	return items;
}

public void openSubmenu(String submenuEng){
	driver.findElement(By.xpath("//li[@id='menu-folder-"+submenuEng+"']/a")).click();
	 
}

public MethodologyPage gotoMethodologyPage() {
	gotoSettingsPage();
	(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(methodologyItem));
	methodologyItem.click();
	return new MethodologyPage(driver);
}

public SaveTemplatePage gotoSaveTemplatePage() {
	gotoSettingsPage();
	clickOnInvisibleElement(saveTemplateItem);
	return new SaveTemplatePage(driver);
}

public LoadTemplatePage gotoLoadTemplatePage() {
	gotoSettingsPage();
	clickOnInvisibleElement(loadTemplateItem);
	return new LoadTemplatePage(driver);
}

	public ProjectRolePage gotoProjectRolesPage() {
		gotoSettingsPage();
		clickOnInvisibleElement(projectRolesItem);
		return new ProjectRolePage(driver);
	}

	public SaveReportPage saveReport() {
		clickOnInvisibleElement(saveReportBtn);
		return new SaveReportPage(driver);
	}


	public String readFilterCaption(String filter){
		return driver.findElement(By.xpath("//div[contains(@class,'filter')]//a[@uid='"+filter+"']")).getText();
	}

	public boolean isColumnPresent(String column){
		return !driver.findElements(By.xpath("//table//th[@uid='"+column+"']")).isEmpty();
	}

	public boolean isFilterPresent(String filter){
		return !driver.findElements(By.xpath("//div[contains(@class,'filter')]//*[@uid='"+filter+"']")).isEmpty();
	}

	public MySettingsPage gotoMySettingsPage() {
		gotoSettingsPage();
		clickOnInvisibleElement(mySettingsItem);
		return new MySettingsPage(driver);
	}

	public void savePageSettins(){
		clickOnInvisibleElement(driver.findElement(By.id("personal-persist")));
		asterixBtn.click();
	}
}
