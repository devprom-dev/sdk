package ru.devprom.servicedesk.pages;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
public class IssuesListPage extends ServicedeskPage {

    public IssuesListPage(WebDriver driver) {
        super(driver);
    }

    public boolean containsListOfIssues() {
        return driver.findElements(By.id("issues-list")).size() != 0;
    }

    public NewIssuePage clickNewIssueButton() {
        driver.findElement(By.id("create-issue-button")).click();
        return new NewIssuePage(driver);
    }

    public boolean containsIssueWithTitle(String issueTitle) {
        return driver.findElements(By.xpath(String.format("//td[contains(.,'%s')]", issueTitle))).size() == 1;
    }

    public ViewIssuePage openFirstIssueInList() {
        driver.findElement(By.xpath("//td[@class='issue-id-cell' and position()=1]/a")).click();
        return new ViewIssuePage(driver);
    }
}
