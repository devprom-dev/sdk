<?php

abstract class WikiImporterBuilder
{
    abstract public function build( WikiImporterRegistry $registry, Metaobject $page );
}