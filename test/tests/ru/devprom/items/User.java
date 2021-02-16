package ru.devprom.items;

import java.util.HashSet;
import java.util.Set;

public class User implements Comparable<User> {
	private String username;
	private String pass;
	private String usernameLong;
	private String email;
	public Boolean isAdmin;
	public int id;

	public enum Lang {
		russian, english
	};

	private Lang language;
	private String contacts;
	private String description;
	public Set<String> groups = new HashSet<String>();
	public Boolean isValid;

	public User(String postfix, Boolean isValid) {
		this.username = "Test" + postfix;
		this.pass = "Test" + postfix;
		this.usernameLong = "Test" + postfix + "username";
		this.email = "Test" + postfix + "@mail.com";
		this.isAdmin = false;
		this.contacts = "";
		this.description = "";
		this.language = Lang.russian;
		this.isValid = isValid;
	}

	public User(String username, String pass, String usernameLong,
			String email, Boolean isAdmin, Boolean isValid) {
		this.username = username;
		this.pass = pass;
		this.usernameLong = usernameLong;
		this.email = email;
		this.isAdmin = isAdmin;
		this.contacts = "";
		this.description = "";
		this.language = Lang.russian;
		this.isValid = isValid;

	}

	public User(String username, String pass, String usernameLong,
			String email, Boolean isAdmin, Boolean isRussian, String contacts,
			String description, String[] groups, Boolean isValid) {
		this.username = username;
		this.pass = pass;
		this.usernameLong = usernameLong;
		this.email = email;
		this.isAdmin = isAdmin;
		this.contacts = contacts;
		this.description = description;
		if (!groups.equals(null)) {
			for (String group : groups) {
				this.groups.add(group);
			}
		}
		if (isRussian)
			this.language = Lang.russian;
		else
			this.language = Lang.english;
		this.isValid = isValid;

	}

	@Override
	public int hashCode() {
		final int prime = 31;
		int result = 1;
		result = prime * result + ((email == null) ? 0 : email.hashCode());
		result = prime * result
				+ ((usernameLong == null) ? 0 : usernameLong.hashCode());
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
		User other = (User) obj;
		if (email == null) {
			if (other.email != null)
				return false;
		} else if (!email.equals(other.email))
			return false;
		if (usernameLong == null) {
			if (other.usernameLong != null)
				return false;
		} else if (!usernameLong.equals(other.usernameLong))
			return false;
		return true;
	}

	@Override
	public String toString() {
		return "User [username=" + username + ", usernameLong=" + usernameLong
				+ ", email=" + email + "]";
	}

	public String getUsername() {
		return username;
	}

	public void setUsername(String username) {
		this.username = username;
	}

	public String getPass() {
		return pass;
	}

	public void setPass(String pass) {
		this.pass = pass;
	}

	public String getUsernameLong() {
		return usernameLong;
	}

	public void setUsernameLong(String usernameLong) {
		this.usernameLong = usernameLong;
	}

	public String getEmail() {
		return email;
	}

	public void setEmail(String email) {
		this.email = email;
	}

	public Lang getLanguage() {
		return language;
	}

	public void setLanguage(Lang language) {
		this.language = language;
	}

	public String getContacts() {
		return contacts;
	}

	public void setContacts(String contacts) {
		this.contacts = contacts;
	}

	public String getDescription() {
		return description;
	}

	public void setDescription(String description) {
		this.description = description;
	}

	public void addGroup(String group) {
		this.groups.add(group);
	}

	public void removeGroup(String group) {
		this.groups.remove(group);
	}

	public Boolean isInGroup(String group) {
		if (this.groups.contains(group))
			return true;
		else
			return false;
	}

	public String[] getGroups() {
		return this.groups.toArray(new String[0]);
	}

	public void removeGroups(String[] groups) {
		for (String group : groups) {
			if (this.groups.contains(group)) {
				this.groups.remove(group);
			}
		}
	}

	public void setGroups(String[] groups) {
		this.groups.clear();
		if (!groups.equals(null)) {
			for (String group : groups) {
				this.groups.add(group);
			}
		}
	}

	@Override
	public int compareTo(User o) {

		return this.hashCode() - o.hashCode();
	}

	public boolean isLike(User user) {
		if (this.username.equals(user.getUsername())
				&& this.usernameLong.equals(user.getUsernameLong())
				&& this.email.equals(user.getEmail())
				&& this.groups.containsAll(user.groups)
				&& user.groups.containsAll(this.groups)
				&& this.language.equals(user.getLanguage())
				&& this.isAdmin == user.isAdmin)
			return true;
		else
			return false;
	}

}
