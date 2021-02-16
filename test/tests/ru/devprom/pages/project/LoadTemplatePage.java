package ru.devprom.pages.project;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;

public class LoadTemplatePage extends SDLCPojectPageBase {

	@FindBy(id = "ProjectTemplate")
	protected WebElement templateSelect;
	
	@FindBy(id = "pm_Project")
	protected WebElement commonSettingsBox;
	
	@FindBy(id = "Widgets")
	protected WebElement modulesSettingsBox;
	
	@FindBy(id = "Permissions")
	protected WebElement permissionsBox;
	
	@FindBy(id = "Workflow")
	protected WebElement statesBox;

	@FindBy(id = "Dictionaries")
	protected WebElement dictionariesBox;

	@FindBy(id = "Terminology")
	protected WebElement terminologyBox; 
	
	@FindBy(id = "Attributes")
	protected WebElement userAttributesBox;
	
	@FindBy(id = "Templates")
	protected WebElement templatesBox;
	
	@FindBy(id = "ProjectArtefacts")
	protected WebElement projectArtefactsBox;
	
	@FindBy(id = "btn")
	protected WebElement loadBtn;
	
	@FindBy(xpath = "//div[@class='btn-group']/a[text()='Выбрать все']")
	protected WebElement checkAllBtn;
	
	@FindBy(xpath = "//div[@class='btn-group']/a[text()='Снять выделение']")
	protected WebElement uncheckAllBtn;
	
	public LoadTemplatePage(WebDriver driver) {
		super(driver);
	}

	public LoadTemplatePage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public void importTemplate(String templateName){
		selectTemplate(templateName);
		loadBtn.click();
		new WebDriverWait(driver,waiting).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//form//div[contains(@class,'alert-success')]")));
		new WebDriverWait(driver,waiting).until(ExpectedConditions.invisibilityOfElementLocated(By.xpath("//form//div[contains(@class,'alert-success')]")));
	}
	
	public void checkAll(){
		checkAllBtn.click();
	}
	
	public void uncheckAll(){
		uncheckAllBtn.click();
	}
	
	public void selectTemplate(String templateName){
		new Select(templateSelect).selectByVisibleText(templateName);
	}
	
	public void checkImportCommonSettings(){
		if (!isBoxChechedJQuery("pm_Project")) commonSettingsBox.click();
	}
	
    public void uncheckImportCommonSettings(){
    	if (isBoxChechedJQuery("pm_Project")) commonSettingsBox.click();
	}
	
    public void checkImportModulesSettings(){
		if (!isBoxChechedJQuery("Widgets")) modulesSettingsBox.click();
	}
	
    public void uncheckImportModulesSettings(){
    	if (isBoxChechedJQuery("Widgets")) modulesSettingsBox.click();
	}
    
    public void checkImportPermissionSettings(){
		if (!isBoxChechedJQuery("Permissions")) permissionsBox.click();
	}
	
    public void uncheckImportPermissionSettings(){
    	if (isBoxChechedJQuery("Permissions")) permissionsBox.click();
	}
    
    public void checkImportStateSettings(){
		if (!isBoxChechedJQuery("Workflow")) statesBox.click();
	}

	public void checkImportDictionariesSettings(){
		if (!isBoxChechedJQuery("Dictionaries")) dictionariesBox.click();
	}

    public void uncheckImportStateSettings(){
    	if (isBoxChechedJQuery("Workflow")) statesBox.click();
	}
    
    public void checkImportTerminologySettings(){
		if (!isBoxChechedJQuery("Terminology")) terminologyBox.click();
	}
	
    public void uncheckImportTerminologySettings(){
    	if (isBoxChechedJQuery("Terminology")) terminologyBox.click();
	}
    
    public void checkImportUserAttributesSettings(){
		if (!isBoxChechedJQuery("Attributes")) userAttributesBox.click();
	}
	
    public void uncheckImportUserAttributesSettings(){
    	if (isBoxChechedJQuery("Attributes")) userAttributesBox.click();
	}
    
    public void checkImportTemplatesSettings(){
		if (!isBoxChechedJQuery("Templates")) templatesBox.click();
	}
	
    public void uncheckImportTemplatesSettings(){
    	if (isBoxChechedJQuery("Templates")) templatesBox.click();
	}
    
    public void checkImportProjectArtefactsSettings(){
		if (!isBoxChechedJQuery("ProjectArtefacts")) projectArtefactsBox.click();
	}
	
    public void uncheckImportProjectArtefactsSettings(){
    	if (isBoxChechedJQuery("ProjectArtefacts")) projectArtefactsBox.click();
	}
}
