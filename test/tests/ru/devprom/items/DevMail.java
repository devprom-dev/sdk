package ru.devprom.items;

import ru.devprom.helpers.Configuration;

/** Simple email message. Uses server authentication. No multiple recipients, no CC, no multipart body. 
 *  Gets mailserver from config.properties. Port 25.  */
public class DevMail {
	private String mailserver;
	private String to;
	private String from;
	private String user;
	private String password;
	private String auth; 
	private String port;
	private String subject;
	private String body;
	
	public DevMail(String to, String user, String password, String subject, String body){
		this.mailserver = Configuration.getMailserver();
		this.to = to;
		this.user = user;
		this.password = password;
		this.from = user;
		this.auth="true";
		this.port="25";
		this.subject=subject;
		this.body=body;
	}

	@Override
	public String toString() {
		return "Email [mailserver=" + mailserver + ", to=" + to
				+ ", from=" + from + ", user=" + user + ", password="
				+ password + ", auth=" + auth + ", port=" + port
				+ ", subject=" + subject + ", body=" + body + "]";
	}


	public String getMailserver() {
		return mailserver;
	}

	public void setMailserver(String mailserver) {
		this.mailserver = mailserver;
	}

	public String getTo() {
		return to;
	}

	public void setTo(String to) {
		this.to = to;
	}

	public String getFrom() {
		return from;
	}

	public void setFrom(String from) {
		this.from = from;
	}

	public String getUser() {
		return user;
	}

	public void setUser(String user) {
		this.user = user;
	}

	public String getPassword() {
		return password;
	}

	public void setPassword(String password) {
		this.password = password;
	}

	public String getAuth() {
		return auth;
	}

	public void setAuth(String auth) {
		this.auth = auth;
	}

	public String getPort() {
		return port;
	}

	public void setPort(String port) {
		this.port = port;
	}

	public String getSubject() {
		return subject;
	}

	public void setSubject(String subject) {
		this.subject = subject;
	}

	public String getBody() {
		return body;
	}

	public void setBody(String body) {
		this.body = body;
	}
	
}
