package ru.devprom.pages.admin;

import java.awt.AWTException;
import java.io.File;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;

import ru.devprom.helpers.Configuration;

public class UploadPage extends AdminPageBase {

	@FindBy(id = "Update")
	private WebElement browseFile;

	@FindBy(id = "btn")
	private WebElement uploadBtn;

	protected UploadPage(WebDriver driver) {
		super(driver);
		Assert.assertTrue(isElementPresent(By.id("Update")));
		FILELOG.debug("Open Upload update file page");
		FILELOG.debug("Current URL is: " + driver.getCurrentUrl());
	}

	public void doUpload(File zipFile) throws AWTException,
			InterruptedException 
	{
		((JavascriptExecutor) driver).executeScript("document.evaluate(\"//input[@id='Update']\", document, null, 9, null).singleNodeValue.removeAttribute('style')");
		browseFile.sendKeys(zipFile.getAbsolutePath());
		uploadBtn.click();
	}

	public void upload_success(File file) {
		try {
			doUpload(file);
			safeAlertDissmiss();
			FILELOG.info("Ready for uploading correct zip. System upload timeout equals to:  "
					+ Configuration.getUpgradeTimeout() + " seconds");
			/*
			 * (new
			 * WebDriverWait(driver,Configuration.getUpgradeTimeout())).until
			 * (new ExpectedCondition<Boolean>(){ public Boolean apply(WebDriver
			 * d) { safeAlertDissmiss(); if
			 * (d.findElement(By.className("alert-error")).isDisplayed() ||
			 * d.getPageSource().contains("Лог установки обновления")) return
			 * true; // if
			 * (d.findElement(By.id("tablePlaceholder")).isEnabled()) return
			 * true; else return false; } });
			 * Assert.assertFalse(driver.findElement
			 * (By.className("alert-error")).isDisplayed());
			 */
			(new WebDriverWait(driver, Configuration.getUpgradeTimeout()))
					.until(ExpectedConditions.presenceOfElementLocated(By
							.id("tablePlaceholder")));
		} catch (Exception e) {
			e.printStackTrace();
		}

		// return new UpdatesPage(driver);
	}

	public UploadPage upload_error(File file) {
		try {
			doUpload(file);
			FILELOG.info("Ready for uploading fake file. Waiting for alert. System upload timeout equals to:  "
					+ Configuration.getUpgradeTimeout() + " seconds");
			(new WebDriverWait(driver, Configuration.getUpgradeTimeout()))
					.until(ExpectedConditions.presenceOfElementLocated(By
							.className("alert-error")));

		} catch (Exception e) {
			e.printStackTrace();
		}

		return new UploadPage(driver);
	}

}
