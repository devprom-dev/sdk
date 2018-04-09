package ru.devprom.items;

public class Document {
	private String name;
	private String body;

	public Document(String name, String body) {
		super();
		this.name = name;
		this.body = body;
	}

	public String getName() {
		return name;
	}

	public void setName(String name) {
		this.name = name;
	}

	public String getBody() {
		return body;
	}

	public void setBody(String body) {
		this.body = body;
	}

}
