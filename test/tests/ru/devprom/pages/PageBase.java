package ru.devprom.pages;

import java.awt.AWTException;
import java.awt.Robot;
import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.TimeUnit;

import org.apache.log4j.Level;
import org.apache.log4j.LogManager;
import org.apache.log4j.Logger;
import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.Point;
import org.openqa.selenium.StaleElementReferenceException;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.TimeoutException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;
import org.openqa.selenium.support.ui.ExpectedCondition;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;

import ru.devprom.helpers.Configuration;
import ru.devprom.helpers.WebDriverPointerRobot;
import ru.devprom.items.Project;
import ru.devprom.items.User;
import ru.devprom.pages.admin.ActivitiesPage;
import ru.devprom.pages.allprojects.AllProjectsPageBase;
import ru.devprom.pages.kanban.KanbanPageBase;
import ru.devprom.pages.project.IProjectBase;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.scrum.ScrumPageBase;
import ru.devprom.pages.support.SupportPageBase;
import org.openqa.selenium.internal.Locatable;

//Base class for all the pages except for Login Page
//has all the header elements and functions
public class PageBase {
	protected final WebDriver driver;
	protected final Logger FILELOG = LogManager.getLogger("MAIN");
	protected final int timeoutValue = Configuration.getTimeout();
	protected final int waiting = Configuration.getWaiting();

	@FindBy(id = "filter-settings")
	protected WebElement filterBtn;

	// Web Elements
	@FindBy(xpath = "//a[contains(.,'Создать проект')]")//".//a[@href='/projects/new']")
	private WebElement newProjectLink;

	@FindBy(xpath = "//a[@id='navbar-project']/following-sibling::ul//a[text()='Мои проекты']")
	private WebElement myProjectsLink;

	@FindBy(xpath = "//a[@id='navbar-user-menu']/following-sibling::ul//a[text()='Мои отчеты']")
	private WebElement myReportsLink;

	@FindBy(id = "navbar-project")
	private WebElement companyLink;

	@FindBy(id = "navbar-quick-create")
	private WebElement createLink;

	@FindBy(xpath = "//a[@id='admincontact']")
	private WebElement requestToAdminLink;

	@FindBy(xpath = "//a[@href='/admin/']")
	private WebElement adminTools;

	@FindBy(id = "navbar-user-menu")
	private WebElement userLink;

	@FindBy(xpath = "//a[@id='navbar-user-menu']/following-sibling::ul//a[@href='/profile']")
	private WebElement profileLink;

	@FindBy(xpath = "//a[@id='navbar-user-menu']/following-sibling::ul//a[@href='/logoff']")
	private WebElement logOutLink;

	public PageBase(WebDriver driver) {
		PageFactory.initElements(driver, this);
		this.driver = driver;

		// Set LogLevel from the configuration file
		switch (Configuration.getLoglevel()) {
		case "debug":
			FILELOG.setLevel(Level.DEBUG);
			break;
		case "info":
			FILELOG.setLevel(Level.INFO);
			break;
		case "warn":
			FILELOG.setLevel(Level.WARN);
			break;
		case "error":
			FILELOG.setLevel(Level.ERROR);
			break;
		default:
			FILELOG.setLevel(Level.DEBUG);
		}

		// Checking for error 500
		if (driver.getCurrentUrl().equalsIgnoreCase(
				Configuration.getBaseUrl() + "/500")
				|| driver.getPageSource().contains(
						"500 / Internal Server Error")) {
			driver.navigate().back();
			Assert.fail("Internal Server Error 500");
		}

		// Checking for error 404
		if (driver.getPageSource().contains("404/Not Found")) {
			driver.navigate().back();
			Assert.fail("Error 404/Not Found");
		}

	}

	public boolean isTextPresent(String text) {
		return driver.findElements(By.xpath("//*[contains(.,'"+text+"')]")).size() > 0;
	}

	public ActivitiesPage goToAdminTools() {
		companyLink.click();
		adminTools.click();
		FILELOG.debug("Opening Administrative Tools page");
		return new ActivitiesPage(driver);
	}

	public ProjectNewPage createNewProject() {
		companyLink.click();
		newProjectLink.click();
		FILELOG.debug("Opening Create New Project page");
		return new ProjectNewPage(driver);
	}

