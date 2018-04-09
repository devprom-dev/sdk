package ru.devprom.servicedesk.pages;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
public class ViewIssuePage extends ServicedeskPage {

    public ViewIssuePage(WebDriver driver) {
        super(driver);
    }


    public String getIssueTitle() {
        return driver.findElement(By.id("issue-title")).getText();
    }

    public String getIssueDescription() {
        return driver.findElement(By.id("issue-description")).getText();
    }

    public String getIssuePriority() {
        return driver.findElement(By.id("issue-priority")).getText();
    }

    public String getIssueProduct() {
        return driver.findElement(By.id("issue-product")).getText();
    }

    public EditIssuePage clickEdit() {
        driver.findElement(By.id("edit-issue-button")).click();
        return new EditIssuePage(driver);
    }

    public boolean containsAttachmentWithName(String filename) {
        return driver.findElements(By.linkText(filename)).size() == 1;
    }

    public NewAttachmentPage clickAttachFileButton() {
        driver.findElement(By.id("add-attachment-button")).click();
        return new NewAttachmentPage(driver);
    }

    public String getFirstAttachmentName() {
        return driver.findElement(By.xpath("//a[contains(@class,'attachment-name') and position()=1]")).getText();
    }

    public ViewIssuePage deleteAttachmentWithName(String attachmentName) {
        String xpath = String.format("//a[contains(@class,'attachment-name') and contains(.,'%s')]/following-sibling::a", attachmentName);
        driver.findElement(By.xpath(xpath)).click();
        return this;
    }
}
