package ru.devprom.items;

import java.util.Random;

public class ScrumTask {
	private String name;
	private String id = "";
	private String priority = "";
	private String type;
	private String executor = "";
	
	public ScrumTask(String name) {
       this.name = name;
       this.type = getRandomType();
	}
	

	public static String getRandomType() {
		String[] select = new String[] { "Проектирование", "Разработка", "Тестирование"};
		return select[new Random(System.currentTimeMillis()).nextInt(select.length - 1)];
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

	public void setPriority(String priority) {
		this.priority = priority;
	}

	public String getType() {
		return type;
	}

	public void setType(String type) {
		this.type = type;
	}

	public String getExecutor() {
		return executor;
	}

	public void setExecutor(String executor) {
		this.executor = executor;
	}

}
