package ru.devprom.servicedesk.pages;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.support.PageFactory;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
public class ServicedeskPage {

    protected WebDriver driver;

    public ServicedeskPage(WebDriver driver) {
        PageFactory.initElements(driver, this);
        this.driver = driver;
    }

    public LoginPage logout() {
        driver.findElement(By.id("logout-link")).click();
        return new LoginPage(driver);
    }

    public boolean containsLogoutLink() {
        return driver.findElements(By.id("logout-link")).size() > 0;
    }

    public IssuesListPage goToIssuesList() {
        driver.findElement(By.id("home-link")).click();
        return new IssuesListPage(driver);
    }
}
