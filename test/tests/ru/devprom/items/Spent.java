package ru.devprom.items;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Locale;

import ru.devprom.helpers.DateHelper;

public class Spent {
	public String date;
	public String username;
	public double hours;
	public String description;

	public Spent(String date, double hours, String username, String description) {
		if (date.equals(""))
			this.date = DateHelper.getCurrentDate();
		else
			this.date = date;
		this.username = username;
		this.hours = hours;
		this.description = description;
	}

	public int hashCode() {
		final int prime = 31;
		int result = 1;
		result = prime * result + ((date == null) ? 0 : date.hashCode());
		result = prime * result
				+ ((description == null) ? 0 : description.hashCode());
		result = (int) (prime * result + hours);
		result = prime * result
				+ ((username == null) ? 0 : username.hashCode());
		return result;
	}

	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		Spent other = (Spent) obj;
		if (date == null) {
			if (other.date != null)
				return false;
		} else if (!date.equals(other.date))
			return false;
		if (hours != other.hours)
			return false;
		return true;
	}

	@Override
	public String toString() {
		return "Spent [date=" + date + ", username=" + username + ", hours="
				+ hours + ", description=" + description + "]";
	}

}
