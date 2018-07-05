package ru.devprom.items;

public class ProductFunction {

	private String id;
	private String name;
	private String description = "";
	private String importance = "";
	private String[] tags;
	private String[] requirements;

	public ProductFunction(String name) {
		this.name = name;
	}

	
	public ProductFunction(String name, String[] requirements) {
		this.name = name;
		this.requirements = requirements;
	}

	public ProductFunction(String name, String description, String importance,
			String[] tags, String[] requirements) {
		this(name, requirements);
		this.description = description;
		this.importance = importance;
		this.tags = tags;
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

	public String getImportance() {
		return importance;
	}

	public void setImportance(String importance) {
		this.importance = importance;
	}

	public String[] getTags() {
		return tags;
	}

	public void setTags(String[] tags) {
		this.tags = tags;
	}

	public String[] getRequirements() {
		return requirements;
	}

	public void setRequirements(String[] requirements) {
		this.requirements = requirements;
	}


	public String getId() {
		return id;
	}


	public void setId(String id) {
		this.id = id;
	}

}
