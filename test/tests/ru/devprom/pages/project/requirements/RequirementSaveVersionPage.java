package ru.devprom.pages.project.requirements;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class RequirementSaveVersionPage extends SDLCPojectPageBase {

	@FindBy(id="Caption")
	protected WebElement captionInput;
	
	@FindBy(id="Description")
	protected WebElement descriptionInput;
	
	@FindBy(id="btn")
	protected WebElement submitBtn;
	
	
	public RequirementSaveVersionPage(WebDriver driver) {
		super(driver);
	}

	public RequirementSaveVersionPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	
	public RequirementViewPage saveVersion(String versionName){
		captionInput.sendKeys(versionName);
		submitDialog(submitBtn);
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//table[contains(@id,'pmwikidocumentlist')]")));
		return new RequirementViewPage(driver);
	}
	
	public RequirementViewPage saveVersion(String versionName, String description){
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.id("Caption")));
		captionInput.sendKeys(versionName);
        descriptionInput.sendKeys(description);
        submitDialog(submitBtn);
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//table[contains(@id,'pmwikidocumentlist')]")));
		return new RequirementViewPage(driver);
	}
	
}
