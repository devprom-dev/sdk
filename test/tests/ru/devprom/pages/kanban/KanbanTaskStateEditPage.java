package ru.devprom.pages.kanban;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;

public class KanbanTaskStateEditPage extends KanbanPageBase {

	@FindBy(id = "pm_StateSubmitBtn")
	protected WebElement saveBtn;
	
	@FindBy(xpath = "//span[@name='pm_StateAttributes']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addAttributeBtn;

	@FindBy(xpath = "//select[contains(@id,'ReferenceName')]")
	protected WebElement addAttributeSelect;
	
	@FindBy(xpath = "//input[contains(@id,'IsVisible')]")
	protected WebElement addAttributeIsVisible;
	
	@FindBy(xpath = "//input[contains(@id,'IsRequired')]")
	protected WebElement addAttributeIsRequired;


	public KanbanTaskStateEditPage(WebDriver driver) {
		super(driver);
	}

	public KanbanTaskStateEditPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	
	public void addAttribute(String attributeName, boolean isVisible, boolean isRequired){
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOf(addAttributeBtn));
		addAttributeBtn.click();
		new Select(addAttributeSelect).selectByVisibleText(attributeName);
		if (isVisible) {if (!driver.findElement(By.xpath("//input[contains(@id,'IsVisible')]")).isSelected())
		addAttributeIsVisible.click();
		}
		else {if (driver.findElement(By.xpath("//input[contains(@id,'IsVisible')]")).isSelected())
			addAttributeIsVisible.click();
		}
		if (isRequired) {if (!driver.findElement(By.xpath("//input[contains(@id,'IsRequired')]")).isSelected())
			addAttributeIsRequired.click();
		}
			else {if (driver.findElement(By.xpath("//input[contains(@id,'IsRequired')]")).isSelected())
				addAttributeIsRequired.click();
			}
		driver.findElement(
				By.xpath("//input[@value='stateattribute']/following-sibling::div[contains(@class,'embedded_footer')]//input[contains(@id,'saveEmbedded')]"))
				.click();
		
		}
	
	public KanbanTasksStatesPage saveChanges(){
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(saveBtn));
		submitDialog(saveBtn);
		return new KanbanTasksStatesPage(driver);
	}

}
