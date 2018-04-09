package ru.devprom.pages.project.functions;

import org.openqa.selenium.WebDriver;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class FunctionViewPage extends SDLCPojectPageBase {

	public FunctionViewPage(WebDriver driver) {
		super(driver);
	}

	public FunctionViewPage(WebDriver driver, Project project) {
		super(driver, project);
	}

}
