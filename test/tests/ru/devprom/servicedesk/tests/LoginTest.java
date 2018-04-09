package ru.devprom.servicedesk.tests;

import org.testng.annotations.BeforeClass;
import org.testng.annotations.BeforeTest;
import org.testng.annotations.Test;
import ru.devprom.helpers.Configuration;
import ru.devprom.servicedesk.pages.IssuesListPage;
import ru.devprom.servicedesk.pages.LoginPage;
import ru.devprom.tests.TestBase;

import static org.testng.Assert.*;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
public class LoginTest extends BaseServicedeskTest {

    @Test
    public void shouldLoginAndLogoutSuccessfullyWithValidCredentials() {
        driver.get(baseURL + "login");
        IssuesListPage issuesListPage = new LoginPage(driver).login(username, password);

        assertTrue(issuesListPage.containsLogoutLink(), "Page should contain logout link");

        LoginPage loginPage = issuesListPage.logout();
        assertFalse(loginPage.containsLogoutLink(), "Page shouldn't contain logout link");
    }

    @Test
    public void shouldNotLoginWithInvalidCredentials() {
        driver.get(baseURL + "login");
        LoginPage loginPage = new LoginPage(driver).loginExpectingError("nonexistinguser", "123");

        assertTrue(loginPage.containsErrorAlert(), "Page should contain error message");
    }

}
