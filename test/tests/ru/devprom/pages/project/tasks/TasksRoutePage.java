package ru.devprom.pages.project.tasks;

import org.openqa.selenium.ElementNotVisibleException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class TasksRoutePage extends SDLCPojectPageBase {

	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(text(),'Добавить')]")
	protected WebElement addBtn;

	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(text(),'Действия')]")
	protected WebElement actionsBtn;

	@FindBy(xpath = "//a[contains(@href,'/pm/devprom_webtest/tasks/board?class=metaobject&entity=pm_Task') and text()='Задача']")
	protected WebElement newTaskBtn;

	public TasksRoutePage(WebDriver driver) {
		super(driver);
	}

	public TasksRoutePage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public TaskNewPage clickNewTask() {
		addBtn.click();
		try {
			newTaskBtn.click();
		} catch (ElementNotVisibleException e) {
			clickOnInvisibleElement(newTaskBtn);
		}
		return new TaskNewPage(driver);
	}

}
