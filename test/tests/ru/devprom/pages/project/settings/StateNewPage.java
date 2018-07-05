package ru.devprom.pages.project.settings;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requests.RequestsStatePage;

public class StateNewPage extends SDLCPojectPageBase {

	
	@FindBy(id = "pm_StateCaption")
	protected WebElement nameEdit;
	
	@FindBy(id = "pm_StateDescription")
	protected WebElement descriptionEdit;
	
	@FindBy(id = "pm_StateSubmitBtn")
	protected WebElement submitBtn;
	
	public StateNewPage(WebDriver driver) {
		super(driver);
	}

	public StateNewPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	
	public RequestsStatePage createNewState(String name){
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(nameEdit));
		nameEdit.clear();
		nameEdit.sendKeys(name);
		submitDialog(submitBtn);
		return new RequestsStatePage(driver);
	}
	
}
