package ru.devprom.servicedesk.pages;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
public class NewIssuePage extends EditIssuePage {

    public NewIssuePage(WebDriver driver) {
        super(driver);
    }

    public NewIssuePage selectFileForUpload(String filePath) {
        driver.findElement(By.id("issue_form_newAttachment_file")).sendKeys(filePath);
        return this;
    }
}
