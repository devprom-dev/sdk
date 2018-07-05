package ru.devprom.pages.project.settings;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.Select;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class MethodologyPage extends SDLCPojectPageBase {


	@FindBy(id = "pm_MethodologyIsReleasesUsed")
	private WebElement planSelect;
	
	@FindBy(id = "pm_MethodologySubmitBtn")
	private WebElement submitBtn;
	
	public MethodologyPage(WebDriver driver) {
		super(driver);
	}

	public MethodologyPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public void selectPlanning(String planning){
		(new Select(planSelect)).selectByVisibleText(planning);
	}
	
	public MethodologyPage saveChanges(){
		submitBtn.click();
		return new MethodologyPage(driver);
	}
	
}
