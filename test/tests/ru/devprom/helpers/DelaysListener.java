package ru.devprom.helpers;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.events.AbstractWebDriverEventListener;
import org.openqa.selenium.support.events.WebDriverEventListener;

public class DelaysListener extends AbstractWebDriverEventListener implements
WebDriverEventListener {

	private static final int delays = Configuration.getDelays();
	
	@Override
	public void beforeChangeValueOf(WebElement element, WebDriver driver) {
		try {
			Thread.sleep(delays);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
	}
	
	
}
