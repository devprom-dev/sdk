package ru.devprom.pages.project.documents;

import org.openqa.selenium.WebDriver;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class DocumentViewPage extends SDLCPojectPageBase {

	public DocumentViewPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public DocumentViewPage(WebDriver driver) {
		super(driver);
	}

}
