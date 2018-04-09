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
import ru.devprom.pages.project.settings.MethodologyPage;
import ru.devprom.pages.project.settings.MySettingsPage;
import ru.devprom.pages.project.settings.ProjectMembersPage;
import ru.devprom.pages.project.settings.SaveTemplatePage;
import ru.devprom.pages.project.settings.TerminologyPage;
import ru.devprom.pages.project.settings.TextTemplatesPage;
import ru.devprom.pages.project.tasks.TasksBoardPage;
import ru.devprom.pages.project.tasks.TasksPage;
import ru.devprom.pages.project.tasks.TasksStatePage;
import ru.devprom.pages.project.testscenarios.TestScenariosPage;
import ru.devprom.pages.project.testscenarios.TestSpecificationsPage;
import ru.devprom.pages.project.ReleasesIterationsPage;

public class SDLCPojectPageBase extends ProjectPageBase implements IProjectBase{

	// Navigation bar items

	//@FindBy(xpath = ".//div[@class='navbar']//a[contains(text(),'Избранное')]")
	@FindBy(xpath = ".//li[@id='tab_favs']/a")
	protected WebElement favLink;

	//@FindBy(xpath = ".//div[@class='navbar']//a[contains(text(),'Управление проектом')]")
	@FindBy(xpath = ".//li[@id='tab_mgmt']/a")
	protected WebElement manageLink;

	//@FindBy(xpath = ".//div[@class='navbar']//a[contains(text(),'Анализ и проектирование')]")
	@FindBy(xpath = ".//li[@id='tab_reqs']/a")
	protected WebElement analysisLink;

	//@FindBy(xpath = ".//div[@class='navbar']//a[contains(text(),'Разработка')]")
	@FindBy(xpath = ".//li[@id='tab_dev']/a")
	protected WebElement devLink;

	//@FindBy(xpath = ".//div[@class='navbar']//a[contains(text(),'Контроль качества')]")
	@FindBy(xpath = ".//li[@id='tab_qa']/a")
	protected WebElement qcLink;

	//@FindBy(xpath = ".//div[@class='navbar']//a[contains(text(),'Документирование')]")
	@FindBy(xpath = ".//li[@id='tab_docs']/a")
	protected WebElement docLink;

	//@FindBy(xpath = ".//div[@class='navbar']//i/..")
	@FindBy(xpath = ".//li[@id='tab_stg']/a")
	protected WebElement settingsLink;

	// ИЗБРАННОЕ menu items
	@FindBy(xpath = ".//ul[@id='menu_reqs']//li[@id='setup']/a")
	protected WebElement menuReqsCustomItem;
	
	@FindBy(xpath = ".//ul[@id='menu_favs']//li[@id='setup']/a")
	protected WebElement menuFavsCustomItem;

	@FindBy(xpath = "//a[@uid='project-settings']")
	protected WebElement projectSettingsLink;

	// УПРАВЛЕНИЕ ПРОЕКТОМ menu items

	// root items
	// База знаний
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[text()='База знаний']")
	protected WebElement knowledgeBaseItem;

	// Left menu head items for УПРАВЛЕНИЕ ПРОЕКТОМ
	// Проект
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[@id='menu-group-project']")
	protected WebElement projectMenu;

	// План
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[@id='menu-group-plan']")
	protected WebElement planMenu;

	// Продукт
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[@id='menu-group-features']")
	protected WebElement productMenu;

	// Релизы
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[@id='menu-group-releases']")
	protected WebElement releaseMenu;

	// Итерации
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[@id='menu-group-iterations']")
	protected WebElement iterationMenu;

	// Графики и отчеты
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[@id='menu-group-reports']")	
	protected WebElement reportsMenu;

	
	// УПРАВЛЕНИЕ ПРОЕКТОМ menu subitems

	// Активности
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[text()='Активности']")
	protected WebElement activitiesItem;

	// Блог
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[text()='Блог']")
	protected WebElement blogItem;

	// Вопросы
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[text()='Вопросы ']")
	protected WebElement questionsItem;

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

	// Трассировка пожеланий
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[text()='Трассировка пожеланий']")
	protected WebElement issuesTraceItem;

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
	@FindBy(xpath = ".//ul[@id='menu_mgmt']//a[@uid='project-reports']")
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
	@FindBy(xpath = ".//ul[@id='menu_reqs']//a[text()='Документы требований']")
	protected WebElement requirementsDocsItem;

