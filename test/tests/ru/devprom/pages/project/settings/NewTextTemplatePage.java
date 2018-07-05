package ru.devprom.pages.project.settings;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class NewTextTemplatePage extends SDLCPojectPageBase
{
	@FindBy(id = "pm_TextTemplateCaption")
	protected WebElement captionEdit;

	@FindBy(id = "ObjectClassText")
	protected WebElement objectClass;

	@FindBy(id = "pm_TextTemplateIsDefault")
	protected WebElement defaultCheckbox;
	
	@FindBy(id = "pm_TextTemplateSubmitBtn")
	protected WebElement saveBtn;
	
	public NewTextTemplatePage(WebDriver driver) {
		super(driver);
	}

	public NewTextTemplatePage(WebDriver driver, Project project) {
		super(driver, project);
	}
	
	public TextTemplatesPage create(String name, String content, String entityName, boolean isDefault)
	{
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(captionEdit));
		captionEdit.clear();
		captionEdit.sendKeys(name);
		
		addContent(content);

		objectClass.sendKeys(entityName);
		autocompleteSelect(entityName);
		
		if(isDefault) defaultCheckbox.click();
		submitDialog(saveBtn);
		
		return new TextTemplatesPage(driver);
	}

	public void addContent(String content)
	{
		CKEditor we = new CKEditor(driver);
		we.changeText(content);
	}
}
