package ru.devprom.pages.project;


import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.Select;


public class ProjectCommonSettingsPage extends SDLCPojectPageBase {

	
	@FindBy(id = "pm_ProjectWikiEditorClass")
	protected WebElement wikiEditorSelector;
	
	@FindBy(id = "pm_ProjectSubmitBtn")
	protected WebElement saveBtn;

	
	public ProjectCommonSettingsPage(WebDriver driver) {
		super(driver);
	}

	public void selectWikiEditor(String value){
		(new Select(wikiEditorSelector)).selectByValue(value);
	}
	
	public SDLCPojectPageBase  saveChanges() {
		saveBtn.click();
		return new SDLCPojectPageBase(driver);
		
	}

    public void resetModuleSettings(){
		driver.findElement(By.xpath("//div[@id='fieldRowModuleSettings']//a[@action='resettodefault']")).click();
    }
}
