<?php

class BlogPostFileIterator extends OrderedIterator
{
    function getFileLink()
    {
        if ( $this->IsImage('File'))
        {
            return '<a class="image_attach" data-fancybox="gallery" href="'.$this->getFileUrl().'&.png" name="'.$this->getFileName('Content').'" ' .
                    'title="'.$this->get('Description').'"><img src="/images/image.png" style="margin-bottom:-4px;"> '.$this->getFileName('Content').'</a>';
        }
        else
        {
            return '<a href="'.$this->getFileUrl().'" name="'.$this->getFileName('Content').'" ' .
                    'title="'.$this->get('Description').'"><img src="/images/attach.png" style="margin-bottom:-4px;"> '.$this->getFileName('Content').'</a>';
        }
    }

    function getDisplayName()
    {
        return $this->getFileLink().' ('.$this->getFileSizeKb('Content').' Kb)';
    }
}
