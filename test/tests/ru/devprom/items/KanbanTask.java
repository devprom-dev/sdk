package ru.devprom.items;

import java.util.ArrayList;
import java.util.List;
import java.util.Random;

public class KanbanTask {
	private String id ="";
	private String name;
	private String description="";
	private String type;
	private String priority;
	private String author ="";
	private String owner="";
	private String number="";
	private String state = "";
	private List<String> attachments = new ArrayList<String>();
	private List<String> linkedTasks = new ArrayList<String>();
	private List<String> tags = new ArrayList<String>();
	private List<String> watchers = new ArrayList<String>();
	
	
	public KanbanTask(String name) {
		this.name = name;
	}
	
	
	public String toString() {
		return "KanbanTask [id=" + id + ", name=" + name + ", description="
				+ description + ", type=" + type + ", priority=" + priority
				+ ", author=" + author + ", owner=" + owner + ", number="
				+ number + ", state=" + state + "]";
	}

	public static String getRandomPriority() {
		String[] select = new String[] { "Критично", "Высокий", "Обычный",
				"Низкий" };
		return select[new Random(System.currentTimeMillis()).nextInt(select.length - 1)];
	}


	public String getId() {
		return id;
	}
	
	public String getNumericId() {
		return id.substring(2);
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
	public String getDescription() {
		return description;
	}
	public void setDescription(String description) {
		this.description = description;
	}
	public String getType() {
		return type;
	}
	public void setType(String type) {
		this.type = type;
	}
	public String getPriority() {
		return priority;
	}
    public void setState(String state) {
        this.state = state;
	}
	public String getState() {
		return state;
	}

	public void setPriority(String priority) {
		this.priority = priority;
	}
	public String getAuthor() {
		return author;
	}
	public void setAuthor(String author) {
		this.author = author;
	}
	public String getOwner() {
		return owner;
	}
	public void setOwner(String owner) {
		this.owner = owner;
	}
	public String getNumber() {
		return number;
	}
	public void setNumber(String number) {
		this.number = number;
	}
	public List<String> getAttachments() {
		return attachments;
	}
	public void addAttachment(String attachment) {
		this.attachments.add(attachment);
	}
	public void removeAttachment(String attachment) {
		this.attachments.remove(attachment);
	}
	public List<String> getLinkedTasks() {
		return linkedTasks;
	}
	public void addLinkedTask(String linkedTask) {
		this.linkedTasks.add(linkedTask);
	}
	public void removeLinkedTask(String linkedTask) {
		this.linkedTasks.remove(linkedTask);
	}
	public List<String> getTags() {
		return tags;
	}
	public void addTag(String tag) {
		this.tags.add(tag);
	}
	public void removeTag(String tag) {
		this.tags.remove(tag);
	}
	public List<String> getWatchers() {
		return watchers;
	}
	public void addWatcher(String watcher) {
		this.watchers.add(watcher);
	}
	public void removeWatcher(String watcher) {
		this.watchers.remove(watcher);
	}
	
}
