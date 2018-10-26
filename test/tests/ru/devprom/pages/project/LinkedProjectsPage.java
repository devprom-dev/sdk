package ru.devprom.pages.project;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;

public class LinkedProjectsPage extends SDLCPojectPageBase {

	
	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(.,'Действия')]")
	protected WebElement actionsBtn;
	
	@FindBy(xpath = "//a[contains(.,'Включить в программу') and @href]")
	protected WebElement includeToProgramBtn;
	
	@FindBy(xpath = "//a[contains(.,'Добавить подпроект') and @href]")
	protected WebElement addSubprojectBtn;
	
	
	public LinkedProjectsPage(WebDriver driver) {
		super(driver);
	}

	public LinkedProjectsPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public LinkProjectsPage includeToProgram(){
		clickOnInvisibleElement(includeToProgramBtn);
		return new LinkProjectsPage(driver);
	}
	

	public LinkProjectsPage addSubproject(){
		clickOnInvisibleElement(addSubprojectBtn);
		return new LinkProjectsPage(driver);
	}
	
	
	
}
