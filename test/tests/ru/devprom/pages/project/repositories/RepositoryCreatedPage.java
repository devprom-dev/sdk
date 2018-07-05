package ru.devprom.pages.project.repositories;

import org.openqa.selenium.WebDriver;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class RepositoryCreatedPage extends SDLCPojectPageBase {

	public RepositoryCreatedPage(WebDriver driver) {
		super(driver);
	}

	public RepositoryCreatedPage(WebDriver driver, Project project) {
		super(driver, project);
		// TODO Auto-generated constructor stub
	}

}
