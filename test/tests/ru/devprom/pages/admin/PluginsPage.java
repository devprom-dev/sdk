package ru.devprom.pages.admin;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

public class PluginsPage extends AdminPageBase {

	
	
	public PluginsPage(WebDriver driver) {
		super(driver);
	}

	public PluginsPage disablePlugin(String pluginName)
	{
       WebElement element = driver.findElement(By.xpath("//tr[contains(@id,'pluginlist1_row_')]/td[@id='file' and text()='"+pluginName+"']/following-sibling::td//a[contains(.,'Отключить')]"));
       clickOnInvisibleElement(element);
       (new WebDriverWait(driver, waiting)).until(
    		   ExpectedConditions.presenceOfElementLocated(By.xpath("//tr[contains(@id,'pluginlist1_row_')]/td[@id='file' and text()='"+pluginName+"' and contains(@style, 'color:silver')]"))
    		   );
		try {
			Thread.sleep(5000);
		} catch (InterruptedException e) {
		}
       return new PluginsPage(driver);
	}

	public PluginsPage enablePlugin(String pluginName)
	{
		WebElement element = driver.findElement(By.xpath("//tr[contains(@id,'pluginlist1_row_')]/td[@id='file' and text()='"+pluginName+"']/following-sibling::td//a[contains(.,'Подключить')]"));
	    clickOnInvisibleElement(element);
		(new WebDriverWait(driver, waiting)).until(
				ExpectedConditions.presenceOfElementLocated(By.xpath("//tr[contains(@id,'pluginlist1_row_')]/td[@id='file' and text()='"+pluginName+"' and not(contains(@style, 'color:silver'))]"))
				);
		try {
			Thread.sleep(5000);
		} catch (InterruptedException e) {
		}
	    return new PluginsPage(driver);
	}

	public Boolean isPluginEnabled(String pluginName)
	{
		if (driver.findElements(By.xpath("//tr[contains(@id,'pluginlist1_row_')]/td[@id='file' and text()='"+pluginName+"' and contains(@style, 'color:black')]")).size() > 0) {
			return true;
		}
		else if (driver.findElements(By.xpath("//tr[contains(@id,'pluginlist1_row_')]/td[@id='file' and text()='"+pluginName+"' and contains(@style, 'color:silver')]")).size() > 0) {
			return false;
		}
		else throw new IllegalStateException("Plugin "+ pluginName + " is not found in the list or there is an error in style");
	}
	
	
	
}
