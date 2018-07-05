package ru.devprom.items;

import java.util.List;

public class Blogpost {

	private String id;
	private String name;
	private String content;
	private int number;
	private List<String> tags;
	private String author;
	
	

	public Blogpost(String name, String content) {
		this.name = name;
		this.content = content;
	}
	
	
	@Override
	public String toString() {
		return "Blogpost [id=" + id + ", name=" + name + ", content=" + content + ", author=" + author + "]";
	}



	public String getId() {
		return id;
	}
	public void setId(String id) {
		this.id = id;
	}
	
	public String getNumericId() {
		return id.substring(2);
	}
	
	public String getName() {
		return name;
	}
	public void setName(String name) {
		this.name = name;
	}
	public String getContent() {
		return content;
	}
	public void setContent(String content) {
		this.content = content;
	}
	public int getNumber() {
		return number;
	}
	public void setNumber(int number) {
		this.number = number;
	}
	public List<String> getTags() {
		return tags;
	}
	public void setTags(List<String> tags) {
		this.tags = tags;
	}
	public String getAuthor() {
		return author;
	}
	public void setAuthor(String author) {
		this.author = author;
	}

	
}
