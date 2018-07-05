package ru.devprom.items;

public class KnowledgeBase {
    
	private String id;
	private String name;
	private String content;
	
	public KnowledgeBase(String name){
		this.name=name;
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
	
	
	
	
}
