<?php

namespace App;

use Moloquent;
use Fadion\Bouncy\BouncyTrait;

class Autocomplete extends Moloquent
{
  use BouncyTrait;
  public $timestamps  = false;
  protected $collection = 'AutocompleteData';
  protected $indexName = 'autocomplete';
  protected $typeName = 'data';
}
