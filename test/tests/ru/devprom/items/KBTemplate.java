package ru.devprom.items;

public class KBTemplate {

	private String id;
	private String name;
	private String content;
	private String parentId;
	public Boolean isDefault;
	
	public KBTemplate(String name) {
		this.name = name;
		this.id="";
		this.content="";
		this.parentId="";
		this.isDefault = false;
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

	public String getContent() {
		return content;
	}

	public void setContent(String content) {
		this.content = content;
	}

	public String getParentId() {
		return parentId;
	}

	public void setParentName(String parentId) {
		this.parentId = parentId;
	}


	
	
}
