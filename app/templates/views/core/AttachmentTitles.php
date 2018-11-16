<?php
foreach( $files as $file )
{
    $url = $file['type'] == 'image' ? $file['url'].'&.png' : $file['url'];
    $title = $file['name']." (".$file['size']." KB)";

    if ( $file['type'] == 'image' ) {
    ?>
        <span>
            <a class="image_attach" data-fancybox="gallery" href="<?=$url?>" title="<?=$title?>">
                <img src="/images/image.png"> <?=$title?>
            </a>
        </span>
    <?php
    }
    else {
        ?>
        <span>
            <a class="file_attach" href="<?=$url?>" title="<?=$title?>">
                <img src="/images/attach.png"> <?=$title?>
            </a>
        </span>
        <?php
    }
}