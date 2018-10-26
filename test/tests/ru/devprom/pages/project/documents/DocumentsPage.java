package ru.devprom.pages.project.documents;

import org.openqa.selenium.ElementNotVisibleException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class DocumentsPage extends SDLCPojectPageBase {

	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(.,'Действия')]")
	protected WebElement actionsBtn;

	@FindBy(xpath = "//a[contains(.,'Создать документ')]")
	protected WebElement newDocBtn;

	public DocumentsPage(WebDriver driver) {
		super(driver);
	}

	public DocumentsPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public DocumentNewPage clickNewDoc() {
		actionsBtn.click();
		try {
			newDocBtn.click();
		} catch (ElementNotVisibleException e) {
			clickOnInvisibleElement(newDocBtn);
		}
		return new DocumentNewPage(driver);
	}

}
