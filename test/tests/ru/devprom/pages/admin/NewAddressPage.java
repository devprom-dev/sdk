package ru.devprom.pages.admin;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

public class NewAddressPage extends AdminPageBase {

	
	@FindBy(id = "co_RemoteMailboxCaption")
	protected WebElement captionEdit;

	@FindBy(id = "co_RemoteMailboxHostAddress")
	protected WebElement mailserverEdit;

	@FindBy(id = "co_RemoteMailboxMailboxProvider")
	protected WebElement protocolSelect;

	@FindBy(id = "co_RemoteMailboxPortServer")
	protected WebElement portEdit;

	@FindBy(id = "co_RemoteMailboxEmailAddress")
	protected WebElement mailboxEdit;
	
	@FindBy(id = "co_RemoteMailboxEmailPassword")
	protected WebElement passwordEdit;
	
	@FindBy(id = "co_RemoteMailboxSenderAddress")
	protected WebElement senderAddressEdit;

	@FindBy(id = "ProjectText")
	protected WebElement projectSelect;
	
	@FindBy(id = "co_RemoteMailboxSubmitBtn")
	protected WebElement saveBtn;
	
	public NewAddressPage(WebDriver driver) {
		super(driver);
	}
	
	public SupportAddressesPage createMailbox(String name, String server, String protocol, String port, String mailbox, String password, String projectLink){
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(captionEdit));
		setName(name);
		setMailserver(server);
		setProtocol(protocol);
		setPort(port);
		setMailbox(mailbox);
		setPassword(password);
		setProjectLink(projectLink);
		setSenderAddress("info@devprom.ru");
		submitDialog(saveBtn);
		driver.navigate().refresh();
		return new SupportAddressesPage(driver);
	}
	
	
	
	public void setName(String name){
		captionEdit.clear();
		captionEdit.sendKeys(name);
	}
	
	public void setMailserver(String server){
		mailserverEdit.clear();
		mailserverEdit.sendKeys(server);
	}
	
	public void setProtocol(String protocol){
		if (protocol.equalsIgnoreCase("pop3"))
				(new Select(protocolSelect)).selectByValue("1");
		else if  (protocol.equalsIgnoreCase("imap"))
			(new Select(protocolSelect)).selectByValue("2");
		else FILELOG.warn("Can't set protocol: " +protocol+ " POP3 value will be used");
	}

	public void setPort(String port){
		portEdit.clear();
		portEdit.sendKeys(port);
	}
	
	public void setMailbox(String mailbox){
		mailboxEdit.clear();
		mailboxEdit.sendKeys(mailbox);
	}
	
	public void setSenderAddress(String address){
		senderAddressEdit.clear();
		senderAddressEdit.sendKeys(address);
	}

	public void setPassword(String pass){
		passwordEdit.clear();
		passwordEdit.sendKeys(pass);
	}
	
	public void setProjectLink(String projectname){
		projectSelect.clear();
		projectSelect.sendKeys(projectname);
		autocompleteSelect(projectname);
	}
	
}
