package ru.devprom.pages;

import org.apache.log4j.LogManager;
import org.apache.log4j.Logger;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;

import ru.devprom.helpers.Configuration;

public class LoginPage {

	@FindBy(id = "login")
	private WebElement usernameLocator;
	@FindBy(id = "pass")
	private WebElement passwordLocator;
	@FindBy(className = "btn-primary")
	private WebElement loginButtonLocator;
	private final WebDriver driver;
	protected final Logger FILELOG = LogManager.getLogger("MAIN");

	public LoginPage(WebDriver driver) {
		PageFactory.initElements(driver, this);
		this.driver = driver;
		FILELOG.debug("Entering login page. URL is: " + driver.getCurrentUrl());
		Assert.assertTrue(driver.getPageSource().contains("id=\"login\""));
	}

	public LoginPage typeUsername(String username) {
		driver.findElement(By.id("login")).clear();
		driver.findElement(By.id("login")).sendKeys(username);
		return this;
	}

	public LoginPage typePassword(String password) {
		passwordLocator.clear();
		passwordLocator.sendKeys(password);
		return this;
	}

	public FavoritesPage submitLogin() {
		loginButtonLocator.click();
		(new WebDriverWait(driver, Configuration.getWaiting())).until(ExpectedConditions.presenceOfElementLocated(By.id("navbar-company-name")));
		return new FavoritesPage(driver);
	}

	public LoginPage submitExpectingFailure() {
		loginButtonLocator.click();
		return new LoginPage(driver);
	}

	public FavoritesPage loginAs(String username, String password) {
		typeUsername(username);
		typePassword(password);
		return submitLogin();
	}

	public String getErrorMessage(){
		return driver.findElement(By.className("alert-error")).getText();
	}
	
}
