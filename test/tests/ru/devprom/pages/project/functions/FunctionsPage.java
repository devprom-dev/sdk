package ru.devprom.pages.project.functions;

import org.openqa.selenium.ElementNotVisibleException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class FunctionsPage extends SDLCPojectPageBase {

	@FindBy(xpath = "//a[@id='new-feature']")
	protected WebElement newFunctionBtn;

	public FunctionsPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public FunctionsPage(WebDriver driver) {
		super(driver);
	}

	public FunctionNewPage clickNewFunction() {
		try {
			newFunctionBtn.click();
		} catch (ElementNotVisibleException e) {
			clickOnInvisibleElement(newFunctionBtn);
		}

		return new FunctionNewPage(driver);

	}

}
