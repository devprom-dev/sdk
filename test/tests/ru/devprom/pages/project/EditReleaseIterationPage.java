package ru.devprom.pages.project;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;

public class EditReleaseIterationPage extends ReleaseNewPage {


	@FindBy(xpath="//input[@class='btn' and @value='Удалить']")
	protected WebElement deleteBtn;
	
	public EditReleaseIterationPage(WebDriver driver) {
		super(driver);
	}

	public EditReleaseIterationPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public ReleasesIterationsPage delete(){
		deleteBtn.click();
		safeAlertAccept();
		return new ReleasesIterationsPage(driver);
	}
	
}
