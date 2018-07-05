package ru.devprom.tests;

import org.openqa.selenium.By;
import org.openqa.selenium.TimeoutException;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.annotations.AfterClass;
import org.testng.annotations.BeforeClass;

import ru.devprom.helpers.Configuration;
import ru.devprom.pages.LoginPage;

public class ProjectTestBase extends TestBase
{
	@BeforeClass
	public void doLogin() throws InterruptedException {
		int attempts = Configuration.getLoginAttempts();
		FILELOG.debug("do login");
		driver.get(Configuration.getBaseUrl());
		FILELOG.debug("Opening login page");
		LoginPage page = new LoginPage(driver);
		while (true) {
			if (attempts == 0) {
				driver.close();
				throw new IllegalStateException(
						"Can't do login. Check your credentials");
			}
			try {
				FILELOG.info("Login as " + username + ":" + password);
				page.loginAs(username, password);
				(new WebDriverWait(driver, waiting)).until(ExpectedConditions
						.presenceOfElementLocated(By.id("main")));
				break;
			} catch (TimeoutException e) {
				attempts--;
				FILELOG.warn("Login attempt failed, " + attempts
						+ " attempts left");
			}
		}
	}

	@AfterClass
	public void doLogout() throws InterruptedException {
		FILELOG.debug("do logout");
		driver.findElement(By.id("navbar-user-menu")).click();
		driver.findElement(By.xpath("//a[@href='/logoff']")).click();
		// catching "Вы действительно хотите покинуть страницу?" alert
		try {
			driver.switchTo().alert().accept();
		} catch (org.openqa.selenium.NoAlertPresentException e) {
			// no alert no problem
		}
		FILELOG.info("Logout done");

	}
}