	// Разделы требований
	@FindBy(xpath = ".//ul[@id='menu_reqs']//a[text()='Реестр требований']")
	protected WebElement requirementsListItem;
	
	// Матрица трассируемости
	@FindBy(xpath = ".//ul[@id='menu_reqs']//a[text()='Матрица трассируемости']")
	protected WebElement traceMatrixListItem;
 
	// Left menu head items for АНАЛИЗ И ПРОЕКТИРОВАНИЕ

	// Задачи
	@FindBy(xpath = ".//ul[@id='menu_reqs']//a[@id='menu-group-tasks']")	
	protected WebElement tasksAnMenu;

	// Продукт
	@FindBy(xpath = ".//ul[@id='menu_reqs']//a[@id='menu-group-features']")	
	protected WebElement productAnMenu;
	
	// StoryMapping
	@FindBy(xpath = ".//ul[@id='menu_reqs']//a[@uid='storymapping-storyboard']")	
	protected WebElement storyMappingItem;

	// Трассировка
	@FindBy(xpath = ".//ul[@id='menu_reqs']//a[@id='menu-group-trace']")	
	protected WebElement trassAnMenu;

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

	//Меню
	
	@FindBy(xpath = ".//ul[@id='menu_dev']/li[@id='menu-folder-'][2]/a")	
	protected WebElement settingsDevMenu;
	
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

	@FindBy(xpath = ".//ul[@id='menu_qa']//a[@uid='testing-list']")
	protected WebElement testScenariosItem;
	
	@FindBy(xpath = ".//ul[@id='menu_qa']//a[@uid='testing-docs']")
	protected WebElement testPlansItem;

	// ДОКУМЕНТИРОВАНИЕ menu items

	@FindBy(xpath = ".//ul[@id='menu_docs']//a[@uid='helpdocs-docs']")
	protected WebElement docsItem;

	// НАСТРОЙКИ

	// Меню
	@FindBy(xpath = ".//ul[@id='menu_stg']//a[@id='menu-group-workflow']")	
	protected WebElement settingsStateMenu;

	@FindBy(xpath = ".//ul[@id='menu_stg']//a[@id='menu-group-dicts']")	
	protected WebElement settingsDictMenu;

	@FindBy(xpath = ".//ul[@id='menu_stg']//a[@id='menu-group-more']")	
	protected WebElement settingsAddMenu;

	@FindBy(xpath = ".//ul[@id='menu_stg']//a[@id='menu-group-templates']")	
	protected WebElement settingsTemplateMenu;
	
	// Атрибуты
	@FindBy(xpath = ".//ul[@id='menu_stg']//a[text()='Атрибуты']")
	protected WebElement dictsAttrItem;

	// Состояния пожеланий
	@FindBy(xpath = ".//ul[@id='menu_stg']//a[@uid='workflow-issuestate']")
	protected WebElement requestsStateItem;

	// Состояния задач
	@FindBy(xpath = ".//ul[@id='menu_stg']//a[@uid='workflow-taskstate']")
	protected WebElement tasksStateItem;

	// Тэги
	@FindBy(xpath = ".//ul[@id='menu_stg']//a[text()='Тэги']")
	protected WebElement tagsItem;

	// Участники
	@FindBy(xpath = ".//ul[@id='menu_stg']//a[text()='Участники']")
	protected WebElement participantsListItem;

	// Тип требования
	@FindBy(xpath = ".//ul[@id='menu_stg']//a[text()='Тип требования']")
	protected WebElement requirementTypeItem;
	
	// Тип пожелания
	@FindBy(xpath = ".//ul[@id='menu_stg']//a[text()='Тип пожелания']")
	protected WebElement requestTypeItem;
	
	// Права доступа
	@FindBy(xpath = ".//ul[@id='menu_stg']//a[@id='menu-group-permissions']")	
	protected WebElement permissionsMenu;
	
	@FindBy(xpath = ".//ul[@id='menu_stg']//a[@uid='permissions-settings']")
	protected WebElement permissionsItem;
	
	// Методология
	@FindBy(xpath = ".//ul[@id='menu_stg']//a[text()='Методология']")
	protected WebElement methodologyItem;
	
	// Терминология
	@FindBy(xpath = ".//ul[@id='menu_stg']//a[text()='Терминология']")
	protected WebElement terminologyItem;
	
	// Общие настройки
	@FindBy(xpath = ".//ul[@id='menu_stg']//a[@uid='project-settings']")
	protected WebElement commonSettingsItem;
	
