package ru.devprom.items;

import java.util.Hashtable;
import java.util.Map;

public class Template {
	private String name;
	private String description;
	private String fileName;
	private String fullName;

	public enum Lang {
		russian, english
	};

	private Lang language;
	private Map<String, Integer> idMap;
	private int value;
	public Boolean isVerified;

	private Template() {
		this.idMap = new Hashtable<String, Integer>();
		this.idMap.put("Scrum", 41);
		this.idMap.put("Kanban", 44);
		this.idMap.put("Waterfall", 31);
		this.idMap.put("Поддержка", 38);
        this.idMap.put("Требования", 49);

		this.name = "Scrum";
		this.description = "";
		this.fileName = "";
		this.language = Lang.russian;
		this.value = 0;
		this.isVerified = false;

	}

	public Template(String existed) {
		this();
		this.name = existed;

		if (!this.idMap.containsKey(existed)) {
			throw new RuntimeException("There is no template with name "
					+ existed);
		}
		this.value = this.idMap.get(existed);

	}

	public Template(String name, String description, String filename,
			Lang language) {
		this();
		this.name = name;
		this.description = description;
		this.fileName = filename;
		this.language = language;
		if (language.equals(Lang.russian))
		this.setFullName(name);
		else 
			this.setFullName(name);	
		if (name.charAt(0) != ' ')
			name = " " + name;
        
	}

	
	public String toString() {
		return "Template [name=" + name + ", description=" + description
				+ ", fileName=" + fileName + ", language=" + language + "]";
	}

	public int getValue() {
		return this.value;
	}

	public String getName() {
		return this.name;
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

	public String getFileName() {
		return fileName;
	}

	public void setFileName(String fileName) {
		this.fileName = fileName;
	}

	public Lang getLanguage() {
		return language;
	}

	public void setLanguage(Lang language) {
		this.language = language;
	}

	public String getFullName() {
		return fullName;
	}

	public void setFullName(String fullName) {
		this.fullName = fullName;
	}

	
}
