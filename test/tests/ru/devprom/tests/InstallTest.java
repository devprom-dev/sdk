package ru.devprom.tests;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;
import org.testng.annotations.Test;

import ru.devprom.helpers.Configuration;
import ru.devprom.helpers.DataProviders;
import ru.devprom.helpers.InstallConfig;
import ru.devprom.items.Project;
import ru.devprom.items.Requirement;
import ru.devprom.items.Template;
import ru.devprom.items.TestScenario;
import ru.devprom.items.User;
import ru.devprom.pages.LoginPage;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.admin.CommonSettingsPage;
import ru.devprom.pages.admin.MailerPage;
import ru.devprom.pages.admin.UsersListPage;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requirements.RequirementNewPage;
import ru.devprom.pages.project.requirements.RequirementViewPage;
import ru.devprom.pages.project.requirements.RequirementsPage;
import ru.devprom.pages.project.testscenarios.TestScenarioNewPage;
import ru.devprom.pages.project.testscenarios.TestScenariosPage;
import ru.devprom.pages.project.testscenarios.TestSpecificationNewPage;
import ru.devprom.pages.project.testscenarios.TestSpecificationsPage;

public class InstallTest extends TestBase {

	// тест для инсталляции без windows installer, набросок
	// @Test (groups="DeploymentInstall", priority=1)
	public void installApplication() {
		if (InstallConfig.isInstall()) {

			driver.get(baseURL + "/install");
			driver.findElement(By.linkText("Установить")).click();
			(new WebDriverWait(driver, installTimeout))
					.until(ExpectedConditions.presenceOfElementLocated(By
							.linkText("Выбор типа лицензии")));
		}
	}

