package ru.devprom.helpers;

import java.io.BufferedReader;
import java.io.File;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.List;

public class SCMHelper {
	static final String svnProcess = "svn.exe";
	
	public static void main(String[] args){
		
	 /*List<String> ss =	getFilesList(Configuration.getSVNUrl(), "admin", "admin");
	 System.out.println("Result:");
		for (String s:ss){
			System.out.println(s);
		}*/
		
		File txtFile = FileOperations.createTxt(Configuration.getWorkingCopy()+"//FileToBeCommited"+DataProviders.getUniqueString()+".txt", "Some content");
		addFile(txtFile.getAbsolutePath(), "admin", "admin");
		System.out.println(commitFile(txtFile.getAbsolutePath(), "I-41 #resolve #time 1h #comment SCM Test commit", "admin", "admin"));
	}
	
	
	
	public static List<String> getFilesList(String repositoryURL, String user, String password){
		ProcessBuilder processBuilder = new ProcessBuilder(svnProcess, "list", repositoryURL,"--username="+user,"--password="+password);
		String output;
		List<String> result = new ArrayList<String>();
		try {
			Process process = processBuilder.start();
		    InputStream readOutput = process.getInputStream();
		    InputStreamReader reader = new InputStreamReader(readOutput);
			BufferedReader buf = new BufferedReader(reader);
			
			while (true) {
				output = buf.readLine();
				if (output==null) break;
				result.add(output);
			}
		} catch (IOException e) {
			e.printStackTrace();
		}
		
		/*System.out.println("Result:");
		for (String s:result){
			System.out.println(s);
		}*/
		return result;
	}
	
	public static void setWorkingCopy(String repositoryUrl, String workingCopyPath, String user, String password){
		
		File workingCopy = new File(workingCopyPath);
		if (workingCopy.exists()) {
			FileOperations.deleteDirectory(workingCopy);
		}
		workingCopy.mkdir();
		
		if (!isWorkingCopy(workingCopyPath)){
		
		ProcessBuilder processBuilder = new ProcessBuilder(svnProcess, "checkout", repositoryUrl, workingCopyPath, "--username="+user,"--password="+password);
		try {
			Process process = processBuilder.start();
		    InputStream readOutput = process.getInputStream();
		    InputStreamReader reader = new InputStreamReader(readOutput);
			BufferedReader buf = new BufferedReader(reader);
			
			while (buf.readLine()!=null) {
				buf.readLine();
			}
		} catch (IOException e) {
			e.printStackTrace();
		}
	  }
	}



	public static boolean isWorkingCopy(String workingCopyPath) {
		
		ProcessBuilder processBuilder = new ProcessBuilder(svnProcess, "info", workingCopyPath);
		
		try {
			Process process = processBuilder.start();
		    InputStream readOutput = process.getInputStream();
		    InputStreamReader reader = new InputStreamReader(readOutput);
			BufferedReader buf = new BufferedReader(reader);
			
			String output;
			while (true) {
				output = buf.readLine();
				if (output == null) return false;
				if (output.contains("Repository Root")) return true;
			}
		} catch (IOException e) {
			e.printStackTrace();
		}
		return false;
	}
	
	
	public static void addFile(String fullFileName, String user, String password){
		ProcessBuilder processBuilder = new ProcessBuilder(svnProcess, "add", fullFileName,"--username="+user,"--password="+password);
		try {
			Process process = processBuilder.start();
			process.waitFor();
		} catch (IOException | InterruptedException e) {
			e.printStackTrace();
		}
	}
	
	
	public static String commitFile(String fullFileName, String comment, String user, String password){
		ProcessBuilder processBuilder = new ProcessBuilder(svnProcess, "commit", fullFileName, "-m "+comment,"--username="+user,"--password="+password);
		String output;
		String revisionNumber = "0";
		try {
			Process process = processBuilder.start();
		    InputStream readOutput = process.getInputStream();
		    InputStreamReader reader = new InputStreamReader(readOutput);
			BufferedReader buf = new BufferedReader(reader);
			
			while (true) {
				output = buf.readLine();
				if (output==null) break;
				if (output.contains("Committed revision")) {
					String[] ss = output.split(" ");
					revisionNumber = ss[2].replace(".", "");
				}
			}
		} catch (IOException e) {
			e.printStackTrace();
		}
		return revisionNumber;
	}

	
		   public static void executeDOSCommand() {
		      final String dosCommand = "cmd /c dir /s";
		      final String location = "C:\\WINDOWS";
		      try {
		         final Process process = Runtime.getRuntime().exec(
		            dosCommand + " " + location);
		         final InputStream in = process.getInputStream();
		         int ch;
		         while((ch = in.read()) != -1) {
		            System.out.print((char)ch);
		         }
		      } catch (IOException e) {
		         e.printStackTrace();
		      }
		   }
}
