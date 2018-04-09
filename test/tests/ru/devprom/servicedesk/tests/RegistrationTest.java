package ru.devprom.servicedesk.tests;

import org.testng.annotations.BeforeClass;
import org.testng.annotations.BeforeSuite;
import org.testng.annotations.Test;
import ru.devprom.helpers.Configuration;
import ru.devprom.helpers.DataProviders;
import ru.devprom.servicedesk.helpers.TestCredentialsProvider;
import ru.devprom.servicedesk.pages.IssuesListPage;
import ru.devprom.servicedesk.pages.LoginPage;
import ru.devprom.servicedesk.pages.RegistrationPage;
import ru.devprom.servicedesk.pages.ServicedeskPage;
import ru.devprom.tests.TestBase;

import static org.testng.Assert.assertFalse;
import static org.testng.Assert.assertTrue;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
public class RegistrationTest extends BaseServicedeskTest {

    @Test (alwaysRun = true)
    public void newUserRegistration() {
        driver.get(baseURL + "login");
        RegistrationPage registrationPage = new LoginPage(driver).clickRegisterButton();

        String name = "User" + DataProviders.getUniqueString();
        String email = name + "@feature.devprom";
        String password = "pass123";

        registrationPage.enterEmail(email);
        registrationPage.enterName(name);
        registrationPage.enterPassword(password);
        registrationPage.enterPasswordConfirm(password);

        IssuesListPage issuesListPage = registrationPage.clickSubmit();
        TestCredentialsProvider.getInstance().setUserEmail(email);
        TestCredentialsProvider.getInstance().setUserPassword(password);
    }


}
