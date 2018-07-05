package ru.devprom.pages.project.requirements;

import java.io.File;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;


public class RequirementsImportPage extends SDLCPojectPageBase {

@FindBy(id = "Excel")
protected WebElement inputPath;

@FindBy(xpath = "//input[@id = 'btn' and @value ='Импортировать']")
protected WebElement importBtn;

@FindBy(xpath = "//input[@id = 'btn' and @value ='Просмотр']")
protected WebElement previewBtn;


	public RequirementsImportPage(WebDriver driver) {
		super(driver);
	}

	public RequirementsImportPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	
	public RequirementsImportPage importRequirements(File file){
		// make file input visible
		((JavascriptExecutor) driver).executeScript("document.evaluate(\"//input[@id='Excel']\", document, null, 9, null).singleNodeValue.removeAttribute('style')");
		inputPath.sendKeys(file.getAbsolutePath());
		importBtn.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//form//div[contains(@class,'alert-success')]")));
		return new RequirementsImportPage(driver);
	}
	
	public RequirementsImportPage previewRequirements(File file){
		// make file input visible
		((JavascriptExecutor) driver).executeScript("document.evaluate(\"//input[@id='Excel']\", document, null, 9, null).singleNodeValue.removeAttribute('style')");
		inputPath.sendKeys(file.getAbsolutePath());
		previewBtn.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.textToBePresentInElement(By.id("preview"), "Всего строк"));
		return new RequirementsImportPage(driver);
	}
	
	
	
	
}
