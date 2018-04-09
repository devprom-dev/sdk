package ru.devprom.helpers;

import org.apache.log4j.LogManager;
import org.apache.log4j.Logger;
import org.openqa.selenium.StaleElementReferenceException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.events.AbstractWebDriverEventListener;
import org.openqa.selenium.support.events.WebDriverEventListener;

public class WebDriverLogger extends AbstractWebDriverEventListener implements
		WebDriverEventListener {
	private static final Logger LOGGER = LogManager.getLogger("MAIN");
	private static final int threshold = Configuration.getPerformanceThreshold();
	private static long lastAction = System.currentTimeMillis();

	@Override
	public void afterClickOn(WebElement element, WebDriver driver) {
		LOGGER.debug("WebDriver DID click");
		checkPause();
	}

	@Override
	public void afterNavigateTo(String url, WebDriver driver) {
		LOGGER.debug("WebDriver navigated to '" + url + "'");
	}

	@Override
	public void beforeClickOn(WebElement element, WebDriver driver) {
		LOGGER.debug("WebDriver click on element - "
				+ elementDescription(element));
	}

	@Override
	public void beforeChangeValueOf(WebElement element, WebDriver driver) {
		LOGGER.debug("WebDriver will change value for element - "
				+ elementDescription(element));
	}

	@Override
	public void afterChangeValueOf(WebElement element, WebDriver driver) {
		LOGGER.debug("WebDriver changed value for element - "
				+ elementDescription(element));
		checkPause();
	}

	private String elementDescription(WebElement element) {
		try {

			String description = "tag:" + element.getTagName();
			if (element.getAttribute("id") != null) {
				description += " id: " + element.getAttribute("id");
			} else if (element.getAttribute("name") != null) {
				description += " name: " + element.getAttribute("name");
			}

			description += " ('" + element.getText() + "')";
			description += "     " + getCurrentTestRow() + " @ " +getCurrentPageMethodString();
			return description;
		} catch (StaleElementReferenceException e) {
			return "Stale Element";
		}
	}
	
	private void checkPause(){
		long now = System.currentTimeMillis();
		long pause = now - lastAction;
		lastAction = now;
		if (pause>(threshold*1000)) {
			StackTraceElement[] stack = Thread.currentThread().getStackTrace();
			String testMethod = null;
		    String pageMethod = null;
			for (StackTraceElement el:stack) {
				if (el.toString().contains("ru.devprom.pages")) { 
					   pageMethod = el.toString();
					   continue;
					}
				if (el.toString().contains("ru.devprom.tests")) {
				   testMethod = el.toString();
				   break;
				}
			}
			LOGGER.warn("LONG PAUSE! " + pause + " ms before page method " + pageMethod + " in test method: " + testMethod);
		}
	}
	
	private String getCurrentTestRow(){
		
	    String testRow = "undefined test row";
		StackTraceElement[] stack = Thread.currentThread().getStackTrace();
		for (StackTraceElement el:stack) {
			if (el.toString().contains("ru.devprom.tests"))  {
				testRow = el.toString().split("\\(")[1].replace(")", "");
				  return testRow;
			}
	}
	  return testRow;
	}
	
	private String getCurrentPageMethodString(){
		
	    String pageString = "undefined page method";
		StackTraceElement[] stack = Thread.currentThread().getStackTrace();
		for (StackTraceElement el:stack) {
			if (el.toString().contains("ru.devprom.pages")) {
				pageString = el.toString();
				 return pageString;
			}
	}
	  return pageString;
	}
	
	
}
