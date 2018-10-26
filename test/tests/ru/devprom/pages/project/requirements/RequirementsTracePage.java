/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package ru.devprom.pages.project.requirements;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.testscenarios.TestScenarioNewPage;

/**
 *
 * @author лена
 */
public class RequirementsTracePage extends SDLCPojectPageBase{
    
    
    public RequirementsTracePage(WebDriver driver) {
		super(driver);
	}

	public RequirementsTracePage(WebDriver driver, Project project) {
		super(driver, project);
	}
        
    public String getIdByName(String requirementName) {
        String ids = 
    		driver.findElement(By.xpath("//tr[contains(@id,'requirementtracelist1_row')]/*[@id='caption']/*[contains(.,'"+
				requirementName+"')]/../preceding-sibling::td[@id='uid']/a")).getAttribute("href");
        String id = ids.substring(ids.lastIndexOf("/") + 1);
        FILELOG.debug("Click to UID of requirement");
        return id;
    }

    public TestScenarioNewPage gotoCreateScenario(String idRequirement) {
        WebElement requirement = driver.findElement(By.xpath(".//tr[@object-id='"+idRequirement+"']"));
        WebElement invisElement = driver.findElement(By.xpath(".//*[@object-id='"+idRequirement
                +"']//*[@id='operations']//a[contains(@class,'dropdown-toggle')]"));
        clickOnInvisibleElement(invisElement);
        clickOnInvisibleElement(
    		driver.findElement(By.xpath(".//*[@object-id='"+idRequirement+"']//*[@id='operations']//a[contains(.,'Создать')]"))
   		);
        clickOnInvisibleElement(
        	driver.findElement(By.xpath(".//*[@object-id='"+idRequirement+"']//*[@id='operations']//a[contains(.,'Тестовый сценарий')]"))
        );
        waitForDialog();
        return new TestScenarioNewPage(driver);
    }
}
    

