/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package ru.devprom.pages.kanban;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

/**
 *
 * @author лена
 */
public class KanbanEnvirenmentsPage extends KanbanPageBase{
    
    @FindBy(xpath = ".//*[@id='new-environment']")
	protected WebElement addEnvirenment;

    public KanbanEnvirenmentsPage(WebDriver driver) {
        super(driver);
    }

    public KanbanEnvirenmentNewPage clickAddEnvironment() {
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(addEnvirenment));
        addEnvirenment.click();
        return new KanbanEnvirenmentNewPage(driver);
    }
    
    
    
}