	// Шаблоны базы знаний
	@FindBy(xpath = ".//ul[@id='menu_stg']//a[text()='Шаблоны базы знаний']")
	protected WebElement kbTemplatesItem;
	
	
	@FindBy(xpath = ".//ul[@id='menu_stg']//a[@uid='ee-projectlinks']")
	protected WebElement linkedProjectsItem;
	
	@FindBy(xpath = ".//ul[@id='menu_stg']//a[text()='Версионирование']")
	protected WebElement versioningItem;
	
	@FindBy(xpath = ".//ul[@id='menu_stg']//a[@uid='process-import']")
	protected WebElement loadTemplateItem;
	
	@FindBy(xpath = ".//ul[@id='menu_stg']//a[@uid='process-export']")
	protected WebElement saveTemplateItem;
	
	@FindBy(xpath = ".//a[@uid='profile']")
	protected WebElement mySettingsItem;
	
		
    // --------------Другие элементы------------------ //
	@FindBy(id = "filter-settings")
	protected WebElement asterixBtn;
	
	@FindBy(xpath = "//a[@id='filter-settings']//following-sibling::ul//a[@id='save-report']")
	protected WebElement saveReportBtn;
		
		
	public SDLCPojectPageBase(WebDriver driver) {
		super(driver);

	}

	public SDLCPojectPageBase(WebDriver driver, Project project) {
		super(driver, project);
	}

	// GOTO Methods

	public ProjectMembersPage gotoMembers() {
		settingsLink.click();
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
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(productMenu));
		if (!boardItem.isDisplayed())
			productMenu.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(boardItem));
		boardItem.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(driver.findElement(By.xpath("//table[contains(@id,'requestboard')]"))));
		return new RequestsBoardPage(driver);
	}
	
	
	
	public FunctionsPage gotoFunctions() {
		manageLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(productMenu));
		if (!featuresItem.isDisplayed())
			productMenu.click();
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
		clickOnInvisibleElement(planMenu);
		clickOnInvisibleElement(tasksBoardItem);
		return new TasksBoardPage(driver);
	}
	
	public RequestsStatePage gotoRequestsStatePage() {
		settingsLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(settingsStateMenu));
		if (!requestsStateItem.isDisplayed())
			settingsStateMenu.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(requestsStateItem));
		requestsStateItem.click();
		return new RequestsStatePage(driver);
	}

	public TasksStatePage gotoTasksStatePage() {
		settingsLink.click();
		if (!tasksStateItem.isDisplayed())
			settingsStateMenu.click();
		tasksStateItem.click();
		return new TasksStatePage(driver);
	}

	public AttributeSettingsPage gotoAttributeSettings() {
		settingsLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(dictsAttrItem));
		dictsAttrItem.click();
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
	 * "//a[@data-toggle='dropdown' and contains(text(),'Действия')]")).click();
	 * driver.findElement(By.xpath(
	 * "//a[contains(text(),'Добавить') and contains(@href,'/pm/devprom_webtest/project/tags?class=metaobject&entity=Tag')]"
	 * )).click(); driver.findElement(By.id("TagCaption")).sendKeys(name);
	 * driver.findElement(By.id("TagSubmitBtn")).click();
	 * driver.navigate().to(youCameFrom); return name; }
	 */

	public String[] getTagsList() {
		String youCameFrom = driver.getCurrentUrl();
		settingsLink.click();
		if (!tagsItem.isDisplayed())
			settingsAddMenu.click();
		tagsItem.click();
		String[] tagsList = new String[driver.findElements(
				By.xpath("//tr[contains(@id,'taglist1_row_')]")).size()];
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
							By.xpath("//tr[contains(@id,'projectloglist1_row_')]/td[@id='content']//a[contains(text(),'["
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
		if (!activitiesItem.isDisplayed()) projectMenu.click();
		activitiesItem.click();	
		if (category!='0') 
		 id = driver.findElement(By.xpath("//tr[contains(@id,'projectloglist1_row_')]/td[@id='content']/a[contains(text(),'["+category+"')]")).getText();
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
		clickOnInvisibleElement(projectSettingsLink);
		(new WebDriverWait(driver,waiting)).until(
				ExpectedConditions.presenceOfElementLocated(By.xpath("//a[@uid='dicts-texttemplate']"))
				);
		clickOnInvisibleElement(driver.findElement(By.xpath("//a[@uid='dicts-texttemplate']")));
		return new TextTemplatesPage(driver);
	}

	public RequirementsTypesPage gotoRequirementsTypes() {
		settingsLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(settingsDictMenu));
		if (!requirementTypeItem.isDisplayed())
			settingsDictMenu.click();
		requirementTypeItem.click();
		return new RequirementsTypesPage(driver);
	}
	
	public RequestsTypesPage gotoRequestsTypes() {
		settingsLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(settingsDictMenu));
		if (!requestTypeItem.isDisplayed())
			settingsDictMenu.click();
		requestTypeItem.click();
		return new RequestsTypesPage(driver);
	}
	
	
	public ProjectCommonSettingsPage gotoCommonSettings(){
		settingsLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(commonSettingsItem));
		commonSettingsItem.click();
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
		if (!blogItem.isDisplayed())
			projectMenu.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(blogItem));
		blogItem.click();
		return new BlogPage(driver);
	}
	
	public AllReportsPage gotoAllReports(){
		manageLink.click();
		if (!reportsItem.isDisplayed())
			reportsMenu.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(reportsItem));
		reportsItem.click();
		return new AllReportsPage(driver);
	}
	
	public TimetablePage gotoTimetablePage(){
		manageLink.click();
		if (!spentTimeItem.isDisplayed())
			reportsMenu.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(spentTimeItem));
		spentTimeItem.click();
		return new TimetablePage(driver);
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
		if (!repositoryConnectItem.isDisplayed())
			settingsDevMenu.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(repositoryConnectItem));
		repositoryConnectItem.click();
		return new RepositoryConnectPage(driver);
	}
	
	
	public PermissionsPage gotoPermissionsPage(){
		settingsLink.click();
		if (!permissionsItem.isDisplayed()) permissionsMenu.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(permissionsItem));
		permissionsItem.click();
		return new PermissionsPage(driver);
	}

	public TerminologyPage gotoTerminologyPage() {
		settingsLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(terminologyItem));
		terminologyItem.click();
		return new TerminologyPage(driver);
	}
	
	public String checkLinkName(String partOfURL){
		return driver.findElement(By.xpath("//a[contains(@href,'"+partOfURL+"')]")).getText();
	}

	public ReleasesIterationsPage gotoReleasesIterations() {
		manageLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(planMenu));
		if (!releaseItem.isDisplayed())
			planMenu.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(releaseItem));
		releaseItem.click();
		return new ReleasesIterationsPage(driver);
	}

	public LinkedProjectsPage gotoLinkedProjects() {
		settingsLink.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(linkedProjectsItem));
		linkedProjectsItem.click();
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
	if (!activitiesItem.isDisplayed())
		projectMenu.click();
	(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(activitiesItem));
	activitiesItem.click();
	return new ProjectActivitiesPage(driver);
}

