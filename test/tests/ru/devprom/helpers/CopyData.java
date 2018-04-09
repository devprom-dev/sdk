/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package ru.devprom.helpers;

import java.awt.Image;
import java.awt.Toolkit;
import java.awt.datatransfer.Clipboard;
import java.awt.datatransfer.StringSelection;
import java.io.File;
import java.io.IOException;
import javax.imageio.ImageIO;

/**
 *
 * @author лена
 */
public class CopyData {
    
    public CopyData(){
        
    }
    public void copyText(String text){
       StringSelection stringSelection = new StringSelection(text);
        Clipboard clipboard = Toolkit.getDefaultToolkit().getSystemClipboard();
        clipboard.setContents(stringSelection, null); 
    }
    
    public void copyImage(String pathToImage) throws IOException{
        Image image = ImageIO.read(new File(pathToImage));
         ImageTransferable imageTransferable = new ImageTransferable(image);
         Clipboard clipboard = Toolkit.getDefaultToolkit().getSystemClipboard();
        clipboard.setContents(imageTransferable, null);
    }
}
