/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package ru.devprom.pages.scrum;

import org.openqa.selenium.By;
import org.openqa.selenium.Keys;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;
import ru.devprom.items.ScrumIssue;
import ru.devprom.pages.CKEditor;

/**
 *
 * @author лена
 */
public class ScrumIssueNewPage extends ScrumPageBase{
    @FindBy(id = "pm_ChangeRequestCaption")
	protected WebElement nameField;
    
    @FindBy(id = "pm_ChangeRequestPriority")
	protected WebElement prioritySelector;
    
    @FindBy(id = "FunctionText")
	protected WebElement epicSelector;
    
    @FindBy(id = "pm_ChangeRequestSubmitBtn")
	protected WebElement saveBtn;

    public ScrumIssueNewPage(WebDriver driver) {
        super(driver);
    }
    
    public void createIssue(ScrumIssue issue) {
    	waitForDialog();
		addName(issue.getName());
		if (!"".equals(issue.getDescription())) 
                    addDescription(issue.getDescription());
		if (!"".equals(issue.getPriority())) 
                    selectPiority(issue.getPriority());
		if (!"".equals(issue.getEpic())) 
                    setEpic(issue.getEpic());
                save();
    	FILELOG.debug("Created Issue: " + issue.getName());
	}

    private void addName(String name) {
        clickTab("main");
        nameField.sendKeys(name);
    }

    private void addDescription(String description) {
        clickTab("main");
        (new CKEditor(driver)).changeText(description);
    }

    private void selectPiority(String priority) {
        clickTab("main");
        new Select(prioritySelector).selectByVisibleText(priority);
    }
    
     private void setEpic(String epic) {
          clickTab("additional");
          epicSelector.sendKeys(epic);
          autocompleteSelect(epic);
    }
     
     private void addNewEpic(String epic) {
        clickTab("additional");
        epicSelector.sendKeys(epic);
        epicSelector.sendKeys(Keys.TAB);
    }

    private void save() {
        submitDialog(saveBtn);
    }

    public void createIssueWithNewEpic(ScrumIssue issue) {
    	waitForDialog();
		addName(issue.getName());
		if (!"".equals(issue.getDescription())) 
                    addDescription(issue.getDescription());
		if (!"".equals(issue.getPriority())) 
                    selectPiority(issue.getPriority());
		if (!"".equals(issue.getEpic())) 
                    addNewEpic(issue.getEpic());
                save();
    	FILELOG.debug("Created Issue: " + issue.getName());
    }
}
