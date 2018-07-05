/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package ru.devprom.pages.project.requirements;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.project.SDLCPojectPageBase;

/**
 *
 * @author лена
 */
public class RequirementReturnToWorkPage  extends SDLCPojectPageBase{

    @FindBy(xpath = ".//*[@id='WikiPageSubmitBtn']")
	protected WebElement saveBtn;
    
    public RequirementReturnToWorkPage(WebDriver driver) {
        super(driver);
    }

    public void addComment(String comment) {
        (new CKEditor(driver)).typeText(comment);
        submitDialog(saveBtn);
    }
    
}
