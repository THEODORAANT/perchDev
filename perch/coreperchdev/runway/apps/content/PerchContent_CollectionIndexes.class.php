<?php

class PerchContent_CollectionIndexes extends PerchFactory
{
    protected $singular_classname = 'PerchContent_CollectionIndex';
    protected $table              = 'collection_indexes';
    protected $pk                 = 'indexID';

    protected $default_sort_column = 'indexCreated';

}
