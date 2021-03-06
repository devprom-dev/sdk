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
import ru.devprom.pages.CKEditor;

/**
 *
 * @author лена
 */
public class KanbanEnvirenmentNewPage extends KanbanPageBase{
    
    @FindBy(xpath = ".//*[@id='pm_EnvironmentCaption']")
	protected WebElement nameField;
    
    @FindBy(xpath = ".//*[@id='pm_EnvironmentServerAddress']")
	protected WebElement addresField;
    
    @FindBy(xpath = ".//*[@id='pm_EnvironmentSubmitBtn']")
	protected WebElement saveBtn;

    public KanbanEnvirenmentNewPage(WebDriver driver) {
        super(driver);
    }

    public void createEnvironment(String name, String address, String description)
    {
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(nameField));
        nameField.clear();
        nameField.sendKeys(name);
        addresField.clear();
        addresField.sendKeys(address);
        (new CKEditor(driver)).changeText(description);
        submitDialog(saveBtn);
    }
}
