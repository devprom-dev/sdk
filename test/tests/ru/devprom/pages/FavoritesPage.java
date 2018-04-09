package ru.devprom.pages;

import org.openqa.selenium.WebDriver;

public class FavoritesPage extends PageBase {

	public FavoritesPage(WebDriver driver) {
		super(driver);
		FILELOG.debug("Current URL is: " + driver.getCurrentUrl());
	}

}
