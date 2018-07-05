package ru.devprom.pages.project.settings;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.items.Template;
import ru.devprom.items.Template.Lang;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class SaveTemplatePage extends SDLCPojectPageBase {

	
	@FindBy(id = "Caption")
	protected WebElement captionEdit;
	
	@FindBy(id = "Description")
	protected WebElement descriptionEdit;
	
	@FindBy(id = "FileName")
	protected WebElement fileNameEdit;
	
	@FindBy(id = "Language")
	protected WebElement languageEdit;
		
	@FindBy(id = "btn")
	protected WebElement submitBtn;
	
	public SaveTemplatePage(WebDriver driver) {
		super(driver);
	}

	public SaveTemplatePage(WebDriver driver, Project project) {
		super(driver, project);
	}
	
	public SaveTemplatePage saveTemplate(Template template) {
		captionEdit.clear();
		captionEdit.sendKeys(template.getName());
		descriptionEdit.clear();
		descriptionEdit.sendKeys(template.getDescription());
		fileNameEdit.clear();
		fileNameEdit.sendKeys(template.getFileName());
		if (template.getLanguage().equals(Lang.english)) new Select(languageEdit).selectByValue("2");
		submitBtn.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//div[contains(@class,'alert')]")));
		return new SaveTemplatePage(driver);
	}
	
	public boolean isSuccess(){
		return !driver.findElements(By.xpath("//div[contains(@class,'alert-success') and text()='Шаблон успешно сохранен']")).isEmpty();
	}
	
}
