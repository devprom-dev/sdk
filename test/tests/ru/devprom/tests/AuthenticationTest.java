package ru.devprom.tests;

import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.*;

import org.apache.commons.codec.binary.Base64;
import org.apache.log4j.LogManager;
import org.apache.log4j.Logger;
import org.testng.Assert;
import org.testng.annotations.BeforeTest;
import org.testng.annotations.Test;

import ru.devprom.helpers.Configuration;
import ru.devprom.helpers.DataProviders;


public class AuthenticationTest  extends TestBase {
	
	protected static final Logger FILELOG = LogManager.getLogger("MAIN");
	
	@BeforeTest
	public void configurationInitialization(){
		try {
			Configuration.readConfig();
		} catch (IOException e) {
			FILELOG.error("Config read error. Hardcoded values will be used", e);
		}
	}
	
	
	@Test
	public void testHttpBasicAuthentication() {
		InputStream is = null;
		InputStreamReader isr = null;
		
        try {
			String webPage = Configuration.getBaseUrl()+"/pm/devprom_webtest/api/v1/tasks";
			String name = Configuration.getUsername();
			String password = Configuration.getPassword();

			String authString = name + ":" + password;
			FILELOG.info("auth string: " + authString);
			byte[] authEncBytes = Base64.encodeBase64(authString.getBytes());
			String authStringEnc = new String(authEncBytes);

			URL url = new URL(webPage);
			URLConnection urlConnection = url.openConnection();
			urlConnection.setRequestProperty("Authorization", "Basic " + authStringEnc);
			 is = urlConnection.getInputStream();
			isr = new InputStreamReader(is);
			int numCharsRead;
			char[] charArray = new char[1024];
			StringBuffer sb = new StringBuffer();
			while ((numCharsRead = isr.read(charArray)) > 0) {
				sb.append(charArray, 0, numCharsRead);
			}
			String result = sb.toString();
            Assert.assertFalse(result.contains("404/Not Found"), "Login failed with correct credentials");
		} catch (MalformedURLException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
        finally {
        	try {
				isr.close();
				is.close();
			} catch (IOException e) {
				e.printStackTrace();
			}
        	
        }
	}  
		 
	
	@Test
	public void testHttpBasicAuthenticationBadUsername() {
        try {
			String webPage = Configuration.getBaseUrl()+"/pm/devprom_webtest/api/v1/tasks";
			String name = "FakeUsername"+DataProviders.getUniqueString();
			String password = Configuration.getPassword();

			String authString = name + ":" + password;
			FILELOG.info("auth string: " + authString);
			byte[] authEncBytes = Base64.encodeBase64(authString.getBytes());
			String authStringEnc = new String(authEncBytes);

			URL url = new URL(webPage);
			HttpURLConnection urlConnection = (HttpURLConnection) url.openConnection();
			urlConnection.setInstanceFollowRedirects(false);
			urlConnection.setRequestProperty("Authorization", "Basic " + authStringEnc);

			Assert.assertTrue(urlConnection.getResponseCode() == 302, "There is no access using wrong user name");
		} catch (MalformedURLException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
        finally {
        }
	}  
		
	
	@Test
	public void testHttpBasicAuthenticationBadPass()
	{
        try {
			String webPage = Configuration.getBaseUrl()+"/pm/devprom_webtest/api/v1/tasks";
			String name = Configuration.getUsername();
			String password = "FakePass"+DataProviders.getUniqueString();

			String authString = name + ":" + password;
			FILELOG.info("auth string: " + authString);
			byte[] authEncBytes = Base64.encodeBase64(authString.getBytes());
			String authStringEnc = new String(authEncBytes);

			URL url = new URL(webPage);
			HttpURLConnection urlConnection = (HttpURLConnection) url.openConnection();
			urlConnection.setInstanceFollowRedirects(false);
			urlConnection.setRequestProperty("Authorization", "Basic " + authStringEnc);

			Assert.assertTrue(urlConnection.getResponseCode() == 302, "There is no access using wrong password");
		} catch (MalformedURLException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
        finally {
        }
	}  
}
