package ru.devprom.helpers;

import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Locale;

public class DateHelper {

	private static Date incDate(long days){
		return new Date(new Date().getTime() + days*86400000);
	}
	
	private static Date decDate(long days){
		return new Date(new Date().getTime() - days*86400000);
	}
		
	public static String getCurrentDate(){
		return (new SimpleDateFormat("dd.MM.yyyy", Locale.UK)).format(new Date().getTime());
	}
	
	public static String getDayAfter(long days){
		return (new SimpleDateFormat("dd.MM.yyyy", Locale.UK)).format(incDate(days));
	}
	
	public static String getDayBefore(long days){
		return (new SimpleDateFormat("dd.MM.yyyy", Locale.UK)).format(decDate(days));
	}
	
	
	
}
