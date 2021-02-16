package ru.devprom.pages.project.testsreports;

import org.openqa.selenium.WebDriver;
import ru.devprom.pages.kanban.KanbanPageBase;

public class TestReportViewPage extends KanbanPageBase {
    public TestReportViewPage (WebDriver driver){super (driver);}

    public TestReportViewPage getName(){
        return this;
    }
}
