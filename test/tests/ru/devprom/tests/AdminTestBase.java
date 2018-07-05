package ru.devprom.tests;

import org.openqa.selenium.By;
import org.openqa.selenium.TimeoutException;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.annotations.AfterClass;
import org.testng.annotations.BeforeClass;

import ru.devprom.helpers.Configuration;
import ru.devprom.pages.FavoritesPage;
import ru.devprom.pages.LoginPage;

public class AdminTestBase extends TestBase {

	@BeforeClass
	public void doLogin() throws InterruptedException {
		int attempts = Configuration.getLoginAttempts();
		FILELOG.debug("do login");
		driver.get(Configuration.getBaseUrl());
		FILELOG.debug("Opening login page");
		LoginPage page = new LoginPage(driver);
		FavoritesPage fp;
		while (true) {
			if (attempts == 0) {
				driver.close();
				throw new IllegalStateException(
						"Can't do login. Check your credentials");
			}
			try {
				FILELOG.info("Login as " + username + ":" + password);
				fp = page.loginAs(username, password);
				(new WebDriverWait(driver, waiting)).until(ExpectedConditions
						.presenceOfElementLocated(By.id("main")));
				break;
			} catch (TimeoutException e) {
				attempts--;
				FILELOG.warn("Login attempt failed, " + attempts
						+ " attempts left");
			}
		}
		fp.goToAdminTools();
	}

	@AfterClass
	public void doLogout() throws InterruptedException {
		FILELOG.debug("do logout");
		driver.findElement(By.id("navbar-user-menu")).click();
		FILELOG.debug("Clicked on user menu to logout");
		driver.findElement(By.xpath("//a[@href='/logoff']")).click();
		FILELOG.debug("Clicked on logoff button, waiting for logging out");
		// catching "Вы действительно хотите покинуть страницу?" alert
		try {
			driver.switchTo().alert().accept();
		} catch (org.openqa.selenium.NoAlertPresentException e) {
			// no alert no problem
		}
		FILELOG.info("Logout done");

	}

}
