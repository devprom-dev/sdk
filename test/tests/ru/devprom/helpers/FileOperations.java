package ru.devprom.helpers;

import java.awt.AWTException;
import java.awt.Color;
import java.awt.Graphics2D;
import java.awt.Robot;
import java.awt.event.KeyEvent;
import java.awt.image.BufferedImage;
import java.io.File;
import java.io.FileWriter;
import java.io.IOException;
import java.io.PrintWriter;

import javax.imageio.ImageIO;

public class FileOperations {

	private static final String PATH = Configuration.getDownloadPath() + "\\";

	public static void main (String[] args) {
		createTxt(Configuration.getWorkingCopy()+"//testfile.txt", "have a happy day dear");
	}
	
	
	
	public static File downloadFile(final String FILENAME) {
				switch (Configuration.getBrowser()) {
		case "ie":
			try {
				Robot robot = new Robot();
				robot.delay(3000);
				robot.keyPress(KeyEvent.VK_ENTER);
				robot.keyRelease(KeyEvent.VK_ENTER);
				robot.delay(3000);
				robot.keyPress(KeyEvent.VK_ENTER);
				robot.keyRelease(KeyEvent.VK_ENTER);
				robot.delay(2000);
			} catch (AWTException e) {
				e.printStackTrace();
			}
			break;
		case "firefox":
			try {
				Robot robot = new Robot();
				robot.delay(5000);
			} catch (AWTException e) {
				e.printStackTrace();
			}
			break;

		case "chrome":

			try {
				Robot robot = new Robot();
				robot.delay(3000);
				robot.keyPress(KeyEvent.VK_ENTER);
				robot.keyRelease(KeyEvent.VK_ENTER);
				robot.delay(2000);
			} catch (AWTException e) {
				e.printStackTrace();
			}
			break;

		default:
			try {
				Robot robot = new Robot();
				robot.delay(3000);
				robot.keyPress(KeyEvent.VK_ENTER);
				robot.keyRelease(KeyEvent.VK_ENTER);
				robot.delay(3000);
				robot.keyPress(KeyEvent.VK_ENTER);
				robot.keyRelease(KeyEvent.VK_ENTER);
				robot.delay(2000);
			} catch (AWTException e) {
				e.printStackTrace();
			}
		}
		return new File(PATH + FILENAME);

	}

	public static void removeExisted(final String FILENAME) {
		File f = new File(PATH + FILENAME);
		f.delete();
	}
	

	   public static File createPNG(String name){
		   File image = new File(PATH+name);
		   BufferedImage img = new BufferedImage(400, 300, 1);
		   Graphics2D g = img.createGraphics();
		   g.setColor(Color.LIGHT_GRAY);
		   g.fillRect(0, 0, img.getWidth(), img.getHeight());
		   try {
			ImageIO.write(img,"png", image);
		} catch (IOException e) {
			e.printStackTrace();
		}
		   return image;
	    }
		
	   
	   public static File createTxt(String name, String content){
		   File file = new File(name);
		   try {
			file.createNewFile();
		    } catch (IOException e) {
			e.printStackTrace();
			return null;
	    	}
			   FileWriter fw = null;
			try {
				fw = new FileWriter(file.getPath(),false);
				PrintWriter	outputWriter = new PrintWriter(fw);
				outputWriter.print(content);
			    outputWriter.close();
			} catch (IOException e) {
				e.printStackTrace();
			}
		   return file;		   
	   }
	   
	   public static File editTxt(String name, String newContent){
		   File file = new File(name);
		   try {
			   FileWriter fw = new FileWriter(file.getPath(),false);
				PrintWriter	outputWriter = new PrintWriter(fw);
				outputWriter.print(newContent);
			    outputWriter.close();
			} catch (IOException e) {
				System.out.println("Error opening file");
				e.printStackTrace();
			}
		   return file;		
	   }
	   
	   public static void deleteDirectory(File dir) {
			if (dir.isDirectory()) {
				String[] children = dir.list();
				for (int i = 0; i < children.length; i++) {
					File f = new File(dir, children[i]);
					deleteDirectory(f);
				}
				dir.delete();
			} else
				dir.delete();
		}
	   
	   
	   
	   public static void clearAttributes(File dir) {
			if (dir.isDirectory()) {
				String[] children = dir.list();
				for (int i = 0; i < children.length; i++) {
					File f = new File(dir, children[i]);
					f.setReadable(true);
					f.setWritable(true);
					clearAttributes(f);
				}
			} else
				dir.setReadable(true);
			    dir.setWritable(true);
		}
}
