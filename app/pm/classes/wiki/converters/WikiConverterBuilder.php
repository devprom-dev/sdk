<?php

abstract class WikiConverterBuilder
{
    abstract public function build( WikiConverterRegistry $registry, Metaobject $page );
}