package ru.devprom.pages.project.requirements;

import java.util.List;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class RequirementsNewTypePage extends SDLCPojectPageBase {


	@FindBy(id = "WikiPageTypeCaption")
	protected WebElement nameEdit;

	@FindBy(id = "WikiPageTypeShortCaption")
	protected WebElement shortNameEdit;
	
	@FindBy(id = "WikiPageTypeDescription")
	protected WebElement descriptionEdit;
	
	@FindBy(id = "DefaultPageTemplateText")
	protected WebElement defaultTemplatePage;
	
	@FindBy(id = "WikiPageTypeReferenceName")
	protected WebElement referenceNameEdit;

	@FindBy(id = "WikiPageTypeWikiEditor")
	protected WebElement wikiEditorSelect;
	
	@FindBy(id = "WikiPageTypeOrderNum")
	protected WebElement numberEdit;
	
	@FindBy(id = "WikiPageTypeSubmitBtn")
	protected WebElement saveBtn;
	
	
	public RequirementsNewTypePage(WebDriver driver) {
		super(driver);
	}

	public RequirementsNewTypePage(WebDriver driver, Project project) {
		super(driver, project);
	}
	
	
	public RequirementsTypesPage createNewRequirementType(String name){
		nameEdit.clear();
		nameEdit.sendKeys(name);
		referenceNameEdit.sendKeys(name);
		saveBtn.click();
		return new RequirementsTypesPage(driver);
	}
	
public RequirementsTypesPage createNewRequirementType(String name, String shortName, String description, String defaultTemplateName, String wikiEditorName, String number){
	(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(nameEdit));
	nameEdit.clear();
	nameEdit.sendKeys(name);
	setShortName(shortName);
	if (!description.isEmpty()) setDescription(description);
	if (!defaultTemplateName.isEmpty()) setDefaultTemplateName(defaultTemplateName);
	if (!wikiEditorName.isEmpty()) setWikiEditorName(wikiEditorName);
	if (!number.isEmpty()) setNumber(number);
	submitDialog(saveBtn);
	
		return new RequirementsTypesPage(driver);
	}

public void setShortName(String shortname){
	shortNameEdit.clear();
	shortNameEdit.sendKeys(shortname);
}

public void setDescription(String description){
	descriptionEdit.clear();
	descriptionEdit.sendKeys(description);
}

public void setDefaultTemplateName(String defaultTemplateName)
{
	defaultTemplatePage.sendKeys(defaultTemplateName);
	autocompleteSelect(defaultTemplateName);
}

public void setWikiEditorName(String wikiEditorName){
	(new Select(wikiEditorSelect)).selectByValue(wikiEditorName);
}


public void setNumber(String number){
	numberEdit.clear();
	numberEdit.sendKeys(number);
}



}
