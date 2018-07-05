package ru.devprom.tests;

import java.awt.AWTException;
import java.io.File;
import java.io.IOException;

import org.testng.Assert;
import org.testng.annotations.Test;

import ru.devprom.helpers.Configuration;
import ru.devprom.helpers.CreateZip;
import ru.devprom.helpers.Messages;
import ru.devprom.pages.admin.AdminPageBase;
import ru.devprom.pages.admin.UpdatesPage;
import ru.devprom.pages.admin.UploadPage;

@Test(groups = "System")
public class UpdateTest extends AdminTestBase {
	String currentVersion;
	int updatesCountBefore;
	int updatesCountAfter;

	@Test
	// Correct update
	public void doUpgrade() throws IOException, AWTException,
			InterruptedException {
		UpdatesPage updp = (new AdminPageBase(driver)).gotoUpdatesPage();
		currentVersion = updp.getCurrentVersion();
		FILELOG.debug("Current version is: " + currentVersion);
		updatesCountBefore = updp.updatesCount();
		FILELOG.debug("Updates count befor is: " + updatesCountAfter);
		UploadPage upp = updp.gotoUploadPage();
		upp.upload_success(CreateZip.makeZip(currentVersion));
		FILELOG.debug("Upload done succesfully");
		UpdatesPage updpnew = new UpdatesPage(driver);
		currentVersion = updpnew.getCurrentVersion();
		FILELOG.debug("Now current version is: " + currentVersion);
		updatesCountAfter = updp.updatesCount();
		Assert.assertEquals(updatesCountAfter, updatesCountBefore + 1);
		Assert.assertEquals(currentVersion, CreateZip.currentVersion);
	}

	@Test
	// Update fails when incorrect file format
	public void doUpgradeWithBadFile() throws AWTException,
			InterruptedException, IOException {
		UpdatesPage updp = (new AdminPageBase(driver)).gotoUpdatesPage();
		updatesCountBefore = updp.updatesCount();
		UploadPage upp = updp.gotoUploadPage();
		upp = upp.upload_error(new File("resources/config.properties"));
		Assert.assertTrue(upp.hasErrorMessage(Messages.ERROR_MESSAGE_FORMAT
				.getText()));
		driver.get(Configuration.getBaseUrl() + "/admin/updates.php");
		updp = new UpdatesPage(driver);
		updatesCountAfter = updp.updatesCount();
		Assert.assertEquals(updatesCountAfter, updatesCountBefore);
	}

	@Test
	// Update fails when the version is out of date
	public void doUpgradeWithOutOfDateVersion() throws AWTException,
			InterruptedException, IOException {
		UpdatesPage updp = (new AdminPageBase(driver)).gotoUpdatesPage();
		updatesCountBefore = updp.updatesCount();
		UploadPage upp = updp.gotoUploadPage();
		upp = upp.upload_error(CreateZip.makeZip("2.0.0.0"));
		Assert.assertTrue(upp.hasErrorMessage(Messages.ERROR_MESSAGE_UPDATE
				.getText()));
		driver.get(Configuration.getBaseUrl() + "/admin/updates.php");
		updp = new UpdatesPage(driver);
		updatesCountAfter = updp.updatesCount();
		Assert.assertEquals(updatesCountAfter, updatesCountBefore);
	}

	@Test
	// Check if update fails when no required version installed
	public void doUpgradeAndVerifyDependency() throws AWTException,
			InterruptedException, IOException {
		UpdatesPage updp = (new AdminPageBase(driver)).gotoUpdatesPage();
		currentVersion = updp.getCurrentVersion();
		updatesCountBefore = updp.updatesCount();
		UploadPage upp = updp.gotoUploadPage();
		upp = upp.upload_error(CreateZip.makeZipR(currentVersion));
		Assert.assertTrue(upp.hasErrorMessage(Messages.ERROR_MESSAGE_DEPENDENCY
				.getText()));
		driver.get(Configuration.getBaseUrl() + "/admin/updates.php");
		updp = new UpdatesPage(driver);
		updatesCountAfter = updp.updatesCount();
		Assert.assertEquals(updatesCountAfter, updatesCountBefore);
	}

}
