package ru.devprom.helpers;

import java.io.BufferedWriter;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.FileWriter;
import java.io.IOException;
import java.util.zip.ZipEntry;
import java.util.zip.ZipOutputStream;

public class CreateZip {
	public static String dirPath = "resources/devprom";
	public static String zipPath = "resources/devprom.zip";
	public static String currentVersion;

	public static File makeZip(String oldVersion) throws IOException {
		makeTxt(updateVersion(oldVersion));
		return zipDir(dirPath, zipPath);
	};

	public static File makeZipR(String oldVersion) throws IOException {
		makeTxt(updateVersion(oldVersion));
		makeRequiredTxt();
		return zipDir(dirPath, zipPath);
	};

	private static void makeTxt(String newVersion) throws IOException {

		File txtFile = new File(dirPath);
		txtFile.mkdir();
		txtFile = new File(dirPath + "/devprom");
		txtFile.mkdir();
		txtFile = new File(dirPath + "/devprom/version.txt");
		txtFile.delete();
		txtFile.createNewFile();

		FileWriter fw = new FileWriter(txtFile);
		BufferedWriter buff = new BufferedWriter(fw);
		buff.write(newVersion);
		buff.close();
		fw.close();

	}

	private static void makeRequiredTxt() throws IOException {

		File txtFile = new File(dirPath + "/devprom/required.txt");
		txtFile.delete();
		txtFile.createNewFile();
		FileWriter fw = new FileWriter(txtFile);
		BufferedWriter buff = new BufferedWriter(fw);
		buff.write("5.0,5.0.1");
		buff.close();
		fw.close();

	}

	private static String updateVersion(String oldVersion) {
		String[] subStrings = oldVersion.split("\\.");
		int buildNumber = Integer.parseInt(subStrings[subStrings.length - 1]);
		buildNumber++;
		// set global variable for current version
		currentVersion = subStrings[0];
		for (int i = 1; i < subStrings.length; i++) {
			if (i == subStrings.length - 1)
				currentVersion = currentVersion + "." + buildNumber;
			else
				currentVersion = currentVersion + "." + subStrings[i];
		}

		return currentVersion;
	}

	private static File zipDir(String dirName, String zipName)
			throws IOException {

		ZipOutputStream zos = new ZipOutputStream(new FileOutputStream(zipName));
		recToZip(dirName, zos, dirName);
		zos.close();
		deleteDirectory(new File(dirPath));
		return new File(zipName);

	}

	private static void recToZip(String dir2zip, ZipOutputStream zos,
			String True_path) throws IOException {
		try {
			File zipDirs = new File(dir2zip);
			String[] dirList = zipDirs.list();
			byte[] readBuffer = new byte[1024];
			int bytesIn = 0;
			for (int i = 0; i < dirList.length; i++) {
				File f = new File(zipDirs, dirList[i]);
				if (f.isDirectory()) {
					String filePath = f.getPath();
					recToZip(filePath, zos, True_path);
					continue;
				}
				FileInputStream fis = new FileInputStream(f);
				ZipEntry anEntry = new ZipEntry(f.getPath().substring(
						True_path.length()));
				zos.putNextEntry(anEntry);
				while ((bytesIn = fis.read(readBuffer)) != -1) {
					zos.write(readBuffer, 0, bytesIn);
				}
				fis.close();
			}
		} catch (Exception e) {
			System.out
					.println("Can't make zip archieve. Exit the application.");
			throw new IOException();
		}
	}

	private static void deleteDirectory(File dir) {
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
}
