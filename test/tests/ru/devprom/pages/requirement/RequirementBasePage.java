/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package ru.devprom.pages.requirement;

import org.openqa.selenium.By;
import org.openqa.selenium.NotFoundException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import ru.devprom.items.Project;
import ru.devprom.pages.project.IProjectBase;
import ru.devprom.pages.project.ProjectPageBase;
import ru.devprom.pages.project.requirements.RequirementsDocsPage;
import ru.devprom.pages.project.requirements.RequirementsPage;
import ru.devprom.pages.project.requirements.RequirementsTypesPage;
import ru.devprom.pages.project.requirements.TraceMatrixPage;

/**
 *
 * @author лена
 */
public class RequirementBasePage extends ProjectPageBase implements IProjectBase{
    
    @FindBy(xpath = "//ul[@id='tab_favs']/a")
	protected WebElement favoriteLink;
    
    @FindBy(xpath = "//*[@uid='requirements-docs']")
	protected WebElement requirementDocsItem;
    
    @FindBy(xpath = ".//*[contains(text(),'Входящие запросы')]")
	protected WebElement inboxRequestsItem;
    
    @FindBy(xpath = "//*[@uid='requirements-list']")
	protected WebElement reestrRequirementItem;
    
    @FindBy(xpath = "//*[@id='menu_favs']//*[@id='menu-group-settings']")
	protected WebElement settingsItem;
    
    @FindBy(xpath = "//*[@id='menu_favs']//*[@uid='dicts-requirementtype']")
	protected WebElement requirementTypesItem;
    
    @FindBy(xpath = "//*[@id='menu_favs']//*[@uid='requirementsmatrix']")
	protected WebElement traceMatrixItem;

    public RequirementBasePage(WebDriver driver, Project project) {
        super(driver, project);
    }

     public RequirementBasePage(WebDriver driver) {
        super(driver);
    }

     protected void clickOnFavoriteLink() {
    	 try {
        	 //favoriteLink.click();
    	 }
    	 catch( NotFoundException ex ) {
    	 }
     }
     
    public RequirementsDocsPage gotoRequirementsDocs() {
    	clickOnFavoriteLink();
        clickOnInvisibleElement(requirementDocsItem);
        return new RequirementsDocsPage(driver);
    }

    public InboxRequestsPage gotoInboxRequests() {
    	clickOnFavoriteLink();
        clickOnInvisibleElement(inboxRequestsItem);
        return new InboxRequestsPage(driver);
    }

    public RequirementsPage gotoReestrRequirements() {
    	clickOnFavoriteLink();
        clickOnInvisibleElement(reestrRequirementItem);
        return new RequirementsPage(driver);
    }

    public RequirementsTypesPage gotoRequirementsTypes() {
    	clickOnFavoriteLink();
        clickOnInvisibleElement(settingsItem);
        clickOnInvisibleElement(requirementTypesItem);
        return new RequirementsTypesPage(driver);
    }

    public TraceMatrixPage gotoMatrixTrace() {
    	clickOnFavoriteLink();
        clickOnInvisibleElement(traceMatrixItem);
        return new TraceMatrixPage(driver);
    }
    
     
}
