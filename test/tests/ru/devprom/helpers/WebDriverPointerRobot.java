package ru.devprom.helpers;

import java.awt.AWTException;
import java.awt.Robot;

import org.openqa.selenium.Point;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.internal.Locatable;
import org.openqa.selenium.support.events.AbstractWebDriverEventListener;
import org.openqa.selenium.support.events.WebDriverEventListener;

public class WebDriverPointerRobot extends AbstractWebDriverEventListener implements WebDriverEventListener
{
	@Override
	public void beforeClickOn(WebElement element, WebDriver driver)
	{
		mouseMove(element);
	}
	
	public void beforeChangeValueOf(WebElement element, WebDriver driver)
	{
		mouseMove(element, 1);
	}

	public static void mouseMove( WebElement element ) {
		mouseMove(element, 1000);
	}

	public static void mouseMove( WebElement element, long waitAfter )
	{
		try {
			Point location = ((Locatable) element).getCoordinates().inViewPort();
			(new Robot()).mouseMove(
					location.getX() + element.getSize().width / 2,
					location.getY() + element.getSize().height / 2
				);	
		} catch (AWTException e) {
		}
		try {
			Thread.sleep(waitAfter);
		} catch (InterruptedException e1) {
		}
	}
}
