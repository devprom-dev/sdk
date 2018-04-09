package ru.devprom.pages.project.settings;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class TextTemplatesPage extends SDLCPojectPageBase {

	@FindBy(xpath = "//a[@id='new-texttemplate']")
	protected WebElement addTemplateBtn;
	
	@FindBy(xpath = "//a[@id='bulk-delete']")
	protected WebElement removeTemplateBtn;
	
	public TextTemplatesPage(WebDriver driver) {
		super(driver);
	}

	public TextTemplatesPage(WebDriver driver, Project project) {
		super(driver, project);
	}
	
	public NewTextTemplatePage createNewTemplate()
	{
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(addTemplateBtn));
		addTemplateBtn.click();
		waitForDialog();
		return new NewTextTemplatePage(driver);
	}
}
