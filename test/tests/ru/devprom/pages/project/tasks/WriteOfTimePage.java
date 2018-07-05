/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package ru.devprom.pages.project.tasks;

import org.openqa.selenium.By;
import org.openqa.selenium.Keys;
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
public class WriteOfTimePage extends SDLCPojectPageBase{
    
    @FindBy(id = "pm_ActivityCapacity")
    WebElement timeFld;
    
    @FindBy(id = "pm_ActivityDescription")
    WebElement descriptionFld;
    
    @FindBy(id = "pm_ActivitySubmitBtn")
    WebElement saveBtn;

    public WriteOfTimePage(WebDriver driver) {
        super(driver);
    }
    
    public void writeOfTime(String time, String description){
          timeFld.sendKeys(time);
          descriptionFld.sendKeys(description);
          submitDialog(saveBtn);
    }
}
