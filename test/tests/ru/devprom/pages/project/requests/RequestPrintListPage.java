package ru.devprom.pages.project.requests;

import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;

import ru.devprom.items.Request;

public class RequestPrintListPage {
	private final WebDriver driver;

	RequestPrintListPage(WebDriver driver) {
		this.driver = driver;
	}

	public Request[] getPrintedRequests() {
		List<WebElement> rows = driver.findElements(By
				.xpath("html/body/table/tbody/tr"));
		rows.remove(0);
		Request[] requests = new Request[rows.size()];
		for (int i = 0; i < requests.length; i++) {
			requests[i] = new Request(
					rows.get(i).findElement(By.xpath("./td[1]")).getText(), 
					rows.get(i).findElement(By.xpath("./td[3]")).getText(),
					rows.get(i).findElement(By.xpath("./td[4]")).getText(), 
					rows.get(i).findElement(By.xpath("./td[5]")).getText(),
					rows.get(i).findElement(By.xpath("./td[2]")).getText());
		}

		return requests;

	}

}