	public LoginPage logOut() {
		userLink.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOf(logOutLink));
		logOutLink.click();
		// catching "Вы действительно хотите покинуть страницу?" alert
		try {
			driver.switchTo().alert().accept();
		} catch (org.openqa.selenium.NoAlertPresentException e) {
			// no alert no problem
		}
		FILELOG.debug("Logout done");
		return new LoginPage(driver);
	}

	public void safeAlertDissmiss() {
		try {
			driver.switchTo().alert().dismiss();
		} catch (org.openqa.selenium.NoAlertPresentException e) {
			// do nothing - no alert no problem
		}
	}

	public void safeAlertAccept() {
		try {
			driver.switchTo().alert().accept();
		} catch (org.openqa.selenium.NoAlertPresentException e) {
			// do nothing - no alert no problem
		}
	}

	public User getCurrentUser() {
		userLink.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOf(profileLink));
		profileLink.click();
		String username = driver.findElement(By.name("Login")).getAttribute(
				"value");
		String password = "";
		String usernameLong = driver.findElement(By.id("Caption"))
				.getAttribute("value");
		String email = driver.findElement(By.id("Email")).getAttribute("value");
		driver.navigate().back();
		return new User(username, password, usernameLong, email, true, true);

	}

	public SDLCPojectPageBase gotoSDLCProject(String projectname) {
		companyLink.click();
		driver.findElement(
				By.xpath(".//a[@id='navbar-project']/following-sibling::ul//a[text()='"
						+ projectname + "']")).click();
		return new SDLCPojectPageBase(driver);
	}

	public IProjectBase gotoProject(Project project) {
		companyLink.click();

		clickOnInvisibleElement(driver
				.findElement(By
						.xpath(".//a[@id='navbar-project']/following-sibling::ul//a[text()='"
								+ project.getName() + "']")));
		switch (project.getTemplate().getName()) {
		case "Scrum":
			return new ScrumPageBase(driver, project);
			// RuScrumSimpleFavsPage(driver, project);
		case "Kanban":
			return new KanbanPageBase(driver, project);
		case "Waterfall":
			return new SDLCPojectPageBase(driver, project);
		case "Поддержка":
			return new SupportPageBase(driver, project);
		default:
			throw new RuntimeException("The class for project type "
					+ project.getTemplate().getName() + " is not implemented");
		}
	}

	public Boolean hasErrorMessage(String message) {
		return getTextError().contains(message);
	}

	private String getTextError() {
		return driver.findElement(By.className("alert-error")).getText();
	}

	public Boolean isElementPresent(By by) {
		return (driver.findElements(by).size() > 0);
	}

	public void clickOnInvisibleElement(WebElement element) {
		mouseMove(element); 
		String script = "var object = arguments[0];"
				+ "var theEvent = document.createEvent(\'MouseEvent\');"
				+ "theEvent.initMouseEvent(\'click\', true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);"
				+ "object.dispatchEvent(theEvent);";
		((JavascriptExecutor) driver).executeScript(script, element);
	}

	public void clickOnInvisibleElementWithCtrl(WebElement element) {
		mouseMove(element);
		String script = "var object = arguments[0];"
				+ "var theEvent = document.createEvent(\'MouseEvent\');"
				+ "theEvent.initMouseEvent(\'click\', true, true, window, 0, 0, 0, 0, 0, true, false, false, false, 0, null);"
				+ "object.dispatchEvent(theEvent);";
		((JavascriptExecutor) driver).executeScript(script, element);
	}

	/*
	 * public void clickOnInvisibleElement(WebElement element) {
	 * JavascriptExecutor executor = (JavascriptExecutor)driver;
	 * executor.executeScript("arguments[0].click();", element); }
	 */

	public void makeElementVisibleByJavascript(WebElement element) {
		String script = "var element = arguments[0];"
				+ "element.style.display='block';";
		((JavascriptExecutor) driver).executeScript(script, element);
	}

	public void makeElementTypeHiddenVisibleByJavascript(WebElement element) {
		String script = "var element = arguments[0];"
				+ "element.type='visible';";
		((JavascriptExecutor) driver).executeScript(script, element);
	}

	public void scrollWithOffset(WebElement webElement, int x, int y) {

		String code = "window.scroll(" + (webElement.getLocation().x + x) + ","
				+ (webElement.getLocation().y + y) + ");";

		((JavascriptExecutor) driver).executeScript(code, webElement, x, y);

	}

	@Deprecated
	protected void selectItemInList(String keyword) {
		try {
			Thread.sleep(500);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		driver.findElement(
				By.xpath("html/body/ul/li/a[contains(.,'" + keyword
						+ "')]")).click();
	}

	public void autocompleteSelect(String target) {
		autocompleteSelect(target, false);
	}

	public void autocompleteSelect(String target, boolean strict) {
		int times = 3;
		while (times-- > 0) {
			try {
				Thread.sleep(300);
			} catch (InterruptedException e) {
			}
			try {
				List<WebElement> list = driver
						.findElements(
							strict 
								? By.xpath("//ul[contains(@class,'ui-autocomplete')]/li/*[text()='"
										+ target + "']")
								: By.xpath("//ul[contains(@class,'ui-autocomplete')]/li/*[contains(.,'"
										+ target + "')]"));
				for (WebElement el : list) {
					if (!el.isDisplayed()) continue;
					while( el.isDisplayed() ) {
						try {
							Thread.sleep(200);
						} catch (InterruptedException e) {
						}
						el.click();
					}
					return;
				}
			} catch (StaleElementReferenceException e) {
				FILELOG.debug("Autocomplete attempt failed");
			}
		}
		FILELOG.error("Autocomplete error");
	}

	public void waitForTextPresent(String text) {
		final int waitRetryDelayMs = 100; // шаг итерации (задержка)
		final int timeOut = 30; // время тайм маута
		boolean first = true;

		for (int milliSecond = 0;; milliSecond += waitRetryDelayMs) {
			if (milliSecond > timeOut * 1000) {
				throw new NoSuchElementException("");
			}
			if (driver.getPageSource().contains(text)) {
				if (!first)
					;
				break;
			}
			if (first)
				;
			first = false;
			try {
				Thread.sleep(waitRetryDelayMs);
			} catch (InterruptedException e) {
				e.printStackTrace();
			}
		}
	}

	public List<String> getSelectValues(WebElement element) {
		List<String> result = new ArrayList<String>();
		List<WebElement> options = new Select(element).getOptions();
		for (WebElement option : options) {
			if (!option.getText().isEmpty())
				result.add(option.getText().trim());
		}
		return result;
	}

	public SendRequestForm sendRequestToAdmin() {
		driver.navigate().to(Configuration.getBaseUrl() + "/profile");
		createLink.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOf(requestToAdminLink));
		requestToAdminLink.click();
		return new SendRequestForm(driver);
	}

	public void scrollToElement(WebElement element) {
		((JavascriptExecutor) driver).executeScript(
				"arguments[0].scrollIntoView(true);", element);
	}

	/**
	 * returns script load page time in seconds
	 * 
	 * @return
	 */
	public double getScriptExecutionTime() {
		return 0.0;
	}

	public boolean isAllProjectsEnabled() {
		return !driver
				.findElements(
						By.xpath("//a[@id='navbar-project']/following-sibling::ul//a[text()='Все проекты']"))
				.isEmpty();
	}

	public AllProjectsPageBase gotoAllProjects() {
		companyLink.click();
		WebElement allProjectsLink = driver
				.findElement(By
						.xpath("//a[@id='navbar-project']/following-sibling::ul//a[text()='Все проекты']"));
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOf(allProjectsLink));
		allProjectsLink.click();
		return new AllProjectsPageBase(driver);
	}

	public MyProjectsPageBase gotoMyProjects() {
		companyLink.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOf(myProjectsLink));
		myProjectsLink.click();
		return new MyProjectsPageBase(driver);
	}

	public MyReportsPageBase gotoMyReports() {
		userLink.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOf(myReportsLink));
		myReportsLink.click();
		return new MyReportsPageBase(driver);
	}

	public void submitDialog(WebElement btn) {
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(btn));
		while(true) {
			try {
				if ( btn.isEnabled() ) btn.click();
				try {
					(new WebDriverWait(driver, 1)).until(elementDissapeared(By.xpath("//div[@id='modal-form']")));
					break;
				}
				catch( TimeoutException e ) {
				}
			}
			catch( NoSuchElementException e ) {
				break;
			}
			catch( StaleElementReferenceException e ) {
				break;
			}
		}
	}

	public void cancelDialog(WebElement btn) 
	{
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(btn));
		while(true) {
			try {
				if ( btn.isEnabled() ) btn.click();
				try {
					(new WebDriverWait(driver, 1)).until(elementDissapeared(By.xpath("//div[@id='modal-form']")));
					break;
				}
				catch( TimeoutException e ) {
				}
			}
			catch( NoSuchElementException e ) {
				break;
			}
			catch( StaleElementReferenceException e ) {
				break;
			}
		}
	}

	public void submitDelete(WebElement btn) {
		btn.click();
		safeAlertAccept();
		(new WebDriverWait(driver, waiting)).until(elementDissapeared(By
				.xpath("//div[@id='modal-form']")));

	}

	public void waitUntilElementPresent(final String xpath) {
		driver.manage().timeouts().implicitlyWait(200, TimeUnit.MILLISECONDS);
		new WebDriverWait(this.driver, waiting)
				.until(new ExpectedCondition<Boolean>() {
					public Boolean apply(WebDriver arg0) {
						return (!driver.findElements(By.xpath(xpath)).isEmpty() || !driver
								.findElement(By.xpath(xpath)).isDisplayed());
					}
				});
		driver.manage().timeouts()
				.implicitlyWait(Configuration.getTimeout(), TimeUnit.SECONDS);
	}

	public void waitForFilterActivity() {
		try {
			driver.findElement(By
					.xpath("//*[@id='filter-settings']/i[contains(@class,'filter-activity')]"));
		} catch (org.openqa.selenium.NoSuchElementException e) {
			FILELOG.error("Состояние индикатора процесса не активно");
		}
		(new WebDriverWait(driver, waiting))
				.until(ExpectedConditions.invisibilityOfElementLocated(By
						.xpath("//*[@id='filter-settings']/i[contains(@class,'filter-activity')]")));
	}

	public List<String> getProjectsList() {
		WebElement link = driver.findElement(By.id("navbar-project"));
		link.click();
		List<String> result = new ArrayList<String>();
		List<WebElement> we = link
				.findElements(By
						.xpath("./following-sibling::ul//table[contains(@class,'table')]//a[contains(@href,'pm')]"));
		for (WebElement el : we) {
			result.add(el.getText());
		}
		link.click();
		return result;
	}

	/**
	 * Показать постранично, по N элементов на странице Согласно меню N.equals -
	 * "5", "20", "60" или "all"
	 * 
	 * @param n
	 * @return
	 */

	public PageBase showNRows(String n) {
		filterBtn.click();
		String script = "filterLocation.setup( 'rows=" + n + "', 0 );";
		((JavascriptExecutor) driver).executeScript(script);
		filterBtn.click();
		return new PageBase(driver);
	}

	/**
	 * Возвращает количество страниц при постраничном разбиении (по количеству
	 * строк)
	 * 
	 * @return
	 */
	public int getPagesCount() {
		return driver.findElements(
				By.xpath("//div[contains(@class,'pagination')]//li")).size();
	}

	public PageBase showPage(String number) {
		driver.findElement(
				By.xpath("//div[contains(@class,'pagination')]//a[text()='"
						+ number + "']")).click();
		return new PageBase(driver);
	}

	public int getDataRowsCount() {
		return driver
				.findElements(
						By.xpath("//div[@id='tablePlaceholder']//tr[contains(@id,'row')]"))
				.size();
	}

	public void waitForFilterIconReload() {
		driver.findElement(By.xpath("//section[contains(@class,'content')]"))
				.click();
		(new WebDriverWait(driver, waiting))
				.until(ExpectedConditions.presenceOfElementLocated(By
						.xpath("//*[@id='filter-settings']//i[contains(@class,'filter-activity')]")));
		(new WebDriverWait(driver, waiting))
				.until(ExpectedConditions.presenceOfElementLocated(By
						.xpath("//*[@id='filter-settings']//i[not (contains(@class,'filter-activity'))]")));
	}

	public boolean isBoxChechedJQuery(String boxId) {
		return (Boolean) ((JavascriptExecutor) driver)
				.executeScript("return $(\"#" + boxId
						+ "\").prop(\"checked\");");
	}

	public boolean isErrorAlert() {
		return !driver.findElements(By.className("alert-error")).isEmpty();
	}

	public ExpectedCondition<Boolean> elementDissapeared(final By locator) {
		return new ExpectedCondition<Boolean>() {
			@Override
			public Boolean apply(WebDriver driver) {
				try {
					driver.manage().timeouts()
							.implicitlyWait(1, TimeUnit.MILLISECONDS);
					return !(driver.findElement(locator).isDisplayed());

				} catch (NoSuchElementException
						| StaleElementReferenceException e) {
					return true;
				} finally {
					driver.manage().timeouts()
							.implicitlyWait(timeoutValue, TimeUnit.SECONDS);
				}
			}

			@Override
			public String toString() {
				return "element to no longer be visible: " + locator;
			}
		};
	}

	public void clickTab(String referenceName) {
		try {
			WebElement tabLink = driver.findElement(
					By.xpath("//div[@id='modal-form']//a[@href='#tab-"
							+ referenceName + "']"));
			if ( !tabLink.findElement(By.xpath("..")).getAttribute("class").contains("ui-state-active") ) {
				tabLink.click();
			}
		} catch (NoSuchElementException e) {
			FILELOG.error("Tab is not found: " + referenceName);
		}
	}

    public void clickLink() {
        companyLink.click();
    }

    public ProjectNewPage clickNewProject() {
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOf(newProjectLink));
        newProjectLink.click();
	FILELOG.debug("Opening Create New Project page");
	return new ProjectNewPage(driver);
    }
    
    public void mouseMove( WebElement element ) {
    	if ( !Configuration.robotPointerUsed() ) return;
    	WebDriverPointerRobot.mouseMove(element);
    }

    public void waitForDialog() {
		(new WebDriverWait(driver, waiting))
			.until(ExpectedConditions.visibilityOfElementLocated(
				By.xpath("//div[@id='modal-form']")));
    }
}
