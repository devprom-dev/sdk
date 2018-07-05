package ru.devprom.helpers;

import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.util.Properties;

import javax.mail.Address;
import javax.mail.BodyPart;
import javax.mail.Message;
import javax.mail.MessagingException;
import javax.mail.Session;
import javax.mail.Transport;
import javax.mail.internet.InternetAddress;
import javax.mail.internet.MimeBodyPart;
import javax.mail.internet.MimeMessage;

import org.apache.log4j.LogManager;
import org.apache.log4j.Logger;

import ru.devprom.items.DevMail;


public class MailHelper {

	public static Properties mailProps = new Properties();
	protected static final Logger FILELOG = LogManager.getLogger("MAIN");
	
	
	//test sendmail
	public static void main(String[] args) {
		
		try {
			Configuration.readConfig();
		} catch (IOException e1) {
			e1.printStackTrace();
		}
		
			send (new DevMail("user@localhost", "test", "test", "DevpromTestMessage", "blablabla"));
	}
	
	
	
	
	/**Sends a simple text message to a single recipient */
	    public static void send(DevMail email)  {
	    	     		  	
	    		    mailProps.put("mail.smtp.auth", email.getAuth());
	    	        mailProps.put("mail.smtp.host", email.getMailserver());
	    	        mailProps.put("mail.smtp.port", email.getPort());
	    	        mailProps.put("mail.smtp.user" , email.getUser());
	    	        mailProps.put("mail.smtp.password",  email.getPassword());
	    	        mailProps.put("mail.to", email.getTo());
	    	        mailProps.put("mail.from", email.getFrom());
	    	        
	    		    	 	       
	        // Get the default Session object.
	        Session session = Session.getDefaultInstance(mailProps);
     //       System.out.println(session.getProperties());
	        try{
	           // Create a default MimeMessage object.
	           MimeMessage message = new MimeMessage(session);

	           // Set From: header field of the header.
	           message.setFrom(new InternetAddress(email.getFrom()));

	           // Set To: header field of the header.
	           message.addRecipient(Message.RecipientType.TO,
	                                    new InternetAddress(email.getTo()));
	       
	           // Set Subject: header field
	           message.setSubject(email.getSubject());
	       
	        // Set ReplyTo field   
	       try {
	        	   Address[] addresses={new InternetAddress(email.getFrom(),email.getUser())};
	        	   message.setReplyTo(addresses);
	           } catch (UnsupportedEncodingException e1)   {
			e1.printStackTrace();
			}
	      //    System.out.println(message.getReplyTo());

	          // Create the message part 
	           BodyPart messageBodyPart = new MimeBodyPart();

	           // Fill the message
	           messageBodyPart.setText(email.getBody());
	           
	          // Send the complete message parts
	           try {
				message.setContent(messageBodyPart.getContent(), "text/html; charset=utf-8");
		    	} catch (IOException e) {
				e.printStackTrace();
		     	}

	           // Send message
	           Transport tr = session.getTransport("smtp");
	           tr.connect(email.getMailserver(), Integer.parseInt(email.getPort()) , email.getUser(), email.getPassword());
	           message.saveChanges();
	           tr.sendMessage(message, message.getAllRecipients());
	           tr.close();
	           FILELOG.info("Sent message successfully!  "+email);
	       
	        }catch (MessagingException mex) {
	           mex.printStackTrace();
	        }
	     
	    }
	    
}   