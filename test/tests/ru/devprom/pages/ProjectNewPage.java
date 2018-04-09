package ru.devprom.pages;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;

import ru.devprom.items.Project;
import ru.devprom.items.Template;
import ru.devprom.pages.kanban.KanbanPageBase;
import ru.devprom.pages.project.IProjectBase;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.requirement.RequirementBasePage;
import ru.devprom.pages.scrum.ScrumPageBase;
import ru.devprom.pages.support.SupportPageBase;

public class ProjectNewPage extends PageBase {

	@FindBy(id = "Caption")
	private WebElement nameEdit;

	@FindBy(id = "CodeName")
	private WebElement codeNameEdit;

	@FindBy(id = "Template")
	private WebElement templateSelect;
        
	@FindBy(id = "btn")
	private WebElement createBtn;

	public ProjectNewPage(WebDriver driver) {
		super(driver);
		Assert.assertTrue(driver.getPageSource().contains("Создание нового"));
		FILELOG.debug("Open Create New Project page");
		FILELOG.debug("Current URL is: " + driver.getCurrentUrl());
	}

	public IProjectBase createNew(Project project) {
		typeProjectName(project.getName());
		typeCodeName(project.getCodeName());
		//templateSelect.sendKeys(project.getTemplate().getName()); 
		if (project.getTemplate().getValue()>0)
        (new Select(templateSelect)).selectByValue(String.valueOf(project.getTemplate().getValue()));
		else 
	    (new Select(templateSelect)).selectByVisibleText(String.valueOf(project.getTemplate().getFullName()));

		if(project.getDemoData()) {
			driver.findElement(By.cssSelector("input[name='DemoData'][value='Y']")).click();			
		}
		createBtn.click();
		// (new
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By.id("menu_favs")));
		switch (project.getTemplate().getName()) {
			case "Kanban": return new KanbanPageBase(driver, project);
			case "Scrum": return new ScrumPageBase(driver, project);
			case "Waterfall":
				return new SDLCPojectPageBase(driver, project);
			case "Поддержка":
				return new SupportPageBase(driver, project);
                        case "Требования":
				return new RequirementBasePage(driver, project);
			default:
				throw new RuntimeException("The class for project type "
						+ project.getTemplate().getName() + " is not implemented");
		}
	}

	
	public SDLCPojectPageBase createNewSDLCFromUserTemplate(Project project) {
		typeProjectName(project.getName());
		typeCodeName(project.getCodeName());
	    (new Select(templateSelect)).selectByVisibleText(String.valueOf(project.getTemplate().getFullName()));
		createBtn.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By.id("menu_favs")));
		return new SDLCPojectPageBase(driver, project);
	}
	
	public void createWithError(Project project) {
		typeProjectName(project.getName());
		typeCodeName(project.getCodeName());
		selectTemplate(project.getTemplate());
		createBtn.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By.className("alert-error")));
	}

    public void typeProjectName(String name) {
    	nameEdit.clear();
        nameEdit.sendKeys(name);
    }

    public void typeCodeName(String codeName) {
    	codeNameEdit.clear();
        codeNameEdit.sendKeys(codeName);
    }

    public void selectTemplate(Template template) {
        if (template.getValue()>0)
        (new Select(templateSelect)).selectByValue(String.valueOf(template.getValue()));
		else 
	    (new Select(templateSelect)).selectByVisibleText(String.valueOf(template.getFullName()));
        }

    public IProjectBase clickCreateProject(Project project) {
        createBtn.click();
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.id("menu_favs")));
        switch (project.getTemplate().getName()) {
			case "Kanban": return new KanbanPageBase(driver, project);
			case "Scrum": return new ScrumPageBase(driver, project);
			case "Waterfall":
				return new SDLCPojectPageBase(driver, project);
			case "Поддержка":
				return new SupportPageBase(driver, project);
			default:
				throw new RuntimeException("The class for project type "
						+ project.getTemplate().getName() + " is not implemented");
    }
    }
}
