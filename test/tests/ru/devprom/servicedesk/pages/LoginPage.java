package ru.devprom.servicedesk.pages;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
public class LoginPage extends ServicedeskPage {

    @FindBy(id = "username")
    private WebElement loginField;

    @FindBy(id = "password")
    private WebElement passwordField;

    private WebDriver driver;

    public LoginPage(WebDriver driver) {
        super(driver);
        this.driver = driver;
    }

    public IssuesListPage login(String username, String password) {
        doLogin(username, password);
        return new IssuesListPage(driver);
    }

    public LoginPage loginExpectingError(String username, String password) {
        doLogin(username, password);
        return new LoginPage(driver);
    }

    public boolean containsErrorAlert() {
        return driver.findElements(By.className("alert-danger")).size() == 1;
    }

    public boolean containsLoginForm() {
        return driver.findElements(By.id("login-form")).size() == 1;
    }

    public RegistrationPage clickRegisterButton() {
        driver.findElement(By.id("_register")).click();
        return new RegistrationPage(driver);
    }


    private void doLogin(String username, String password) {
        loginField.clear();
        loginField.sendKeys(username);
        passwordField.clear();
        passwordField.sendKeys(password);
        passwordField.submit();
    }

}
