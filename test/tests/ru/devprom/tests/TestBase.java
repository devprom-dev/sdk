package ru.devprom.tests;

import java.io.File;
import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.util.TimeZone;
import java.util.concurrent.TimeUnit;
import org.apache.commons.codec.binary.Base64;
import org.apache.log4j.Level;
import org.apache.log4j.LogManager;
import org.apache.log4j.Logger;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.chrome.ChromeOptions;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.firefox.FirefoxProfile;
import org.openqa.selenium.firefox.internal.ProfilesIni;
import org.openqa.selenium.ie.InternetExplorerDriver;
import org.openqa.selenium.support.events.EventFiringWebDriver;
import org.testng.Reporter;
import org.testng.annotations.AfterClass;
import org.testng.annotations.AfterMethod;
import org.testng.annotations.BeforeClass;
import org.testng.annotations.BeforeMethod;
import org.testng.annotations.BeforeTest;
import org.testng.annotations.Listeners;
import ru.devprom.helpers.Configuration;
import ru.devprom.helpers.DelaysListener;
import ru.devprom.helpers.HeatRowEventListener;
import ru.devprom.helpers.InstallConfig;
import ru.devprom.helpers.MyTestListener;
import ru.devprom.helpers.ScreenshotsHelper;
import ru.devprom.helpers.SystemOutAndErrToLog4jRedirecter;
import ru.devprom.helpers.TestAndMethodListener;
import ru.devprom.helpers.WebDriverLogger;
import ru.devprom.helpers.WebDriverPointerRobot;

@Listeners({ MyTestListener.class, TestAndMethodListener.class })
public class TestBase {
	private HeatRowEventListener listener;
	protected WebDriver driver;
	protected FirefoxProfile profile = null;
	protected static final Logger FILELOG = LogManager.getLogger("MAIN");
	protected String browserVersion = "ie";
	protected String firefoxProfile = "none";
	protected String loglevel = "debug";
	protected Integer timeoutValue = 2;
	protected String user;
	protected String username;
	protected String password;
	protected String baseURL;
	protected int installTimeout;
	protected int waiting;
	public boolean isDetailedLogs = false;
	public boolean robotPointerUsed = false;
	public static boolean isNeedScreenshots = false;
	public int delays = 0; 

	protected final String waterfallTemplateName = "Waterfall"; 
	protected final String kanbanTemplateName = "Kanban";
	protected final String scrumTemplateName = "Scrum";
	protected final String supportTemplateName = "Поддержка";
        protected final String requirementTemplateName = "Требования";

	@BeforeTest
	public void setUp() {
		FILELOG.info("do setUp");
        
		// redirecting console output to a file
		SystemOutAndErrToLog4jRedirecter.bindSystemOutAndErrToLog4j();

		// Read configuration for browser
		try {
			Configuration.readConfig();
			loglevel = Configuration.getLoglevel();
			InstallConfig.readConfig();
			installTimeout = InstallConfig.getInstallTimeout();
			waiting = Configuration.getWaiting();
			isNeedScreenshots = Configuration.isNeedScreenshots();
			TimeZone.setDefault(TimeZone.getTimeZone(Configuration.getTimezone()));
		} catch (IOException e) {
			FILELOG.error("Config read error. Hardcoded values will be used", e);
		}

		// Set LogLevel from the configuration file
		switch (loglevel) {
		case "debug":
			FILELOG.setLevel(Level.DEBUG);
			break;
		case "info":
			FILELOG.setLevel(Level.INFO);
			break;
		case "warn":
			FILELOG.setLevel(Level.WARN);
			break;
		case "error":
			FILELOG.setLevel(Level.ERROR);
			break;
		default:
			FILELOG.setLevel(Level.DEBUG);
		}
		System.out.println("Application log level set to: " + loglevel);
	}

