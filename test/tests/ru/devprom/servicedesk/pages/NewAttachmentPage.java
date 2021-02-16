package ru.devprom.servicedesk.pages;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
public class NewAttachmentPage extends ServicedeskPage {

    public NewAttachmentPage(WebDriver driver) {
        super(driver);
    }

    public NewAttachmentPage selectFileForUpload(String filePath) {
        driver.findElement(By.id("attachment_form_file")).sendKeys(filePath);
        return this;
    }

    public ViewIssuePage submit() {
        driver.findElement(By.xpath("//button[@type='submit']")).click();
        return new ViewIssuePage(driver);
    }
}
