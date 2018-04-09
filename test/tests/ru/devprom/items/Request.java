package ru.devprom.items;

import java.util.ArrayList;
import java.util.List;
import java.util.Random;

public class Request implements Comparable<Request>, Cloneable {

	private String id = "";
	private String name = "";
	private String description = "";
	private String priority = "";
	private double estimation = 10.0;
	private String type = "";
	private String state = "";
	private String pfunction = "";
	private String originator = "";
	private String version = "";
	private String closedVersion = "";
	private String release = "";
	private List<RTask> tasks = new ArrayList<RTask>();
	private List<String> tags = new ArrayList<String>();
	private List<Spent> spentTime = new ArrayList<Spent>();
	private List<String> deadlines = new ArrayList<String>();
	private List<String> requirements = new ArrayList<String>();
	private List<String> testDocs = new ArrayList<String>();
	private List<String> attachments = new ArrayList<String>();
	private List<Document> docs = new ArrayList<Document>();
	private List<String> watchers = new ArrayList<String>();
	private List<Request> linkedReqs = new ArrayList<Request>();

	// Constructor to create request object for already existed Request
	public Request(String id, String name, String type, String state,
			String priority) {
		this.id = id;
		this.name = name;
		this.priority = priority;
		this.type = type;
		this.state = state;
	}

	// Constructor to create very new request
	public Request(String name, String description, String priority,
			double estimation, String type) {
		this.name = name;
		this.description = description;
		this.priority = priority;
		this.estimation = estimation;
		this.type = type;
		this.state = "Добавлено";
	}

	// Constructor to quick create and set the fields automatically
	public Request(String name) {
		this.name = name;
		this.description = name + " description";
		this.priority = "Низкий";
		this.estimation = 10;
	}

	public static String getRandomPriority() {
		String[] select = new String[] { "Критично", "Высокий", "Обычный", "Низкий" };
		return select[new Random(System.currentTimeMillis()).nextInt(select.length - 1)];
	}

	public static String getHighPriority() {
		return "Высокий";
	}

	public static double getRandomEstimation() {
		return Double.valueOf((new Random(System.currentTimeMillis()).nextInt(10) + 10));
	}

	public static String getRandomLinkType() {
		String[] select = new String[] { "Дубликат", "Зависимость",
				"Блокируется", "Блокирует" };
		return select[new Random(System.currentTimeMillis()).nextInt(select.length - 1)];
	}

	public static String getRandomTestDoc() {
		return "Функциональное тестирование";
	}

	public int hashCode() {
		final int prime = 31;
		int result = 1;
		result = prime * result + ((id == null) ? 0 : id.hashCode());
		result = prime * result + ((name == null) ? 0 : name.hashCode());
		return result;
	}

	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		Request other = (Request) obj;
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

	@Override
	public String toString() {
		return "Request [id=" + id + ", name=" + name + ", priority="
				+ priority + ", type=" + type + ", state=" + state + "]";
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
	
	public String getNumericId() {
		return id.substring(2);
	}

	public String getDescription() {
		return description;
	}

	public void setDescription(String description) {
		this.description = description;
	}

	public String getPriority() {
		return priority;
	}

	public void setPriority(String priority) {
		this.priority = priority;
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

	public String getState() {
		return state;
	}

	public void setState(String state) {
		this.state = state;
	}

	public void addSpentTimeRecord(String date, double hours, String username,
			String description) {
		spentTime.add(new Spent(date, hours, username, description));
	}

	public void removeSpentTimeRecord(String date, double hours, String username,
			String description) {
		spentTime.remove(new Spent(date, hours, username, description));
	}

	public void clearSpentTimeRecords() {
		spentTime.clear();
	}

	public List<String> getTags() {
		return tags;
	}
	
	
	public void addTag(String tag) {
		tags.add(tag);
	}

	public void removeTag(String tag) {
		tags.remove(tag);
	}

	public void clearTags() {
		tags.clear();
	}

	public void addRequirements(String requirement) {
		requirements.add(requirement);
	}

	public void removeRequirements(String requirement) {
		requirements.remove(requirement);
	}

	public void clearRequirements() {
		requirements.clear();
	}

	public void addDeadline(String deadline) {
		deadlines.add(deadline);
	}

	public void removeDeadline(String deadline) {
		deadlines.remove(deadline);
	}

	public void clearDeadlines() {
		deadlines.clear();
	}

	public void addTestDocs(String testdoc) {
		testDocs.add(testdoc);
	}

	public void removeTestDocs(String testdoc) {
		testDocs.remove(testdoc);
	}

	public void clearTestDocs() {
		testDocs.clear();
	}

	public void addDocs(Document doc) {
		docs.add(doc);
	}

	public void removeDocs(Document doc) {
		docs.remove(doc);
	}

	public void clearDocs() {
		docs.clear();
	}

	public List<String> getWatchers() {
		return watchers;
	}
	
	public void addWatcher(String watcher) {
		watchers.add(watcher);
	}

	public void removeWatcher(String watcher) {
		watchers.remove(watcher);
	}

	public void clearWatchers() {
		watchers.clear();
	}

	public List<Request> getLinkedRequests() {
		return linkedReqs;
	}
	
	public void addlinkedReq(Request request) {
		linkedReqs.add(request);
	}

	public void removeWlinkedReq(Request request) {
		linkedReqs.remove(request);
	}

	public void clearlinkedReqs() {
		linkedReqs.clear();
	}

	public String getPfunction() {
		return pfunction;
	}

	public void setPfunction(String pfunction) {
		this.pfunction = pfunction;
	}

	public String getOriginator() {
		return originator;
	}

	public void setOriginator(String originator) {
		this.originator = originator;
	}

	public String getVersion() {
		return version;
	}

	public void setVersion(String version) {
		this.version = version;
	}

	public List<RTask> getTasks() {
		return tasks;
	}

	public void addTask(RTask task) {
		this.tasks.add(task);
	}
	
	public void removeTask(RTask task) {
		this.attachments.remove(task);
	}

	public int compareTo(Request o) {
		return this.id.hashCode() - o.id.hashCode();
	}

	public String getClosedVersion() {
		return closedVersion;
	}

	public void setClosedVersion(String closedInVersion) {
		this.closedVersion = closedInVersion;
	}

	public List<String> getAttachments() {
		return attachments;
	}

	public void addAttachments(String attachment) {
		this.attachments.add(attachment);
	}
	
	public void removeAttachments(String attachment) {
		this.attachments.remove(attachment);
	}

	public Request clone() {
        Request result=null;
		try {
			result = (Request)super.clone();
		} catch (CloneNotSupportedException e) {
			e.printStackTrace();
		}
		return result;
  }

	public String getRelease() {
		return release;
	}

	public void setRelease(String release) {
		this.release = release;
	}
	
}
