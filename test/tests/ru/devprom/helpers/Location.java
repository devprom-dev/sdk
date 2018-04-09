package ru.devprom.helpers;

import org.openqa.selenium.Dimension;
import org.openqa.selenium.Point;

/**
 * Created with IntelliJ IDEA.
 * User: andrebrov
 * Date: 06.03.13
 * Time: 10:02
 * To change this template use File | Settings | File Templates.
 */
public class Location {
    private Point topLeftPoint;
    private Dimension dimension;

    public Point getTopLeftPoint() {
        return topLeftPoint;
    }

    public Dimension getDimension() {
        return dimension;
    }

    public void setTopLeftPoint(Point topLeftPoint) {
        this.topLeftPoint = topLeftPoint;
    }

    public void setDimension(Dimension dimension) {
        this.dimension = dimension;
    }

    @Override
    public boolean equals(Object o) {
        if (this == o) return true;
        if (!(o instanceof Location)) return false;

        Location location = (Location) o;

        if (!dimension.equals(location.dimension)) return false;
        if (!topLeftPoint.equals(location.topLeftPoint)) return false;

        return true;
    }

    @Override
    public int hashCode() {
        int result = topLeftPoint.hashCode();
        result = 31 * result + dimension.hashCode();
        return result;
    }
}
