/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package ru.devprom.pages.requirement;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import ru.devprom.pages.kanban.KanbanTaskNewPage;
import ru.devprom.pages.kanban.KanbanTaskViewPage;

/**
 *
 * @author лена
 */
public class InboxRequestsPage extends RequirementBasePage{
    
    @FindBy(xpath = "//*[@id='new-issue']")
	protected WebElement addWishBtn;

    public InboxRequestsPage(WebDriver driver) {
        super(driver);
    }

    public KanbanTaskNewPage addWish() {
        clickOnInvisibleElement(addWishBtn);
        return new KanbanTaskNewPage(driver);
    }

    public String getIDWishByName(String name) {
         String ids;
        ids = driver.findElement(By.xpath("//tr[contains(@id,'issueslist1_row_')]/td[@id='caption' and contains(.,'"+
                name+"')]/preceding-sibling::td[@id='uid']")).getText();
        String id = ids.substring(1, ids.length()-1);
        FILELOG.debug("Get UID of wish" + id);
        return id; 
    }

    public IssueViewPage clickOnWish(String id) {
        driver.findElement(By.xpath("//*[@id='uid']/a[contains(.,'"+id+"')]")).click();
        return new IssueViewPage(driver);
    }
    
    
}
