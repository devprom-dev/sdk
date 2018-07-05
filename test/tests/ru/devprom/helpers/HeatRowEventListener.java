package ru.devprom.helpers;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.events.WebDriverEventListener;


public class HeatRowEventListener implements WebDriverEventListener {
    
    public void afterClickOn(WebElement element, WebDriver driver) {

    }

    public void beforeClickOn(WebElement element, WebDriver driver) {
    	//ScreenshotsHelper.takeScreenshotWithHighlightElement(driver, element, "EACH_");
    }

    public void afterChangeValueOf(WebElement element, WebDriver driver) {

    }

    public void beforeChangeValueOf(WebElement element, WebDriver driver) {
     //   Page page = takeScreenshot(driver);
     //   extractElementLocationAndSave(page, element);
    }

    public void beforeNavigateRefresh(WebDriver driver) {
    }
   
    public void afterNavigateRefresh(WebDriver driver) {
    }

    public void beforeNavigateTo(String url, WebDriver driver) {
        //To change body of implemented methods use File | Settings | File Templates.
    }

    public void afterNavigateTo(String url, WebDriver driver) {
        //To change body of implemented methods use File | Settings | File Templates.
    }

    public void beforeNavigateBack(WebDriver driver) {
        //To change body of implemented methods use File | Settings | File Templates.
    }

    public void afterNavigateBack(WebDriver driver) {
        //To change body of implemented methods use File | Settings | File Templates.
    }

    public void beforeNavigateForward(WebDriver driver) {
        //To change body of implemented methods use File | Settings | File Templates.
    }

    public void afterNavigateForward(WebDriver driver) {
        //To change body of implemented methods use File | Settings | File Templates.
    }

    public void beforeFindBy(By by, WebElement element, WebDriver driver) {
        //To change body of implemented methods use File | Settings | File Templates.
    }

    public void afterFindBy(By by, WebElement element, WebDriver driver) {
        //To change body of implemented methods use File | Settings | File Templates.
    }

    public void beforeScript(String script, WebDriver driver) {
        //To change body of implemented methods use File | Settings | File Templates.
    }

    public void afterScript(String script, WebDriver driver) {
        //To change body of implemented methods use File | Settings | File Templates.
    }

    public void onException(Throwable throwable, WebDriver driver) {
    }

}
