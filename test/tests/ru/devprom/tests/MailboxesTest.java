package ru.devprom.tests;

import org.openqa.selenium.By;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;
import org.testng.annotations.Test;

import ru.devprom.helpers.Configuration;
import ru.devprom.helpers.DataProviders;
import ru.devprom.helpers.MailHelper;
import ru.devprom.items.DevMail;
import ru.devprom.items.Project;
import ru.devprom.items.Request;
import ru.devprom.items.Template;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.admin.AdminPageBase;
import ru.devprom.pages.admin.NewAddressPage;
import ru.devprom.pages.admin.SupportAddressesPage;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requests.RequestDonePage;
import ru.devprom.pages.project.requests.RequestViewPage;
import ru.devprom.pages.project.requests.RequestsPage;

public class MailboxesTest extends AdminTestBase 
{
 private String subjectIMAP = "IMAP Test Message, ID: " + DataProviders.getUniqueString();
	
/**The method creates a mailbox account in the system and then removes it  */
	@Test
	public void testCreateAndRemove() {
		SupportAddressesPage sap = (new AdminPageBase(driver)).gotoSupportAddresses();
		NewAddressPage nap = sap.addAddress();
		String addressName = "Тестовый ящик "+DataProviders.getUniqueString();
		sap = nap.createMailbox(addressName, Configuration.getMailserver(), "IMAP", "143", "user@localhost", "user", "DEVPROM.WebTest");
		Assert.assertTrue(sap.isAddressPresent(addressName));
		sap = sap.deleteAddress(addressName);
		(new WebDriverWait(driver, waiting)).until(
				ExpectedConditions.invisibilityOfAllElements(
					driver.findElements(By.xpath("//td[@id='caption' and text()='"+addressName+"']"))
				)
			);
		Assert.assertFalse(sap.isAddressPresent(addressName));
	}
	
	/**The method sends a test mail, creates IMAP account in DEVPROM and checks it for a new mail */
	@Test
	public void testMailGatherIMAP() {
		String body = "This message was sent by Selenium test";
		DevMail email = new DevMail("user@localhost", "test@localhost", "test", subjectIMAP, body);
		SupportAddressesPage sap = (new AdminPageBase(driver)).gotoSupportAddresses();
		MailHelper.send(email);
		NewAddressPage nap = sap.addAddress();
		String addressName = "Тестовый ящик IMAP"+DataProviders.getUniqueString();
		sap = nap.createMailbox(addressName, Configuration.getMailserver(), "IMAP", "143", "user@localhost", "user", "DEVPROM.WebTest");
		Assert.assertTrue(sap.isAddressPresent(addressName));
	}

	/**The method checks if the issues were created when the messages were received */
	@Test (priority = 5)
	public void sendMailAndCheckIssue() {
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(
						this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		RequestsPage mip = favspage.gotoRequests();
		mip.showAll();
		driver.navigate().refresh();
		Request IMAPRequest = null;
	    try {
	     IMAPRequest = mip.findRequestByName(subjectIMAP);
	    FILELOG.info("Request found: " +  IMAPRequest);
	    }
	    catch (NoSuchElementException e2) {
	    	Assert.fail("Not found request with caption: " + subjectIMAP);
	    }
	    
	    RequestViewPage rvp = mip.clickToRequest(IMAPRequest.getId());
	    RequestDonePage rdp = rvp.completeRequest();
	    rvp = rdp.complete("Тест", "0.1");
	    
	    Assert.assertTrue(rvp.readState().contains("Выполнено"), "Пожелание POP3 не выполнено");
	}
	 
}
