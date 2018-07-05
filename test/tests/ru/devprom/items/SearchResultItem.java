package ru.devprom.items;

import org.openqa.selenium.WebElement;

public class SearchResultItem {

	private WebElement link;
	private String category;
	private String id;
	private String name;
	private String findString;
	private String bold;
	
	public SearchResultItem(WebElement link, String category, String id,
			String name, String findString, String bold) {
		super();
		this.link = link;
		this.category = category;
		this.id = id;
		this.name = name;
		this.findString = findString;
		this.setBold(bold);
	}
	
	@Override
	public String toString() {
		return "SearchResultItem [link=" + link + ", category=" + category
				+ ", id=" + id + ", name=" + name + ", findString="
				+ findString + "]";
	}

	public WebElement getLink() {
		return link;
	}

	public void setLink(WebElement link) {
		this.link = link;
	}

	public String getCategory() {
		return category;
	}

	public void setCategory(String category) {
		this.category = category;
	}

	public String getId() {
		return id;
	}

	public void setId(String id) {
		this.id = id;
	}

	public String getName() {
		return name;
	}

	public void setName(String name) {
		this.name = name;
	}

	public String getFindString() {
		return findString;
	}

	public void setFindString(String findString) {
		this.findString = findString;
	}

	public String getBold() {
		return bold;
	}

	public void setBold(String bold) {
		this.bold = bold;
	}
	
	
	
}
