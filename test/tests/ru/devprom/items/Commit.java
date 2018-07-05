package ru.devprom.items;

public class Commit {

	private String id;
	private String version;
	private String dateTime;
	
	public Commit(String id, String version, String dateTime) {
		this.id = id;
		this.version = version;
		this.dateTime = dateTime;
	}

	public Commit(String record) {
		record = record.substring(1);
		String temp[] = record.split("\\]");
		record = temp[1].trim();
		record = record.replace("(", "");
		record = record.replace(")", "");
		this.id = temp[0];
		String tempp[] = record.split(" ");
		this.version = tempp[0].split(":")[1];
		this.dateTime = tempp[tempp.length-2] + " " + tempp[tempp.length-1];
	}
	
	public String getId() {
		return id;
	}

	public void setId(String id) {
		this.id = id;
	}

	public String getVersion() {
		return version;
	}

	public void setVersion(String version) {
		this.version = version;
	}

	public String getDateTime() {
		return dateTime;
	}

	public void setDateTime(String dateTime) {
		this.dateTime = dateTime;
	}

	@Override
	public String toString() {
		return "Commit [id=" + id + ", version=" + version + ", dateTime="
				+ dateTime + "]";
	}

	@Override
	public int hashCode() {
		final int prime = 31;
		int result = 1;
		result = prime * result
				+ ((dateTime == null) ? 0 : dateTime.hashCode());
		result = prime * result + ((id == null) ? 0 : id.hashCode());
		result = prime * result + ((version == null) ? 0 : version.hashCode());
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
		Commit other = (Commit) obj;
		if (dateTime == null) {
			if (other.dateTime != null)
				return false;
		} else if (!dateTime.equals(other.dateTime))
			return false;
		if (id == null) {
			if (other.id != null)
				return false;
		} else if (!id.equals(other.id))
			return false;
		if (version == null) {
			if (other.version != null)
				return false;
		} else if (!version.equals(other.version))
			return false;
		return true;
	}
	
	
	
	
}
