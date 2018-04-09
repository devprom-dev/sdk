/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package ru.devprom.pages.kanban;

import java.io.File;
import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

/**
 *
 * @author лена
 */
public class KanbanTestsPage extends KanbanPageBase{
    
    @FindBy(xpath = "//a[@id='import']")
	protected WebElement importReportsBtn;
    
    @FindBy(xpath = ".//*[contains(text(),'Действия')]")
	protected WebElement actionBtn;
    
    @FindBy(xpath = ".//*[@id='pm_TestSubmitBtn']")
	protected WebElement submitBtn;

    public KanbanTestsPage(WebDriver driver) {
        super(driver);
    }

    public void importReport(File file) {
        String codeIE = "$.browser.msie = true; document.documentMode = 8;";
		((JavascriptExecutor) driver).executeScript(codeIE);
        clickOnInvisibleElement(actionBtn);
        clickOnInvisibleElement(importReportsBtn);
        waitForDialog();
        driver.findElement(By.xpath(".//*[@id='pm_TestReportFile']")).sendKeys(file.getAbsolutePath());
        try {
			Thread.sleep(3000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
        submitDialog(submitBtn);
    }
    
}
