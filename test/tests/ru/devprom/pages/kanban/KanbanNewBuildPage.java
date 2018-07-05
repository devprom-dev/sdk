/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package ru.devprom.pages.kanban;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import ru.devprom.items.Project;
import ru.devprom.pages.CKEditor;

/**
 *
 * @author лена
 */
public class KanbanNewBuildPage extends KanbanPageBase {

	@FindBy(xpath = ".//*[@id='pm_BuildCaption']")
	protected WebElement numberField;
        
        @FindBy(xpath = "")
	protected WebElement descriptionField;
        
        @FindBy(xpath = ".//*[@id='pm_BuildSubmitBtn']")
	protected WebElement saveBtn;
        
        @FindBy(xpath = ".//div[@id='sidebar']//a[@uid='kanbanboard']")
	protected WebElement boardItem;
        
        @FindBy(xpath = "//a[contains(text(),'Избранное')]")//"//a[contains(text(),'	Избранное')]")
	protected WebElement favoriteLink;
        
        @FindBy(xpath = ".//*[@id='BuildRevisionText']")//"//a[contains(text(),'	Избранное')]")
	protected WebElement commitNumberField;

    public KanbanNewBuildPage(WebDriver driver) {
        super(driver);
    }

    public void createNewBuild(String name, String description, String commitNumber) {
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(saveBtn));
        numberField.clear();
        numberField.sendKeys(name);
        if ( !commitNumber.equals("")) {
            commitNumberField.sendKeys(commitNumber);
            autocompleteSelect(commitNumber);
        }
        if ( !description.equals("")) {
            (new CKEditor(driver)).typeText(description);
        }
        submitDialog(saveBtn);
        FILELOG.debug("Created new build " + name);
    }
    
    public void createSimpleNewBuild(String name) {
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(saveBtn));
        numberField.clear();
        numberField.sendKeys(name);
        submitDialog(saveBtn);
        FILELOG.debug("Created new build " + name);
    }

    public void gotoKanbanBoardFromBuildPage() {
         (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(favoriteLink));
        favoriteLink.click();
        (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(boardItem));
		boardItem.click();
    }
    
}
