package ru.devprom.pages.project;

import org.openqa.selenium.WebDriver;

import ru.devprom.items.Project;

public class RequirementDocumentsPage extends SDLCPojectPageBase {

	public RequirementDocumentsPage(WebDriver driver) {
		super(driver);
	}

	public RequirementDocumentsPage(WebDriver driver, Project project) {
		super(driver, project);
	}

}
