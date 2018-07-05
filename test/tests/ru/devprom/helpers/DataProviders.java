package ru.devprom.helpers;

import org.testng.annotations.DataProvider;

import java.io.*;
import java.util.Random;

//For future needs - Data Driven Tests
public class DataProviders {
	protected static final int UNIQUELENGTH = 10;

	public static String getUniqueString() {
		return getUniqueString(UNIQUELENGTH);
	}
	public static String getUniqueString(int digits) {
		if (digits > 13 || digits < 1) {
			digits = 13;
			System.out.println("Invalid digits number. Set to 13.");
		}
		Random r = new Random();
		return String.valueOf(System.currentTimeMillis() + r.nextInt())
				.substring(13 - digits, 13);
    }

    public static File createRandomTextFile() {
        Writer writer = null;
        try {
            File file = File.createTempFile("att", ".txt");
            writer = new BufferedWriter(new FileWriter(file));
            writer.write(getUniqueString(50));
            return file;
        } catch (IOException e) {
            throw new RuntimeException("Unable to create file", e);
        } finally {
            if (writer != null) {
                try { writer.close(); } catch (IOException e) { /* swallow */ }
            }
        }
	}

    	  @DataProvider(name = "sp1")
    	  public static Object[][] createData() {
    	    return new Object[][] {
    	      new String[] { "Scrum" , "Участник команды", "История" , "Доска историй", "История пользователя.Автор"},
    	      new String[] { "Waterfall" , "Аналитик", "Пожелание" , "Доска пожеланий", "Пожелание.Автор"}
    	  };
    	}
    
    
}
