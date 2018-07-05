/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package ru.devprom.pages.scrum;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;
import ru.devprom.items.ScrumTask;

/**
 *
 * @author лена
 */
public class ScrumTaskNewPage extends ScrumPageBase{
    
    @FindBy(id = "pm_TaskCaption")
	protected WebElement nameFld;
    
    @FindBy(id = "pm_TaskSubmitBtn")
	protected WebElement saveBtn;
    
    @FindBy(id = "pm_TaskTaskType")
	protected WebElement typeSelector;
    
    @FindBy(id = "pm_TaskPriority")
	protected WebElement prioritySelector;
    
    @FindBy(id = "pm_TaskAssignee")
	protected WebElement assigneeSelector;

    public ScrumTaskNewPage(WebDriver driver) {
        super(driver);
    }
    
    public void createTask(ScrumTask task)
    {
    	nameFld.clear();
        nameFld.sendKeys(task.getName());
		new Select(typeSelector).selectByVisibleText(task.getType());
		if (!"".equals(task.getExecutor())) 
	            new Select(assigneeSelector).selectByVisibleText(task.getExecutor());
		if (!"".equals(task.getPriority())) 
	            new Select(prioritySelector).selectByVisibleText(task.getPriority());
		submitDialog(saveBtn);
	}
}
