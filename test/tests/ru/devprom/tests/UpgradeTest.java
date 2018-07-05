package ru.devprom.tests;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.annotations.Test;

import ru.devprom.helpers.Configuration;
import ru.devprom.helpers.InstallConfig;

public class UpgradeTest extends AdminTestBase {

	@Test(groups = "Deployment", priority = 1)
	public void installUpdate() {
		FILELOG.info("Starting installUpdate");
		if (InstallConfig.getUpdatePath() == "") {
			FILELOG.debug("Update has not been found on the path: "
					+ InstallConfig.getUpdatePath());
			return;
		}
		driver.get(baseURL + "/admin/updates.php");
		driver.findElement(By.xpath("//a[@id='upload']")).click();
		((JavascriptExecutor) driver).executeScript("document.evaluate(\"//input[@id='Update']\", document, null, 9, null).singleNodeValue.removeAttribute('style')");
		driver.findElement(By.id("Update")).sendKeys(
				InstallConfig.getUpdatePath());
		driver.findElement(By.id("btn")).click();
		(new WebDriverWait(driver, Configuration.getUpgradeTimeout()))
				.until(ExpectedConditions.presenceOfElementLocated(By
						.id("tablePlaceholder")));
		FILELOG.debug("Installing the update done");

	}

}
