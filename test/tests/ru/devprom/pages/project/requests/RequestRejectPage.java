package ru.devprom.pages.project.requests;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class RequestRejectPage extends SDLCPojectPageBase {

	@FindBy(id = "SubmittedVersionText")
	protected WebElement versionSubmitted;

	@FindBy(id = "ClosedInVersionText")
	protected WebElement versionClosed;

	@FindBy(id = "pm_ChangeRequestSubmitBtn")
	protected WebElement submitBtn;

	public RequestRejectPage(WebDriver driver) {
		super(driver);
	}

	public RequestRejectPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public RequestViewPage reject(String comment) {
		try {
			Thread.sleep(500);
		} catch (InterruptedException e) {
		}
		(new CKEditor(driver)).typeText(comment);
		submitDialog(submitBtn);
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By.xpath("//span[@id='state-label' and contains(.,'Добавлено')]")));
		return new RequestViewPage(driver);
	}

}
