package ru.devprom.pages.project;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import ru.devprom.items.Project;

public class SaveReportPage extends SDLCPojectPageBase {

	@FindBy(id = "pm_CustomReportCaption")
	protected WebElement nameInput;

	@FindBy(id = "pm_CustomReportIsHandAccess")
	protected WebElement addFavsBox;
	
	@FindBy(id = "pm_CustomReportDescription")
	protected WebElement descriptionInput;
	
	@FindBy(id = "pm_CustomReportSubmitBtn")
	protected WebElement submitBtn;
	
	
	public SaveReportPage(WebDriver driver) {
		super(driver);
	}

	public SaveReportPage(WebDriver driver, Project project) {
		super(driver, project);
	}
	
	public SDLCPojectPageBase saveReport(String name, boolean isSaveFavs, String description){
		waitForDialog();
		nameInput.clear();
		nameInput.sendKeys(name);
		if (!isSaveFavs) addFavsBox.click();
		if (!description.isEmpty()) descriptionInput.sendKeys(description);
		submitDialog(submitBtn);
		return new SDLCPojectPageBase(driver);
	}

}
