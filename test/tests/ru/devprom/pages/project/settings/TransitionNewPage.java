package ru.devprom.pages.project.settings;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requests.RequestsStatePage;

public class TransitionNewPage extends SDLCPojectPageBase {

	@FindBy(id = "pm_TransitionCaption")
	protected WebElement nameEdit;
	
	@FindBy(id = "pm_TransitionTargetState")
	protected WebElement targetStateSelect;
	
	@FindBy(id = "pm_TransitionDescription")
	protected WebElement descriptionEdit;
	
	@FindBy(id = "pm_TransitionIsReasonRequired")
	protected WebElement isReasonRequiredBox;
	
	@FindBy(id = "pm_TransitionSubmitBtn")
	protected WebElement submitBtn;
	
	public TransitionNewPage(WebDriver driver) {
		super(driver);
	}

	public TransitionNewPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	/**
	 * Метод не возвращает новую страницу, так как является универсальным для различных страниц.
	 * В тесте необходимо создавать объект нужной страницы дополнительно.
	 */
	public void createNewTransition(String name, String targetStateName){
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(nameEdit));
		nameEdit.clear();
		nameEdit.sendKeys(name);
		String value = targetStateSelect.findElement(By.xpath("//option[contains(.,'"+targetStateName+"')]")).getAttribute("value");
		(new Select(targetStateSelect)).selectByValue(value);
		submitDialog(submitBtn);
	}
	
	
}
