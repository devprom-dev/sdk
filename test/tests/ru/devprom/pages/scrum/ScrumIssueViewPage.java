/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package ru.devprom.pages.scrum;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.pages.CKEditor;
import ru.devprom.pages.project.functions.FunctionNewPage;
import ru.devprom.pages.project.requests.RequestViewPage;

/**
 *
 * @author лена
 */
public class ScrumIssueViewPage extends RequestViewPage
{
	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(text(),'Действия')]")
	protected WebElement actionsBtn;

	@FindBy(xpath = "//ul//a[text()='Преобразовать в эпик']")
	protected WebElement convertToEpic;

    public ScrumIssueViewPage(WebDriver driver) {
        super(driver);
    }

    public void editDescription(String text) {
        driver.findElement(By.xpath("//*[contains(@id,'pm_ChangeRequestDescription')]/p")).click();
        WebElement textField = driver.findElement(By.xpath("//*[contains(@id,'pm_ChangeRequestDescription')]/p")); 
                ((JavascriptExecutor) driver).executeScript("arguments[0].innerHTML = '"+text+"';", textField);
    }
    
    public FunctionNewPage convertToEpic() {
		actionsBtn.click();
		(new WebDriverWait(driver, 5)).until(ExpectedConditions.visibilityOf(convertToEpic));
		convertToEpic.click();
		return new FunctionNewPage(driver);
    }
}
