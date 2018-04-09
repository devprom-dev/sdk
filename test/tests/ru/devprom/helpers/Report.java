package ru.devprom.helpers;

import java.io.File;

/**
 * Created with IntelliJ IDEA.
 * User: andrebrov
 * Date: 10.07.13
 * Time: 8:56
 * To change this template use File | Settings | File Templates.
 */
public class Report {
    private File image;

    public Report(File reportImage) {
        this.image = reportImage;
    }

    public File getImage() {
        return image;
    }

    public void setImage(File image) {
        this.image = image;
    }
}
