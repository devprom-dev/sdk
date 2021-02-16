package ru.devprom.pages.project.logs;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import ru.devprom.items.Project;
import ru.devprom.pages.kanban.KanbanPageBase;

public class LogPage extends KanbanPageBase {

    // кнопка Отменить (изменения)
    @FindBy(xpath = "//*[@class='btn btn-info btn-xs dropdown-toggle actions-button']")
    protected WebElement cancelBtn;

    public LogPage(WebDriver driver) { super(driver); }
    public LogPage(WebDriver driver, Project project) {
        super(driver, project);
    }

    public LogPage changeCancel(){
        cancelBtn.click();
        return this;
    }

}
