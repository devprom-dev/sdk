package ru.devprom.items;

import java.util.ArrayList;
import java.util.List;

public class Requirement implements Cloneable{
    	
	private String id="";
	private String clearId;
	private String name;
	private String templateName;
	private String content;
	private Requirement parentPage;
	private String type;
	private int number;
	private String state;
	private String projectID;
	private String date;
	private String executor;
	private List<String> tags = new ArrayList<String>();
	private List<String> watchers = new ArrayList<String>();
	private List<String> requests = new ArrayList<String>();
	private List<String> functions = new ArrayList<String>();
	private List<String> sourceRequirements = new ArrayList<String>();
	private List<String> linkedRequirements = new ArrayList<String>();
	private List<String> testDocs = new ArrayList<String>();
	private List<Document> docs = new ArrayList<Document>();
	
	
	public Requirement(String name){
        this.name=name;
	}
	
	public Requirement(String name, String content){
        this.name=name;
        this.content=content;
	}
	
	public Requirement clone() {
		Requirement result=null;
		try {
			result = (Requirement)super.clone();
		} catch (CloneNotSupportedException e) {
			e.printStackTrace();
		}
		return result;
  }
	
	@Override
	public String toString() {
		return "Requirement [id=" + id + ", name=" + name + ", content="
				+ content + ", type=" + type + ", state=" + state + ", date="
				+ date + "]";
	}




	@Override
	public int hashCode() {
		final int prime = 31;
		int result = 1;
		result = prime * result + ((id == null) ? 0 : id.hashCode());
		result = prime * result + ((name == null) ? 0 : name.hashCode());
		return result;
	}




	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		Requirement other = (Requirement) obj;
		if (id == null) {
			if (other.id != null)
				return false;
		} else if (!id.equals(other.id))
			return false;
		if (name == null) {
			if (other.name != null)
				return false;
		} else if (!name.equals(other.name))
			return false;
		return true;
	}




	public String getId() {
		return id;
	}
	public void setId(String id) {
		this.id = id;
		this.clearId = id.split("-")[1];
	}
	public String getClearId() {
		return clearId;
	}
	public String getNumericId() {
		return id.split("-")[1];
	}
	public String getName() {
		return name;
	}
	public void setName(String name) {
		this.name = name;
	}
	public String getTemplateName() {
		return templateName;
	}
	public void setTemplateName(String templateName) {
		this.templateName = templateName;
	}
	public String getContent() {
		return content;
	}
	public void setContent(String content) {
		this.content = content;
	}
	public Requirement getParentPage() {
		return parentPage;
	}
	public void setParentPage(Requirement parentPage) {
		this.parentPage = parentPage;
	}
	public String getType() {
		return type;
	}
	public void setType(String type) {
		this.type = type;
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
	
	public void addTag(String tag){
		this.tags.add(tag);
	}
	
	public void removeTag(String tag){
		this.tags.remove(tag);
	}
	
	public List<String> getWatchers() {
		return watchers;
	}
	public void setWatchers(List<String> watchers) {
		this.watchers = watchers;
	}
	
	public void addWatcher(String watcher){
		this.watchers.add(watcher);
	}
	
	public void removeWatcher(String watcher){
		this.watchers.remove(watcher);
	}
	
	public List<String> getRequests() {
		return requests;
	}
	public void setRequests(List<String> requests) {
		this.requests = requests;
	}
	
	public void addRequest(String requestId){
		this.requests.add(requestId);
	}
	
	public void removeRequest(String requestId){
		this.requests.remove(requestId);
	}
	
	public List<String> getFunctions() {
		return functions;
	}
	public void setFunctions(List<String> functions) {
		this.functions = functions;
	}
	
	public void addFunction(String function){
		this.functions.add(function);
	}
	
	public void removeFunction(String function){
		this.functions.remove(function);
	}
	
	public List<String> getSourceRequirements() {
		return sourceRequirements;
	}
	public void setSourceRequirements(List<String> sourceRequirements) {
		this.sourceRequirements = sourceRequirements;
	}
	public List<String> getLinkedRequirements() {
		return linkedRequirements;
	}
	public void setLinkedRequirements(List<String> linkedRequirements) {
		this.linkedRequirements = linkedRequirements;
	}
	public List<String> getTestDocs() {
		return testDocs;
	}
	public void setTestDocs(List<String> testDocs) {
		this.testDocs = testDocs;
	}
	public List<Document> getDocs() {
		return docs;
	}
	public void setDocs(List<Document> docs) {
		this.docs = docs;
	}


	public String getDate() {
		return date;
	}


	public void setDate(String date) {
		this.date = date;
	}


	public String getState() {
		return state;
	}


	public void setState(String state) {
		this.state = state;
	}


	public String getProjectID() {
		return projectID;
	}


	public void setProjectID(String projectID) {
		this.projectID = projectID;
	}


	public String getExecutor() {
		return executor;
	}


	public void setExecutor(String executor) {
		this.executor = executor;
	}
	
	
	
	
	
}
