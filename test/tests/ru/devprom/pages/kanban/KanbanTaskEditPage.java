package ru.devprom.pages.kanban;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;

public class KanbanTaskEditPage extends KanbanTaskNewPage {
        
	public KanbanTaskEditPage(WebDriver driver) {
		super(driver);
	}

	public KanbanTaskEditPage(WebDriver driver, Project project) {
		super(driver, project);
	}
}