	@BeforeClass
	public void runDriver() throws InterruptedException {
		browserVersion = Configuration.getBrowser();
		timeoutValue = Configuration.getTimeout();
		isDetailedLogs = Configuration.isDetailedLogs();
		robotPointerUsed = Configuration.robotPointerUsed();
		delays = Configuration.getDelays();
		firefoxProfile = Configuration.getFFProfile();
		baseURL = Configuration.getBaseUrl();
		username = Configuration.getUsername();
		password = Configuration.getPassword();
		user = Configuration.getFullUsername();
		
		if (isDetailedLogs || delays>0 || Configuration.isNeedScreenshots()) {
		    System.out.println("Very detailed logging enabled");
			System.out.println("Using system delays: "
					+ Configuration.getDelays() + "ms");
			WebDriver regularDriver;
			switch (browserVersion) {
			case "ie":
				regularDriver = new InternetExplorerDriver();
				System.out.println("Browser is InternetExplorer");
				break;

			case "firefox":
				System.out.println("Browser is Firefox");
				if (!firefoxProfile.equals("")) {
					ProfilesIni allProfiles = new ProfilesIni();
					profile = allProfiles.getProfile(firefoxProfile);
					System.out.println("Using Firefox profile: "
							+ firefoxProfile);
				} else {
					profile = new FirefoxProfile();
					System.out.println("Setup clean Firefox profile");
				}
				profile.setPreference("browser.download.folderList", 2);
				profile.setPreference("browser.cache.disk.enable", false);
				profile.setPreference("browser.cache.memory.enable", false);
				profile.setPreference("browser.helperApps.alwaysAsk.force", false);
				profile.setPreference("browser.download.manager.showWhenStarting", false);
				profile.setPreference("browser.download.dir", Configuration.getDownloadPath());
				profile.setPreference("browser.helperApps.neverAsk.saveToDisk", "application/vnd.ms-excel");
				regularDriver = new FirefoxDriver(profile);
				break;

			case "chrome":
				if (!Configuration.getChromeArgs().equals("")) {
					ChromeOptions option = new ChromeOptions();
					option.addArguments(Configuration.getChromeArgs());
					regularDriver = new ChromeDriver(option);
					System.out
							.println("Browser is Google Chrome running with arguments: "
									+ Configuration.getChromeArgs());
				} else {
					regularDriver = new ChromeDriver();
					System.out.println("Browser is Google Chrome");
				}
				break;
			default:
				regularDriver = new InternetExplorerDriver();
				System.out.println("Browser is InternetExplorer");
			}
			driver = new EventFiringWebDriver(regularDriver);
  		    ((EventFiringWebDriver) driver).register(new HeatRowEventListener());
			if (robotPointerUsed) ((EventFiringWebDriver) driver).register(new WebDriverPointerRobot());
			if (isDetailedLogs) ((EventFiringWebDriver) driver).register(new WebDriverLogger());
			if (delays>0) ((EventFiringWebDriver) driver).register(new DelaysListener());
		}

		else {
			switch (browserVersion) {
			case "ie":
				driver = new InternetExplorerDriver();
				System.out.println("Browser is InternetExplorer");
				break;
			case "firefox":
				System.out.println("Browser is Firefox");
				if (!firefoxProfile.equals("")) {
					ProfilesIni allProfiles = new ProfilesIni();
					profile = allProfiles.getProfile(firefoxProfile);
					System.out.println("Using Firefox profile: "
							+ firefoxProfile);
				} else {
					profile = new FirefoxProfile();
					System.out.println("Setup clean Firefox profile");
				}
				
				profile.setPreference("browser.download.folderList", 2);
				profile.setPreference("browser.helperApps.alwaysAsk.force",
						false);
				profile.setPreference(
						"browser.download.manager.showWhenStarting", false);
				profile.setPreference("browser.download.dir",
						Configuration.getDownloadPath());
				profile.setPreference("browser.helperApps.neverAsk.saveToDisk",
						"application/vnd.ms-excel");
				driver = new FirefoxDriver(profile);
				break;
			case "chrome":
				if (!Configuration.getChromeArgs().equals("")) {
					ChromeOptions option = new ChromeOptions();
					option.addArguments(Configuration.getChromeArgs());
					driver = new ChromeDriver(option);
					System.out
							.println("Browser is Google Chrome running with arguments: "
									+ Configuration.getChromeArgs());
				} else {
					driver = new ChromeDriver();
					System.out.println("Browser is Google Chrome");
				}
				break;
			/*
			 * case "opera": { System.setProperty("os.name", "windows"); driver
			 * = new OperaDriver(); }
			 */
			default:
				driver = new InternetExplorerDriver();
				System.out.println("Browser is InternetExplorer");
			}
		}
	
		// Set global timeouts from the configuration file
		driver.manage().timeouts()
				.implicitlyWait(timeoutValue, TimeUnit.SECONDS);
		System.out.println("Search for elements timeout = " + timeoutValue
				+ " seconds.");
	//	driver.manage().timeouts().pageLoadTimeout(waiting, TimeUnit.SECONDS);
	//	driver.manage().timeouts().setScriptTimeout(waiting, TimeUnit.SECONDS);
		// set currentUser field
		driver.manage().window().maximize();
	}

	@AfterClass
	public void tearDown() {
		FILELOG.info("do tearDown");
		driver.quit();
	}

	private static void deleteDirectory(File dir) {
		if (dir.isDirectory()) {
			String[] children = dir.list();
			for (int i = 0; i < children.length; i++) {
				File f = new File(dir, children[i]);
				deleteDirectory(f);
			}
			dir.delete();
		} else
			dir.delete();
	}
	
	public WebDriver getDriver(){
		return driver;
	}
}