	@Test(groups = "Deployment", priority = 2)
	public void installLicense() throws InterruptedException {
		driver.get(baseURL + "/install");
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By.xpath("//input[@value='" + InstallConfig.getEdition() + "']"))); 
		Assert.assertTrue(findText("Выбор типа лицензии"));
		driver.findElement(
				By.xpath("//input[@value='" + InstallConfig.getEdition() + "']"))
				.click();
		driver.findElement(By.xpath("//input[@value='Ввести ключ']")).click();
		FILELOG.info("License type: " + InstallConfig.getEdition());
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By.id("LicenseKey")));
		driver.findElement(By.id("LicenseValue")).sendKeys(
				InstallConfig.getUsersCount());
		FILELOG.info("UsersCount: " + InstallConfig.getUsersCount());
		driver.findElement(By.id("LicenseKey")).sendKeys(
				InstallConfig.getLicenseKey());
		FILELOG.info("LicenseKey: " + InstallConfig.getLicenseKey());
		driver.findElement(By.id("btn")).click();
		FILELOG.debug("Installing the license");
		
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By
						.xpath("//table[contains(@id,'userlist')]")));
		FILELOG.debug("Installing the license done");
	}

	@Test(groups = "Deployment", priority = 3)
	public void addAdministrator() {
		driver.get(baseURL + "/admin/users.php");
		UsersListPage ulp = new UsersListPage(driver);
		ulp.addFirstUser(new User(username, password, user, "mail", true, true));
		try {
			Thread.sleep(5000);
		} catch (InterruptedException e) {
		}
	}

	@Test(groups = "Deployment", priority = 4)
	public void addProject() throws InterruptedException {
		FILELOG.info("Starting addProject");
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By.id("main")));
		ProjectNewPage npp = (new PageBase(driver)).createNewProject();
		Template SDLC = new Template(
				this.waterfallTemplateName);
		Project project = new Project("DEVPROM.WebTest", "devprom_webtest",SDLC);
		SDLCPojectPageBase sdlcFavsPage = (SDLCPojectPageBase) npp.createNew(project);
		RequirementsPage rp = sdlcFavsPage.gotoRequirements();
		RequirementNewPage nrp = rp.createNewRequirement();
		Requirement testR = new Requirement("Требования", "");
		nrp.create(testR);
		TestSpecificationsPage tsp = sdlcFavsPage.gotoTestPlans();
		TestSpecificationNewPage ntsp = tsp.createNewSpecification();
		ntsp.create(new TestScenario("Функциональное тестирование"));
		Assert.assertEquals(sdlcFavsPage.getProjectTitle(), project.getName());
	}

	@Test(groups = "Deployment", priority = 5)
	public void setCommonSettings() {
		driver.get(baseURL + "/admin/mailer/");
		MailerPage csp = new MailerPage(driver);
		csp.setAdminEmail("info@devprom.ru");
		csp.saveChanges();
	}

	@Test(groups = "Deployment", priority = 6)
	public void turnoffAlerts() throws InterruptedException {
		FILELOG.info("Starting turnoffAlerts");

		//Setup Administration Project
		
		// Uncomment if login operation is needed

		/*
		  FILELOG.info("do login");
		  driver.get(Configuration.getBaseUrl());
		  FILELOG.info("Opening login page");
		  LoginPage page = new  LoginPage(driver);
		  FILELOG.info("Login as "+username+":"+password);
		  FavoritePages fp = page.loginAs(username, password);
		  (new  WebDriverWait(driver,15)).until(ExpectedConditions.presenceOfElementLocated (By.id("main")));
		  FILELOG.debug("Login has been succesfully done, opening Favorites page" );
		  fp.goToAdminTools();*/
		 

		driver.get(baseURL + "/admin/checks.php");

		while (!driver.findElements(By
				.xpath(".//table[@id='systemchecklist1']//tr[contains(@id,'systemchecklist1_row') and child::td[@id='caption' and contains(@style,'color:red')]]")).isEmpty()) {
			FILELOG.debug("Found alert, turning off");
			WebElement alert = driver
					.findElement(By
							.xpath(".//table[@id='systemchecklist1']//tr[contains(@id,'systemchecklist1_row') and child::td[@id='caption' and contains(@style,'color:red')]]"));
			String js = alert.findElement(By.xpath(".//a[text()='Отключить']"))
					.getAttribute("href");
			js = js.replace("%20", " ");
			FILELOG.debug("Javascript executed: " + js);
			((JavascriptExecutor) driver).executeScript(js, alert);
			Thread.sleep(2000);
		}
	}

	// @Test (groups="DeploymentUpgrade", priority=1)
	public void installUpdate() {
		FILELOG.info("Starting installUpdate");
		if (InstallConfig.getUpdatePath() == "") {
			return;
		}
		driver.get(baseURL + "/admin/updates.php");
		(new WebDriverWait(driver, installTimeout)).until(ExpectedConditions
				.presenceOfElementLocated(By
						.xpath("//a[contains(.,'Действия')]")));
		FILELOG.debug("Entering: " + driver.getCurrentUrl());
		driver.findElement(By.xpath("//a[contains(.,'Действия')]"))
				.click();
		driver.findElement(By.xpath("//a[@href='?action=upload']")).click();
		// make file input visible
		((JavascriptExecutor) driver).executeScript("document.evaluate(\"//input[@id='Update']\", document, null, 9, null).singleNodeValue.removeAttribute('style')");
		driver.findElement(By.id("Update")).sendKeys(
				InstallConfig.getUpdatePath());
		driver.findElement(By.id("btn")).click();
		(new WebDriverWait(driver, Configuration.getUpgradeTimeout()))
				.until(ExpectedConditions.presenceOfElementLocated(By
						.id("tablePlaceholder")));
		FILELOG.debug("Installing the update done");

	}

	private Boolean findText(String text) {
		return driver.getPageSource().contains(text);
	}

	public void safeAlertDissmiss() {
		try {
			driver.switchTo().alert().dismiss();
		} catch (org.openqa.selenium.NoAlertPresentException e) {
			// do nothing - no alert no problem
		}
	}

}
