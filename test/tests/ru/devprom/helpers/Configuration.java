package ru.devprom.helpers;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.util.Properties;

import org.apache.log4j.LogManager;
import org.apache.log4j.Logger;

public class Configuration {
	public static Properties props = new Properties();
	private static InputStream fis;
	private static OutputStream fos;
	private static final Logger FILELOG = LogManager.getLogger("A1Logger");

	public static void readConfig() throws IOException {
		try {
			fis = new FileInputStream("resources/config.properties");
			try {
				props.load(fis);
			} catch (IOException e) {
				e.printStackTrace();
			} finally {
				fis.close();
			}
		} catch (FileNotFoundException e2) {
			FILELOG.error(
					"Couldn't find config.properties file. Creating a new one. ",
					e2);
			try {
				props.setProperty("browser", "firefox");
				props.setProperty("timeout", "2");
				props.setProperty("loglevel", "debug");
				props.setProperty("logdetails", "true");
				props.setProperty("ffprofile", "none");
				props.setProperty("username", "");
				props.setProperty("password", "");
				props.setProperty("baseURL", "http://localhost");
				props.setProperty("upgradetimeout", "300");
				props.setProperty("loginattempts", "3");
				props.setProperty("delays", "0");
				props.setProperty("stresstimeout", "10.0");
				props.setProperty("downloadpath", System.getenv("USERPROFILE")
						+ "\\Downloads\\");
				props.setProperty("delays", "0");
                props.setProperty("servicedeskBaseURL", "http://localhost/servicedesk");
                props.setProperty("servicedeskUsername", "set username in config.properties");
                props.setProperty("servicedeskPassword", "");
                props.setProperty("mailserver", "localhost");
                props.setProperty("svnurl", "http://localhost:8080");
                props.setProperty("workingcopy", "c:\\temp\\test");
                props.setProperty("svnuser", "admin");
                props.setProperty("svnpass", "admin");
                props.setProperty("ldapURL", "http://admin:secret@localhost:8090");
                props.setProperty("ldapserver", "localhost:10389");
                props.setProperty("ldapuser", "uid=admin,ou=system");
                props.setProperty("ldappass", "secret");
                props.setProperty("cachepath", "c:\\Devprom\\apache\\htdocs\\cache");
                props.setProperty("pathToRequestKanbanImage","resources//RequestKanban.jpg");
                props.setProperty("pathToBugImage","resources//bugImage.jpg");
                props.setProperty("pathToTestReport","resources//TestResult.xml");
                File propfile = new File("resources/config.properties");
				propfile.createNewFile();
				fos = new FileOutputStream("resources/config.properties");
				props.store(fos, "Global configuration");
			} catch (IOException e) {
				FILELOG.error(
						"Can't create new configurations file. Hardcoded settings will be used. ",
						e);
			} finally {
				fos.close();
			}
		}
	}

	public static String getBrowser() {
		return (props.getProperty("browser", "firefox"));
	}

	public static String getFFProfile() {
		return (props.getProperty("ffprofile", ""));
	}

	public static String getChromeArgs() {
		return (props.getProperty("chromeargs", ""));
	}

	public static int getTimeout() {
		return (Integer.parseInt(props.getProperty("timeout", "2")));
	}

	public static int getDelays() {
		return (Integer.parseInt(props.getProperty("delays", "0")));
	}
	
	public static int getPerformanceThreshold() {
		return (Integer.parseInt(props.getProperty("perfthreshold", "3")));
	}

	public static String getLoglevel() {
		return (props.getProperty("loglevel", "debug"));
	}
	
	public static String getMailserver() {
		return (props.getProperty("mailserver", "localhost"));
	}

	public static Boolean isDetailedLogs() {
		if (props.getProperty("logdetails", "false").equals("true"))
			return true;
		else
			return false;
	}

	public static Boolean robotPointerUsed() {
		if (props.getProperty("pointer-robot", "false").equals("true"))
			return true;
		else
			return false;
	}
	
	public static Boolean isNeedScreenshots() {
		return props.getProperty("needscreenshots", "false").equals("true");
	}
	
	public static String getUsername() {
		return (props.getProperty("username", ""));
	}
	
	public static String getFullUsername() {
		return (props.getProperty("fullusername", ""));
	}


	public static String getPassword() {
		return (props.getProperty("password", ""));
	}

	public static String getBaseUrl() {
		return (props.getProperty("baseURL", "http://localhost"));
	}

	public static int getUpgradeTimeout() {
		return (Integer.parseInt(props.getProperty("upgradetimeout", "30000")));
	}

	public static int getLoginAttempts() {
		return (Integer.parseInt(props.getProperty("loginattempts", "3")));
	}

	public static String getDownloadPath() {
		return (props.getProperty("downloadpath", System.getenv("USERPROFILE")
				+ "\\Downloads\\"));
	}

    public static String getServicedeskBaseUrl() {
        return props.getProperty("servicedeskBaseURL", "http://trunk.devprom.ru/servicedesk/");
    }

    public static String getServicedeskUsername() {
        return props.getProperty("servicedeskUsername", "http://trunk.devprom.ru/servicedesk/");
    }

    public static String getServicedeskPassword() {
        return props.getProperty("servicedeskPassword", "http://trunk.devprom.ru/servicedesk/");
    }

	public static String getReportFolder() {
		return (props.getProperty("reportpath","screenshots"));
	}

	public static int getWaiting() {
		return (Integer.parseInt(props.getProperty("waiting", "60")));
	}
	
	public static String getSVNUrl() {
		return (props.getProperty("svnurl","http://localhost:8080"));
	}
	
	public static String getSVNPath() {
			return (props.getProperty("svnpath",""));
	}
	
	public static String getWorkingCopy() {
		return (props.getProperty("workingcopy","c:\\temp\\test"));
	}
	
	public static String getSVNUser() {
		return (props.getProperty("svnuser","admin"));
	}
	
	public static String getSVNPass() {
		return (props.getProperty("svnpass","admin"));
	}

	public static String getLDAPURL() {
		return (props.getProperty("ldapURL","http://admin:secret@localhost:8090"));
	}

	public static String getLDAPserver() {
		return (props.getProperty("ldapserver","localhost:10389"));
	}

	public static String getLDAPUser() {
		return (props.getProperty("ldapuser","uid=admin,ou=system"));
	}

	public static String getLDAPPass() {
		return (props.getProperty("ldappass","secret"));
	}

	public static String getTimezone() {
		return (props.getProperty("timezone","GMT+04"));
	}

	public static double getStressTimeout() {
		return Double.parseDouble((props.getProperty("stresstimeout","10.0")));
	}

	public static int getPersistTimeout() {
		return 5;
	}

	public static boolean isNeedScreenshotsForEachStep() {
		return props.getProperty("needscreenshotsforeachstep", "false").equals("true");
	}
	
	public static String getCachePath() {
		return (props.getProperty("cachepath","c:\\Devprom\\apache\\htdocs\\cache"));
	}
        public static String getPathToRequestKanbanImage() {
		return (props.getProperty("pathToRequestKanbanImage","resources//RequestKanban.jpg"));
	}
        
	 public static String getPathToBugImage() {
		return (props.getProperty("pathToBugImage","resources//bugImage.jpg"));
	}
        
	public static String getPathToTestReport() {
		return (props.getProperty("pathToTestReport","resources//TestResult.xml"));
	}
}
