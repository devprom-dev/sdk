package ru.devprom.servicedesk.pages;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
public class RegistrationPage extends ServicedeskPage {

    public RegistrationPage(WebDriver driver) {
        super(driver);
    }

    public IssuesListPage registerUserWithCredentials(String email, String name, String password) {
        enterEmail(email);
        enterName(name);
        enterPassword(password);
        enterPasswordConfirm(password);
        return clickSubmit();
    }

    public RegistrationPage enterEmail(String email) {
        driver.findElement(By.id("fos_user_registration_form_email")).sendKeys(email);
        return this;
    }

    public RegistrationPage enterName(String name) {
        driver.findElement(By.id("fos_user_registration_form_username")).sendKeys(name);
        return this;
    }

    public RegistrationPage enterPassword(String password) {
        driver.findElement(By.id("fos_user_registration_form_plainPassword_first")).sendKeys(password);
        return this;
    }

    public RegistrationPage enterPasswordConfirm(String password) {
        driver.findElement(By.id("fos_user_registration_form_plainPassword_second")).sendKeys(password);
        return this;
    }

    public IssuesListPage clickSubmit() {
        driver.findElement(By.xpath("//*[@type='submit']")).click();
        return new IssuesListPage(driver);
    }
}
