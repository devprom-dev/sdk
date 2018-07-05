package ru.devprom.tests;

import org.openqa.selenium.By;
import org.openqa.selenium.TimeoutException;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;
import org.testng.annotations.BeforeClass;
import org.testng.annotations.Test;

import ru.devprom.helpers.Configuration;
import ru.devprom.items.User;
import ru.devprom.pages.FavoritesPage;
import ru.devprom.pages.LoginPage;
import ru.devprom.pages.admin.AdminPageBase;
import ru.devprom.pages.admin.ImportFromLDAPPage;
import ru.devprom.pages.admin.UserEditPage;
import ru.devprom.pages.admin.UsersListPage;

public class LDAPTest extends TestBase {
	private String usernameLDAP = "admin";
	private String userNameLongLDAP = "system administrator";
	private String baseOU = "ou=system";
	
	@Test
	public void importFromLDAP() throws InterruptedException
	{
		AdminPageBase page = new AdminPageBase(driver);
		//Open Import Users From LDAP Page		
		ImportFromLDAPPage iflp = page.gotoImportFromLDAP();
		//Setup LDAP server configuration
		iflp.setupLDAPConfiguration(Configuration.getLDAPserver(), "apacheds", Configuration.getLDAPUser(), Configuration.getLDAPPass(), baseOU);
		//Setup attributes for login and mail
		iflp.setupAttributes("uid", "mail");
		//select the user from LDAP tree
		iflp.selectUserToImport(Configuration.getLDAPUser());
		iflp.importUsers();
		UsersListPage ulp = iflp.completeImport(false);
		//Open the imported user details 
		UserEditPage uvp = ulp.editUser(userNameLongLDAP);
		User user = uvp.readUser();
		user.setPass(Configuration.getLDAPPass());
		user.setEmail("no");
		user.setLanguage(User.Lang.russian);
		//check existed attributes
		Assert.assertEquals(user.getUsername(), usernameLDAP);
		Assert.assertEquals(user.getUsernameLong(), userNameLongLDAP);
		uvp.close();
		logOut();
		
		//Login as LDAP user
		doLDAPLogin(usernameLDAP, Configuration.getLDAPPass());
        //Check that current user is the imported one
        User currentUser = page.getCurrentUser();
    	Assert.assertEquals(currentUser.getUsername(), usernameLDAP);
		Assert.assertEquals(currentUser.getUsernameLong(), userNameLongLDAP);
	}

	@BeforeClass
	public void doLogin() throws InterruptedException {
		int attempts = Configuration.getLoginAttempts();
		FILELOG.debug("do login");
		driver.get(Configuration.getBaseUrl());
		FILELOG.debug("Opening login page");
		LoginPage page = new LoginPage(driver);
		FavoritesPage fp;
		while (true) {
			if (attempts == 0) {
				driver.close();
				throw new IllegalStateException(
						"Can't do login. Check your credentials");
			}
			try {
				FILELOG.info("Login as " + username + ":" + password);
				fp = page.loginAs(username, password);
				(new WebDriverWait(driver, waiting)).until(ExpectedConditions
						.presenceOfElementLocated(By.id("main")));
				break;
			} catch (TimeoutException e) {
				attempts--;
				FILELOG.warn("Login attempt failed, " + attempts
						+ " attempts left");
			}
		}
		fp.goToAdminTools();
	}
    
	public void doLDAPLogin( String userName, String userPassword ) throws InterruptedException {
		driver.navigate().to(Configuration.getLDAPURL() + "/login");
		FILELOG.debug("Opening login page: "+ Configuration.getLDAPURL());
		LoginPage page = new LoginPage(driver);
		try {
			FILELOG.info("Login as " + userName + ":" + userPassword);
			page.loginAs(userName, userPassword);
			(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.id("main")));
		} catch (TimeoutException e) {
			FILELOG.warn("Login attempt failed, 0 attempts left");
		}
	}

	//@AfterClass 
	public void cleanUp() throws InterruptedException{
		logOut();
		doLogin();
		UsersListPage ulp = (new AdminPageBase(driver)).gotoUsers();
		ulp.editUser(userNameLongLDAP).deleteUser();
	}
	
	
	
	
	public void logOut() {
		driver.findElement(By.id("navbar-user-menu")).click();
		driver.findElement(By.xpath("//a[@href='/logoff']")).click();
		FILELOG.debug("Logout done");
	}
	
	public void deleteUser() {
		driver.findElement(By.xpath("//a[contains(text(),'Действия')]")).click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOfElementLocated(By.xpath("//a[text()='Удалить']")));
		driver.findElement(By.xpath("//a[text()='Удалить']")).click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.alertIsPresent());
		driver.switchTo().alert().accept();
		try {
			Thread.sleep(3000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
	}
	
   
}
