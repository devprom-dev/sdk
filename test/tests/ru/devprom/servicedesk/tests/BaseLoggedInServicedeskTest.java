package ru.devprom.servicedesk.tests;

import org.testng.annotations.AfterClass;
import org.testng.annotations.BeforeClass;
import org.testng.annotations.BeforeTest;
import ru.devprom.helpers.Configuration;
import ru.devprom.servicedesk.pages.ServicedeskPage;
import ru.devprom.servicedesk.pages.LoginPage;
import ru.devprom.tests.TestBase;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
public class BaseLoggedInServicedeskTest extends BaseServicedeskTest {

    @BeforeClass(dependsOnMethods = "setup")
    public void doLogin() throws InterruptedException {
        driver.get(baseURL + "login");
        new LoginPage(driver).login(username, password);
    }

    @AfterClass
    public void doLogout() throws InterruptedException {
        new ServicedeskPage(driver).logout();
    }

}
