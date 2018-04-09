package ru.devprom.items;

public class ScrumIssue {
private String name;
private String id;
private String priority;
private String epic ="";
private String description;


public ScrumIssue(String name, String priority, String description) {
	super();
	this.name = name;
	this.priority = priority;
	this.description = description;
}


public String getName() {
	return name;
}


public void setName(String name) {
	this.name = name;
}


public String getId() {
	return id;
}


public void setId(String id) {
	this.id = id;
}


public String getPriority() {
	return priority;
}

public String getNumericId() {
	return id.substring(2);
}


public void setPriority(String priority) {
	this.priority = priority;
}


public String getEpic() {
	return epic;
}


public void setEpic(String epic) {
	this.epic = epic;
}


public String getDescription() {
	return description;
}


public void setDescription(String description) {
	this.description = description;
}



	
	
	
}
