package ru.devprom.items;

import java.io.File;
import java.util.List;

public class TestScenario implements Cloneable{
    private String id="";
	private String name;
	private String template="";
        private String type="";
	private String content="";
	private TestScenario parentPage;
	private int number;
	private List<File> attachments;
	private List<String> tags;
	private List<String> watchers;
	private List<Request> linkedRequests;
	private List<String> linkedRequirements;
	private String state;
	private String completeId;
        
	
	
    public TestScenario(String name) {
    	this.name=name;
    	this.state="В работе";
    	this.type="Тестовый сценарий";
    }
    
    public TestScenario clone() {
    	TestScenario result=null;
		try {
			result = (TestScenario)super.clone();
		} catch (CloneNotSupportedException e) {
			e.printStackTrace();
		}
		return result;
  }
	

	@Override
	public String toString() {
		return "TestScenario [name=" + name + ", state=" + state + "]";
	}


	public String getName() {
		return name;
	}


	public void setName(String name) {
		this.name = name;
	}


	public String getTemplate() {
		return template;
	}


	public void setTemplate(String template) {
		this.template = template;
	}


	public String getContent() {
		return content;
	}


	public void setContent(String content) {
		this.content = content;
	}


	public TestScenario getParentPage() {
		return parentPage;
	}


	public void setParentPage(TestScenario parentPage) {
		this.parentPage = parentPage;
	}


	public int getNumber() {
		return number;
	}


	public void setNumber(int number) {
		this.number = number;
	}


	public List<File> getAttachments() {
		return attachments;
	}


	public void setAttachments(List<File> attachments) {
		this.attachments = attachments;
	}


	public List<String> getTags() {
		return tags;
	}


	public void setTags(List<String> tags) {
		this.tags = tags;
	}


	public List<String> getWatchers() {
		return watchers;
	}


	public void setWatchers(List<String> watchers) {
		this.watchers = watchers;
	}


	public List<Request> getLinkedRequests() {
		return linkedRequests;
	}


	public void setLinkedRequests(List<Request> linkedRequests) {
		this.linkedRequests = linkedRequests;
	}


	public List<String> getLinkedRequirements() {
		return linkedRequirements;
	}


	public void setLinkedRequirements(List<String> linkedRequirements) {
		this.linkedRequirements = linkedRequirements;
	}


	public String getState() {
		return state;
	}


	public void setState(String state) {
		this.state = state;
	}


	public String getId() {
		return id;
	}


	public void setId(String id) {
		this.id = id;
		setCompleteId("[" + id + "]");
	}


	public String getCompleteId() {
		return completeId;
	}


	public void setCompleteId(String completeId) {
		this.completeId = completeId;
	}

    public void setType(String type) {
        this.type = type;
    }
    
    public String getType() {
		return type;
	}

  
	
	
}
