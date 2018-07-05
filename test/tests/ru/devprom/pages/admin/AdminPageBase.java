package ru.devprom.pages.admin;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import ru.devprom.pages.PageBase;
import ru.devprom.pages.project.BlocksPage;

public class AdminPageBase extends PageBase {

	// Page Object fields common for the whole Administration section

	@FindBy(xpath = "//li[child::a[@href='http://devprom.ru']]")
	protected WebElement currentVersion;

	@FindBy(id = "navbar-company-name")
	protected WebElement devpromLLC;

	// Администрирование
	@FindBy(xpath = "//a[@href='/admin/']")
	protected WebElement adminTools;

	// ПОЛЬЗОВАТЕЛИ
	@FindBy(xpath = ".//*[@id='menu_main']/li[@id='menu-folder-users']/a")
	protected WebElement sectionUsers;

	// ПРОЕКТЫ
	@FindBy(xpath = ".//*[@id='menu_main']/li[@id='menu-folder-projects']/a")
	protected WebElement sectionProjects;

	// НАСТРОЙКИ
	@FindBy(xpath = ".//*[@id='menu_main']/li[@id='menu-folder-settings']/a")
	protected WebElement sectionSettigs;

	// support
	@FindBy(xpath = ".//*[@id='menu_main']/li[@id='menu-folder-support']/a")
	protected WebElement supportSettigs;
	
	// Обновления
	@FindBy(xpath = "//ul[@id='menu_main']//a[@uid='updates']")
	protected WebElement updatesLink;

	// Резервные копии
	@FindBy(xpath = "//ul[@id='menu_main']//a[@uid='backups']")
	protected WebElement backupsLink;

	// Список (пользователей)
	@FindBy(xpath = "//ul[@id='menu_main']//a[@uid='users']")
	protected WebElement userslistLink;

	// Группы
	@FindBy(xpath = "//ul[@id='menu_main']//a[@module='usergroup']")
	protected WebElement groupsLink;

	// Группы
	@FindBy(xpath = "//ul[@id='menu_main']//a[@module='rights']")
	protected WebElement accessPermissionsLink;
	
	// Блокировки
	@FindBy(xpath = "//ul[@id='menu_main']//a[@uid='blacklist']")
	protected WebElement blacklistLink;

	// Список (проектов)
	@FindBy(xpath = "//ul[@id='menu_main']//a[@uid='projects']")
	protected WebElement projectsLink;
	
	// Плагины
	@FindBy(xpath = "//ul[@id='menu_main']//a[@uid='plugins']")
	protected WebElement pluginsLink;
	
	// Общие настройки
	@FindBy(xpath = "//ul[@id='menu_main']//a[@uid='commonsettings']")
	protected WebElement commonSettingsLink;
	
	// Адреса поддержки
	@FindBy(xpath = "//ul[@id='menu_main']//a[@module='mailboxes']")
	protected WebElement mailboxesSettingsLink;
	
	// Задания
	@FindBy(xpath = "//ul[@id='menu_main']//a[@uid='jobs']")
	protected WebElement tasksLink;
	
	// Задания
	@FindBy(xpath = "//ul[@id='menu_main']//a[@module='ldap']")
	protected WebElement importFromLDAPLink;


	public AdminPageBase(WebDriver driver) {
		super(driver);
	}

	public UsersListPage gotoUsers() {
		if (!userslistLink.isDisplayed())
			sectionUsers.click();
		userslistLink.click();
		return new UsersListPage(driver);
	}
	
	public BlocksPage gotoBlockedUsers() {
		if (!blacklistLink.isDisplayed())
			sectionUsers.click();
		blacklistLink.click();
		return new BlocksPage(driver);
	}

	public UpdatesPage gotoUpdatesPage() {
		updatesLink.click();
		FILELOG.debug("Go to Updates Page");
		return new UpdatesPage(driver);
	}

	public BackUpsPage gotoBackUpsPage() {
		backupsLink.click();
		FILELOG.info("Go to BackUps Page");
		return new BackUpsPage(driver);
	}

	public GroupsPage gotoGroups() {
		if (!groupsLink.isDisplayed())
			sectionUsers.click();
		groupsLink.click();
		return new GroupsPage(driver);
	}
	
	public PluginsPage gotoPlugins() {
		if (!pluginsLink.isDisplayed())
			sectionSettigs.click();
		pluginsLink.click();
		return new PluginsPage(driver);
	}

	public CommonSettingsPage gotoCommonSettings() {
		if (!commonSettingsLink.isDisplayed())
			sectionSettigs.click();
		commonSettingsLink.click();
		return new CommonSettingsPage(driver);
	}
	
	public SupportAddressesPage gotoSupportAddresses() {
		if (!mailboxesSettingsLink.isDisplayed())
			supportSettigs.click();
		mailboxesSettingsLink.click();
		return new SupportAddressesPage(driver);
	}
	
	public SystemTasksPage gotoSystemTasks(){
		tasksLink.click();
		return new SystemTasksPage(driver);
	}
	
	public ImportFromLDAPPage gotoImportFromLDAP(){
		if (!importFromLDAPLink.isDisplayed())
			sectionUsers.click();
		importFromLDAPLink.click();
		return new ImportFromLDAPPage(driver);
	}
	
	public AccessPermissionsPage gotoAccessPermissions(){
		if (!accessPermissionsLink.isDisplayed())
			sectionUsers.click();
		accessPermissionsLink.click();
		return new AccessPermissionsPage(driver);
	}
	
}
