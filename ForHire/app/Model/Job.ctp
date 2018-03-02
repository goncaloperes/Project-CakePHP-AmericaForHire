<?php

class Job extends AppModel {

    public $name = 'Job';
    public $belongsTo = array('Type', 'Category');

}