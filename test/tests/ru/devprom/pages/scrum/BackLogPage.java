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
import ru.devprom.pages.project.functions.FunctionNewPage;

/**
 *
 * @author лена
 */
public class BackLogPage extends ScrumPageBase{
    
    @FindBy(xpath = "//*[@id='new-issue']")
	protected WebElement addUserStoryBtn;
    
    public BackLogPage(WebDriver driver) {
        super(driver);
    }

    public ScrumIssueNewPage addUserStory() {
        clickOnInvisibleElement(addUserStoryBtn);
        waitForDialog();
        return new ScrumIssueNewPage(driver);
    }

    public String getIDByName(String name) {
        String ids = driver.findElement(By.xpath("//tr[contains(@id,'requestlist1_row_')]/td[@id='caption' and contains(.,'"+
                name+"')]/preceding-sibling::td[@id='uid']/a")).getAttribute("href");
        ids = ids.substring(ids.lastIndexOf("/")+1);
        FILELOG.debug("ID = " + ids);
        return ids; 
    }

    public FunctionNewPage makeEpic(String id) {
        String clearID = id.substring(2);
        WebElement row = driver.findElement(By.xpath("//tr[@object-id='"+clearID+"']"));
        clickOnInvisibleElement(row.findElement(By.xpath(".//*[@id='operations']/div/a")));
        row.findElement(By.xpath(".//a[contains(.,'Преобразовать в эпик')]")).click();
        return new FunctionNewPage(driver);
    }
}
