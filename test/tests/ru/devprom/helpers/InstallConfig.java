package ru.devprom.helpers;

import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.util.Properties;

import org.apache.log4j.LogManager;
import org.apache.log4j.Logger;

public class InstallConfig {

	public static Properties props = new Properties();
	private static InputStream fis;
	private static final Logger FILELOG = LogManager.getLogger("A1Logger");

	public static void readConfig() throws IOException {
		try {
			fis = new FileInputStream("resources/install.properties");
			try {
				props.load(fis);
			} catch (IOException e) {
				e.printStackTrace();
			} finally {
				fis.close();
			}
		} catch (FileNotFoundException e2) {
			FILELOG.error(
					"Install.properties is not found in resources folder. ", e2);
		}
	}

	public static Boolean isInstall() {
		if (props.getProperty("doinstall").equals("true"))
			return true;
		else
			return false;
	}

	public static String getEdition() {
		return (props.getProperty("edition", ""));
	}

	public static String getUsersCount() {
		return (props.getProperty("userscount", ""));
	}

	public static String getLicenseKey() {
		return (props.getProperty("licensekey", ""));
	}

	public static String getUpdatePath() {
		return (props.getProperty("updatepath", ""));
	}

	public static int getInstallTimeout() {
		return (Integer.parseInt(props.getProperty("installtimeout", "300")));
	}
}
