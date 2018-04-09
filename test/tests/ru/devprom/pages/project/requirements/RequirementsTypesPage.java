package ru.devprom.pages.project.requirements;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class RequirementsTypesPage extends SDLCPojectPageBase {

	
	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(text(),'Действия')]")
	protected WebElement actionsBtn;
	
	@FindBy(xpath = "//a[contains(.,'Добавить') and contains(@class,'append-btn')]")
	protected WebElement addRequirementTypeBtn;
	
	@FindBy(xpath = "//a[@id='bulk-delete']")
	protected WebElement removeRequirementTypeBtn;
	
	public RequirementsTypesPage(WebDriver driver) {
		super(driver);
	}

	public RequirementsTypesPage(WebDriver driver, Project project) {
		super(driver, project);
	}
	

	public RequirementsNewTypePage createNewRequirementType() {
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(addRequirementTypeBtn));
		addRequirementTypeBtn.click();
		waitForDialog();
		return new RequirementsNewTypePage(driver);
	}
	
	

}