public MilestonesPage gotoMilestones() {
	manageLink.click();
	if (!milestonesItem.isDisplayed())
		planMenu.click();
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

public TraceMatrixPage gotoStoryMapping() {
	analysisLink.click();
	if (!storyMappingItem.isDisplayed())
		productAnMenu.click();
	storyMappingItem.click();
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
	settingsLink.click();
	(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(methodologyItem));
	methodologyItem.click();
	return new MethodologyPage(driver);
}

public SaveTemplatePage gotoSaveTemplatePage() {
	settingsLink.click();
	if(!saveTemplateItem.isDisplayed()) {
		settingsTemplateMenu.click();
	}
	(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(saveTemplateItem));
	saveTemplateItem.click();
	return new SaveTemplatePage(driver);
}

public LoadTemplatePage gotoLoadTemplatePage() {
	settingsLink.click();
	if(!loadTemplateItem.isDisplayed()) {
		settingsTemplateMenu.click();
	}
	(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(loadTemplateItem));
	loadTemplateItem.click();
	return new LoadTemplatePage(driver);
}

public VersioningPage gotoVersioningPage() {
	settingsLink.click();
	if (!versioningItem.isDisplayed())
		settingsAddMenu.click();
	(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(versioningItem));
	versioningItem.click();
	return new VersioningPage(driver);
}


public SaveReportPage saveReport(){
	asterixBtn.click();
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
	settingsLink.click();
	(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(mySettingsItem));
	mySettingsItem.click();
	return new MySettingsPage(driver);
}

public void savePageSettins(){
	asterixBtn.click();
	clickOnInvisibleElement(driver.findElement(By.id("personal-persist")));
	asterixBtn.click();
}

}
