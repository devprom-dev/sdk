package ru.devprom.pages.project.tasks;

import org.openqa.selenium.WebDriver;

import ru.devprom.items.Project;
import ru.devprom.pages.project.settings.StatePageBase;

public class TasksStatePage extends StatePageBase {

	public TasksStatePage(WebDriver driver) {
		super(driver);
	}

	public TasksStatePage(WebDriver driver, Project project) {
		super(driver, project);
	}


}
