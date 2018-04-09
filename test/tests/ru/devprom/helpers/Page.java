package ru.devprom.helpers;

import java.io.File;

/**
 * Created with IntelliJ IDEA.
 * User: andrebrov
 * Date: 10.07.13
 * Time: 8:27
 * To change this template use File | Settings | File Templates.
 */
public class Page {
    private File screenshot;

    
    public Page(File screenshot){
    	   this.screenshot = screenshot;
    }
    
    public File getScreenshot() {
        return screenshot;
    }

    public void setScreenshot(File screenshot) {
        this.screenshot = screenshot;
    }

    @Override
    public boolean equals(Object o) {
        if (this == o) return true;
        if (!(o instanceof Page)) return false;

        Page page = (Page) o;

        if (!screenshot.equals(page.screenshot)) return false;

        return true;
    }

    @Override
    public int hashCode() {
        return screenshot.hashCode();
    }
}
