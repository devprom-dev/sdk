package ru.devprom.servicedesk.pages;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.JavascriptExecutor;

import java.util.List;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
public class EditIssuePage extends ServicedeskPage {

    public EditIssuePage(WebDriver driver) {
        super(driver);
    }

    public EditIssuePage enterIssueTitle(String issueTitle) {
        WebElement caption = driver.findElement(By.id("issue_caption"));
        caption.clear();
        caption.sendKeys(issueTitle);
        return this;
    }

    public EditIssuePage enterIssueDescription(String issueDescription) {
        WebElement description = driver.findElement(By.id("issue_description"));
        description.clear();
        description.sendKeys(issueDescription);
        return this;
    }

    public EditIssuePage selectPriority(String newPriority) {
        Select select = new Select(driver.findElement(By.xpath("//*[@id='issue_severity']")));
        select.selectByVisibleText(newPriority);
        return this;
    }

    public EditIssuePage selectType(String newType) {
        Select select = new Select(driver.findElement(By.xpath("//*[@id='issue_issueType']")));
        select.selectByVisibleText(newType);
        return this;
    }

    public EditIssuePage selectProduct(String newProduct) {
        Select select = new Select(driver.findElement(By.xpath("//*[@id='issue_product']")));
        select.selectByVisibleText(newProduct);
        return this;
    }


    public ViewIssuePage clickSubmitButton() {
        driver.findElement(By.xpath("//*[@type='submit']")).click();
        return new ViewIssuePage(driver);
    }

    public String getDifferentProduct() {
        return getFirstNotSelectedOption("issue_product");
    }

    public String getDifferentPriority() {
        return getFirstNotSelectedOption("issue_severity");
    }

    public String getDifferentType() {
        return getFirstNotSelectedOption("issue_issueType");
    }


    protected String getFirstNotSelectedOption(String selectFieldId) {
		// make control visible
		((JavascriptExecutor) driver).executeScript(String.format("document.evaluate(\"//select[@id='%s']\", document, null, 9, null).singleNodeValue.removeAttribute('class')", selectFieldId));

        String xpath = String.format("//*[@id='%s']/option[not(@selected)]", selectFieldId);
        List<WebElement> options = driver.findElements(By.xpath(xpath));
        return options.size() > 0
                ? options.get(0).getText()
                : new Select(driver.findElement(By.id(selectFieldId))).getFirstSelectedOption().getText();
    }
}
