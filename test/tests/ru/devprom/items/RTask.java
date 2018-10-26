package ru.devprom.items;

import java.util.Random;

public class RTask implements Comparable<RTask> {
	private String id ="undefined";
	private String name;
	private String iteration="";
	private String type="";
	private String priority="";
	private String executor="";
	private String state="";
	private double estimation = 0.0;

	public RTask(String name, String executor, String type, double estimation) {
		this.name = name;
		this.executor = executor;
		this.type = type;
		this.estimation = estimation;
		this.iteration = "0.1";
		this.id = "";
	}
	
	public RTask (String id, String name, String type, String priority, String state){
		this.id = id;
		this.name = name;
		this.type = type;
		this.priority = priority;
		this.state=state;
	}


	public static String getRandomType() {
		String[] select = new String[] { "Анализ", "Проектирование", "Разработка",
				"Дизайн тестов", "Тестирование", "Документирование" };
		return select[new Random(System.currentTimeMillis()).nextInt(select.length - 1)];
	}
	
	public static double getRandomEstimation() {
		return (new Random(System.currentTimeMillis()).nextInt(10) + 10);
	}
	
	public static String getRandomPriority() {
		String[] select = new String[] { "Критично", "Высокий", "Обычный", "Низкий" };
		return select[new Random(System.currentTimeMillis()).nextInt(select.length - 1)];
	}
	
	
	public String toString() {
		return "RTask [id=" + id + ", name=" + name + ", type=" + type
				+ ", priority=" + priority + ", executor=" + executor + "]";
	}

	
	
	public int hashCode() {
		final int prime = 31;
		int result = 1;
		result = prime * result + ((id == null) ? 0 : id.hashCode());
		result = prime * result + ((name == null) ? 0 : name.hashCode());
		result = prime * result
				+ ((priority == null) ? 0 : priority.hashCode());
		result = prime * result + ((type == null) ? 0 : type.hashCode());
		return result;
	}
	
	

	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		RTask other = (RTask) obj;
		if (id == null) {
			if (other.id != null)
				return false;
		} else if (!id.equals(other.id))
			return false;
		if (priority == null) {
			if (other.priority != null)
				return false;
		} else if (!priority.equals(other.priority))
			return false;
		if (type == null) {
			if (other.type != null)
				return false;
		} else if (!type.equals(other.type))
			return false;
		return true;
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

	public String getIteration() {
		return iteration;
	}

	public void setIteration(String iteration) {
		this.iteration = iteration;
	}

	public String getExecutor() {
		return executor;
	}

	public void setExecutor(String executor) {
		this.executor = executor;
	}

	public double getEstimation() {
		return estimation;
	}

	public void setEstimation(double estimation) {
		this.estimation = estimation;
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

	public void setPriority(String priority) {
		this.priority = priority;
	}

	public String getState() {
		return state;
	}

	public void setState(String state) {
		this.state = state;
	}

	@Override
	public int compareTo(RTask task) {
		if (this.getId()==null || task.getId()==null) {
			if (this.getName()!=null && task.getName()!=null)
			return this.getName().hashCode() - task.getName().hashCode(); 
			else return this.hashCode() - task.hashCode();
					}
		else 
		return this.getId().hashCode() - task.getId().hashCode();
	}

}
