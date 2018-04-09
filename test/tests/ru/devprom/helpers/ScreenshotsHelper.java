package ru.devprom.helpers;

import java.awt.Color;
import java.awt.Graphics2D;
import java.awt.Image;
import java.awt.image.BufferedImage;
import java.io.File;
import java.io.IOException;
import java.lang.reflect.Array;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.HashMap;
import java.util.HashSet;
import java.util.List;
import java.util.Map;
import java.util.Set;

import javax.imageio.ImageIO;
import javax.swing.ImageIcon;

import org.apache.commons.io.FileUtils;
import org.apache.log4j.LogManager;
import org.apache.log4j.Logger;
import org.openqa.selenium.Dimension;
import org.openqa.selenium.OutputType;
import org.openqa.selenium.Point;
import org.openqa.selenium.TakesScreenshot;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;

import ru.devprom.tests.TestBase;

public class ScreenshotsHelper {

	private static int screenshotcounter = 0;
	private static String reportsFolder;
    private static Map<Page, Set<Location>> data = new HashMap<Page, Set<Location>>();
    private static final Logger FILELOG = LogManager.getLogger("MAIN");

   static {
   	 SimpleDateFormat formatter = new SimpleDateFormat("yyyy-MM-dd-HH-mm-ss");
       String date =formatter.format(Calendar.getInstance().getTime());
   	reportsFolder  = Configuration.getReportFolder() + "-" + date;
   }
	
	
	 public static File takeScreenshotOnFail(WebDriver driver) {
		 return takeScreenshot(driver, "FAIL_"); 
	    }
	 
	 public static void takeScreenshotForInfo(WebDriver driver) {
		 takeScreenshot(driver, "INFO_");
	    }
	 
	 public static File takeScreenshot(WebDriver driver, String prefix){
			int c = 100000+screenshotcounter;
	    	screenshotcounter++;
	    	try {
	            File source = ((TakesScreenshot) driver).getScreenshotAs(OutputType.FILE);
	            String path = reportsFolder +"\\"+TestAndMethodListener.testClassName + "\\"+ prefix + TestAndMethodListener.testMethodName+ String.valueOf(c)+".png";
	            File destFile = new File(path);
	            new File(reportsFolder+"\\"+TestAndMethodListener.testClassName).mkdirs();
	            destFile.createNewFile();
	            FileUtils.copyFile(source, destFile);
	            return destFile;
	        } catch (IOException e) {
	            throw new RuntimeException(e);
	        }
	 }

	 public static void takeScreenshotWithHighlightElement(WebDriver driver,  WebElement element, String prefix){
		 int c = 100000+screenshotcounter;
	    	screenshotcounter++;
	    	try {
	            File source = ((TakesScreenshot) driver).getScreenshotAs(OutputType.FILE);
	            String path = reportsFolder +"\\"+TestAndMethodListener.testClassName + "\\"+ prefix + TestAndMethodListener.testMethodName+ String.valueOf(c)+".png";
	            File destFile = new File(path);
	            new File(reportsFolder+"\\"+TestAndMethodListener.testClassName).mkdirs();
	            destFile.createNewFile();
	            FileUtils.copyFile(source, destFile);
	            extractElementLocationAndSave (new Page(destFile), element);
	        } catch (IOException e) {
	            throw new RuntimeException(e);
	        }
	 }
	 
	 
	    private static void extractElementLocationAndSave(Page page, WebElement element) {
	        Location location = new Location();
	        Point topLeftPoint = element.getLocation();
	        location.setTopLeftPoint(topLeftPoint);
	        Dimension size = element.getSize();
	        location.setDimension(size);
	        Set<Location> locationSet = data.get(page);
	        if (locationSet == null) {
	            locationSet = new HashSet<Location>();
	        }
	        locationSet.add(location);
	        data.put(page, locationSet);
	    }

	    public static List<Report> getReports() {
	        List<Report> reports = new ArrayList<Report>();
	        for (Page page : data.keySet()) {
	            Set<Location> locations = data.get(page);
	            try {
	                Image pageScreenShot = ImageIO.read(page.getScreenshot());
	                ImageIcon img = new ImageIcon(pageScreenShot);
	                BufferedImage bi = new BufferedImage(img.getIconWidth(), img.getIconHeight(), BufferedImage.TYPE_INT_ARGB);
	                Graphics2D ig2 = bi.createGraphics();
	                ig2.drawImage(ImageIO.read(page.getScreenshot()), null, 0, 0);
	                ig2.setPaint(Color.yellow);
	                for (Location location : locations) {
	                    Point topLeftPoint = location.getTopLeftPoint();
	                    Dimension dimension = location.getDimension();
	                    ig2.drawRect(topLeftPoint.getX(), topLeftPoint.getY(),
	                            dimension.getWidth(), dimension.getHeight());
	                    ig2.fillRect(topLeftPoint.getX(), topLeftPoint.getY(),
	                            dimension.getWidth(), dimension.getHeight());
	                }
	                File reportImage = new File(page.getScreenshot().getParent()+"\\"+ "Report_" + page.getScreenshot().getName());
	                ImageIO.write(bi, "PNG", reportImage);
	                page.getScreenshot().delete();
	                reports.add(new Report(reportImage));
	            } catch (IOException e) {
	                throw new RuntimeException(e);
	            }
	        }
	        return reports;
	    }
	    
}
