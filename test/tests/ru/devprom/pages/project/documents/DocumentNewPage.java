package ru.devprom.pages.project.documents;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Document;
import ru.devprom.items.Project;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requirements.RequirementViewPage;

public class DocumentNewPage extends SDLCPojectPageBase {

	public DocumentNewPage(WebDriver driver) {
		super(driver);
	}

	public DocumentNewPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public DocumentViewPage createNewDoc(Document doc) 
	{
		WebElement editableArea = driver.findElement(
				By.xpath("//div[contains(@id,'WikiPageContent') and contains(@class,'wysiwyg')]"));
		editableArea.clear();
		editableArea.click();
		(new WebDriverWait(driver, waiting)).until(
				ExpectedConditions.attributeContains(editableArea, "class", "cke_editable_inline"));
		editableArea.sendKeys(doc.getBody());

		WebElement caption = driver.findElement(By.xpath("//div[contains(@id,'WikiPageCaption')]"));
		caption.click();
		(new WebDriverWait(driver, waiting)).until(
				ExpectedConditions.attributeContains(caption, "class", "cke_editable_inline"));
		caption.clear();
		caption.sendKeys(doc.getName());

		return new DocumentViewPage(driver);
	}
}
