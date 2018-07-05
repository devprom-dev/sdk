package ru.devprom.pages.project.requests;

import org.openqa.selenium.WebDriver;

import ru.devprom.items.Project;
import ru.devprom.pages.project.settings.StatePageBase;

public class RequestsStatePage extends StatePageBase {

	
	public RequestsStatePage(WebDriver driver) {
		super(driver);
	}

	public RequestsStatePage(WebDriver driver, Project project) {
		super(driver, project);
	}

	
}
