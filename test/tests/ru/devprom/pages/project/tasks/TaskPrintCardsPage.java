package ru.devprom.pages.project.tasks;

import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;

import ru.devprom.items.RTask;

public class TaskPrintCardsPage {
	private final WebDriver driver;
	
	public TaskPrintCardsPage(WebDriver driver) {
		this.driver = driver;
	}

	public RTask[] getPrintedTasks() {

		List<WebElement> cards = driver.findElements(By.className("taskcard"));
		RTask[] tasks = new RTask[cards.size()];
		for (int i = 0; i < tasks.length; i++) {
			String id="";
			String priority="";
			String executor="";
			String caption="";
			double estimation=0;
			String value="";
			List<WebElement> td = cards.get(i).findElements(By.xpath(".//td[@class='right']/table/tbody/tr"));
			if (td.size()==4) {
			id = cards.get(i)
					.findElement(
							By.xpath(".//td[@class='right']/table/tbody/tr[1]/td"))
					.getText();
	    	priority = cards.get(i)
					.findElement(
							By.xpath(".//td[@class='right']/table/tbody/tr[2]/td"))
					.getText();
	    	executor = cards.get(i)
					.findElement(
							By.xpath(".//td[@class='right']/table/tbody/tr[3]/td"))
					.getText();
	    	value = cards.get(i)
					.findElement(
							By.xpath(".//td[@class='right']/table/tbody/tr[4]/td"))
					.getText();
	    	estimation = !value.isEmpty() ? Double.parseDouble(value) : 0;
	    	caption = cards.get(i)
					.findElement(
							By.xpath(".//td[@class='left']/div[@class='caption']"))
					.getText();
			}
			else if (td.size()==5) {
				id = cards.get(i)
						.findElement(
								By.xpath(".//td[@class='right']/table/tbody/tr[1]/td"))
						.getText();
		    	priority = cards.get(i)
						.findElement(
								By.xpath(".//td[@class='right']/table/tbody/tr[3]/td"))
						.getText();
		    	executor = cards.get(i)
						.findElement(
								By.xpath(".//td[@class='right']/table/tbody/tr[4]/td"))
						.getText();
		    	value = cards.get(i)
						.findElement(
								By.xpath(".//td[@class='right']/table/tbody/tr[5]/td"))
						.getText();
		    	estimation = !value.isEmpty() ? Double.parseDouble(value) : 0;
		    	caption = cards.get(i)
						.findElement(
								By.xpath(".//td[@class='left']/div[@class='caption']"))
						.getText();
							
			}
			
			else System.out.println("ERROR: unknown cards format");
			
			tasks[i] = new RTask(id,caption,"",priority,"");
			tasks[i].setEstimation(estimation);
			tasks[i].setExecutor(executor);
		}

		return tasks;
	}

}
