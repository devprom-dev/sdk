/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package ru.devprom.pages.project.requirements;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import ru.devprom.pages.project.SDLCPojectPageBase;

/**
 *
 * @author лена
 */
public class RequirementsDocsPage extends SDLCPojectPageBase {
    //кнопка +Документ
    @FindBy(xpath = ".//*[@id='0']")
	protected WebElement addDocBtn;
    
    public RequirementsDocsPage(WebDriver driver) {
		super(driver);
	}

    public RequirementViewPage addDoc() {
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(addDocBtn));
        addDocBtn.click();
        try {
			Thread.sleep(5000);
		} catch (InterruptedException e) {
		}
        return new RequirementViewPage(driver);
    }
    
}
