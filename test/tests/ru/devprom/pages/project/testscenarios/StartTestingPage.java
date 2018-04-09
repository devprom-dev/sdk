/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package ru.devprom.pages.project.testscenarios;

import org.openqa.selenium.Keys;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import ru.devprom.pages.project.SDLCPojectPageBase;

/**
 *
 * @author лена
 */
public class StartTestingPage extends SDLCPojectPageBase
{
    @FindBy(xpath = ".//*[@id='VersionText']")
	protected WebElement versionField;
        
    @FindBy(xpath = ".//*[@id='EnvironmentText']")
	protected WebElement envirenmentField;
        
    @FindBy(xpath = ".//*[@id='pm_TestCaption']")
	protected WebElement captionField;

    @FindBy(xpath = ".//*[@id='pm_TestSubmitBtn']")
	protected WebElement saveTestingBtn;

    public StartTestingPage(WebDriver driver) {
        super(driver);
    }

    public TestScenarioTestingPage startTest(String version, String envirenment)
    {
        if (!"".equals(envirenment)) {
            envirenmentField.sendKeys(envirenment);
            autocompleteSelect(envirenment);
        }
        versionField.sendKeys(version);
        autocompleteSelect(version);
        submitDialog(saveTestingBtn);
        FILELOG.debug("Save button had clicked on start testing form");
        return new TestScenarioTestingPage(driver);
    }
    
    public TestScenarioTestingPage startTestWithNewData(String version, String envirenment)
    {
        if (!"".equals(envirenment)) {
            envirenmentField.sendKeys(envirenment);
            envirenmentField.sendKeys(Keys.TAB);
        }
        if ( captionField.getText() == "" ) {
        	captionField.sendKeys("Название тестового отчета");
        }
        versionField.sendKeys(version);
        versionField.sendKeys(Keys.TAB);
        submitDialog(saveTestingBtn);
        FILELOG.debug("Save button had clicked on start testing form");
        return new TestScenarioTestingPage(driver);
    }
}
