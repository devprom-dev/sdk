/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package ru.devprom.pages.scrum;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import ru.devprom.pages.project.IterationNewPage;

/**
 *
 * @author лена
 */
public class HistoryBoardPage extends ScrumPageBase{

    public HistoryBoardPage(WebDriver driver) {
        super(driver);
    }

    public IterationNewPage versionChange(String sprint) {
        WebElement sprintTitle = driver.findElement(By.xpath("//td[@class='board-group']//span[contains(text(),'"+sprint+"')]"));
        clickOnInvisibleElement(sprintTitle.findElement(By.xpath("./ancestor::td//a[@id='row-modify']")));
        return new IterationNewPage(driver);
    }

    public void moveToAnotherSprint(String requestNumericId, int releaseNumber, String columnName)
    {
        int itemCount = driver.findElements(By.xpath("//table[contains(@id,'requestboard')]/tbody/tr[contains(@class,'board-columns')]/th")).size(); 
        int column = 1;
        for(int i = 1; i<=itemCount; i++)
        {
            if(driver.findElement(By.xpath("//table[contains(@id,'requestboard')]/tbody/tr[contains(@class,'board-columns')]/th["+i+"]")).getText().contains(columnName))	
               column = i;
        }
      	 WebElement element = driver.findElement(By.xpath("//a[contains(text(),'"+requestNumericId+"')]/ancestor::div[@class='board_item_body']"));
         int rowNum = releaseNumber+1;
       WebElement onElement = driver.findElement(By.xpath("//table[contains(@id,'requestboard')]//tr[contains(@class,'row-cards')]["+rowNum+"]//td["+column+"]"));
       scrollToElement(onElement);
       mouseMove(element);
       new Actions(driver).dragAndDrop(element, onElement).build().perform();
       mouseMove(onElement);
       try {
		Thread.sleep(500);
		} catch (InterruptedException e) {
		}
    
    }

    public ScrumIssueViewPage openUserStory(String id) {
        driver.findElement(By.xpath("//table[contains(@id,'requestboard')]//a[contains(text(),'[" +id+ "]')]")).click();
        return new ScrumIssueViewPage(driver);
    }
    
}
