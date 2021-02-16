package ru.devprom.pages.project.autoactions;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;
import ru.devprom.items.Project;
import ru.devprom.pages.kanban.KanbanPageBase;
import ru.devprom.pages.CKEditor;


public class AutoActionNewPage extends KanbanPageBase {

	@FindBy(id = "pm_AutoActionCaption")
	protected WebElement nameEdit;
	
	@FindBy(id = "pm_AutoActionSubmitBtn")
	protected WebElement submitBtn;
	
	@FindBy(id = "pm_AutoActionState")
	protected WebElement AutoActionState;

	@FindBy(xpath = "//*[@id='IterationText']")
	protected WebElement sprintSelector;

	@FindBy(id = "pm_AutoActionEstimation")
	protected WebElement setEstimation;

	@FindBy(id = "pm_AutoActionTask_Caption")
	protected WebElement addTaskName;


	public AutoActionNewPage(WebDriver driver) {
		super(driver);
	}

	public AutoActionNewPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	 public AutoActionNewPage addName(String name){
		clickMainTab();
	   	nameEdit.sendKeys(name);
		return this;
	}

	public AutoActionNewPage addComment(String comment){
		clickCommentTab();
		CKEditor we = new CKEditor(driver);
		we.typeText(comment);
		return this;
	}

	//установка значения состояния пожелания на вкладке Изменить атрибуты
	public AutoActionNewPage setActionState(String state){
		clickActionsTab();
		Select selDr = new Select(driver.findElement(By.id("pm_AutoActionState")));
		selDr.selectByValue(state);
		return this;
	}

	public AutoActionNewPage save(){
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(submitBtn));
		submitBtn.click();
		try {
			Thread.sleep(6000);
		} catch (InterruptedException e) {
		}
		return new AutoActionNewPage(driver);
	}

	public void clickMainTab()
	{
		clickTab("main");
	}

	public void clickActionsTab()
	{
		clickTab("actions");
	}

	public void clickCommentTab()
	{
		clickTab("comment");
	}

	public void clickChangeAttributesTab(){clickTab("actions");}

	public void clickAddTaskTab(){clickTab("task");}


	//установка значения в 1-й ячейке Состояние (строка 1, столбец 1) поля Условие
	public AutoActionNewPage setCondition0(String condition){
		Select selDr = new Select(driver.findElement(By.xpath("//*[@name='Condition0']")));
		selDr.selectByValue(condition);
		return this;
	}

	//установка значения во 2-й ячейке Состояние (строка 2, столбец 1) поля Условие
	public AutoActionNewPage setCondition1(String condition){
		Select selDr = new Select(driver.findElement(By.xpath("//*[@name='Condition1']")));
		selDr.selectByValue(condition);
		return this;
	}

	//установка значения во 2-й ячейке Оператор (строка 2, столбец 2) поля Условие
	public AutoActionNewPage setOperator1(String operator){
		Select selDr = new Select(driver.findElement(By.xpath("//*[@name='Operator1']")));
		selDr.selectByValue(operator);
		return this;
	}

	//установка значения в 1-й ячейке Значение (строка 1, столбец 3) поля Условие
	public AutoActionNewPage setValue0(String value){
		driver.findElement(By.xpath("//*[@name='Value0']")).sendKeys(value);
		return this;
	}

	//установка значения во 2-й ячейке Значение (строка 2, столбец 3) поля Условие
	public AutoActionNewPage setValue1(String value){
		driver.findElement(By.xpath("//*[@name='Value1']")).sendKeys(value);
		return this;
	}

	public AutoActionNewPage setSprint(String sprint){
		clickChangeAttributesTab();
		sprintSelector.sendKeys(sprint);
		autocompleteSelect(sprint);
		return this;
	}

	//установка значения в поле Оценка
	public AutoActionNewPage setEstimation(String estimation){
		clickChangeAttributesTab();
		setEstimation.sendKeys(estimation);
		return this;
	}

	//добавление имени задачи, создававемой по автодействию
	public AutoActionNewPage addTaskName(String type){
		clickAddTaskTab();
		addTaskName.sendKeys(type);
		return this;
	}

	//установка типа задачи, создаваемой по автодействию
	public AutoActionNewPage addTaskType(String type) {
		clickAddTaskTab();
		Select selDr = new Select(driver.findElement(By.id("pm_AutoActionTask_TaskType")));
		selDr.selectByVisibleText(type);
		return this;
	}
}

