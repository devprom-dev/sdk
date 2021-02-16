package ru.devprom.items;

public class Document {
	private String name;
	private String body;
	private String id;

	public Document(String name, String body) {
		super();
		this.name = name;
		this.body = body;
		this.id = "";
	}

	public String getName() {
		return name;
	}

	public void setName(String name) {
		this.name = name;
	}

	public String getContent() {
		return body;
	}

	public void getContent(String body) {
		this.body = body;
	}

	public void setId(String id) {
		this.id = id;
	}

	public String getId() {
		return id;
	}
}
