package ru.devprom.items;

import java.util.List;

public class Milestone {
	private String id;
	private String date;
	private String name;
	private String description = "";
	private Boolean isDone = false;
	private String dataChangeReason = "";
	private String completeResult = "";
	private List<Request> requestsList = null;

	public Milestone(String date, String name) {
		this.date = date;
		this.name = name;
	}

	@Override
	public String toString() {
		return "Milestone [date=" + date + ", name=" + name + ", description="
				+ description + ", isDone=" + isDone + ", dataChangeReason="
				+ dataChangeReason + ", completeResult=" + completeResult + "]";
	}

	@Override
	public int hashCode() {
		final int prime = 31;
		int result = 1;
		result = prime * result + ((date == null) ? 0 : date.hashCode());
		result = prime * result
				+ ((description == null) ? 0 : description.hashCode());
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
		Milestone other = (Milestone) obj;
		if (date == null) {
			if (other.date != null)
				return false;
		} else if (!date.equals(other.date))
			return false;
		if (description == null) {
			if (other.description != null)
				return false;
		} else if (!description.equals(other.description))
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
	}
	public String getDate() {
		return date;
	}

	public void setDate(String date) {
		this.date = date;
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

	public Boolean getIsDone() {
		return isDone;
	}

	public void setIsDone(Boolean isDone) {
		this.isDone = isDone;
	}

	public String getDataChangeReason() {
		return dataChangeReason;
	}

	public void setDataChangeReason(String dataChangeReason) {
		this.dataChangeReason = dataChangeReason;
	}

	public String getCompleteResult() {
		return completeResult;
	}

	public void setCompleteResult(String completeResult) {
		this.completeResult = completeResult;
	}

	
	public void addRequest(Request request){
		requestsList.add(request);
	}
	
	
	public void deleteRequest(Request request){
		requestsList.remove(request);
	}
	
	
	
}
