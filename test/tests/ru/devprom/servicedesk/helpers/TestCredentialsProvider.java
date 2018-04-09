package ru.devprom.servicedesk.helpers;

import ru.devprom.helpers.Configuration;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
public class TestCredentialsProvider {

    private static TestCredentialsProvider instance;

    private String userEmail;

    private String userPassword;

    public static TestCredentialsProvider getInstance() {
        if (instance == null) {
            instance = new TestCredentialsProvider();
        }

        return instance;
    }

    public TestCredentialsProvider() {
        userEmail = Configuration.getServicedeskUsername();
        userPassword = Configuration.getServicedeskPassword();
    }

    public String getUserEmail() {
        return userEmail;
    }

    public String getUserPassword() {
        return userPassword;
    }

    public void setUserEmail(String userEmail) {
        this.userEmail = userEmail;
    }

    public void setUserPassword(String userPassword) {
        this.userPassword = userPassword;
    }
}
