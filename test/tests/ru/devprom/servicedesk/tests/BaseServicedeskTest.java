package ru.devprom.servicedesk.tests;

import org.testng.annotations.BeforeClass;
import ru.devprom.helpers.Configuration;
import ru.devprom.servicedesk.helpers.TestCredentialsProvider;
import ru.devprom.tests.TestBase;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
public class BaseServicedeskTest extends TestBase {

    @BeforeClass(dependsOnMethods = "runDriver")
    public void setup() {
        baseURL = Configuration.getServicedeskBaseUrl();
        username = TestCredentialsProvider.getInstance().getUserEmail();
        password = TestCredentialsProvider.getInstance().getUserPassword();
    }
}
