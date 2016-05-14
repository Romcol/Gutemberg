<?php

namespace App;

use Moloquent;
use Fadion\Bouncy\BouncyTrait;

class Autocomplete extends Moloquent
{
  use BouncyTrait;
  protected $collection = 'AutocompleteData';
  protected $indexName = 'autocomplete';
  protected $typeName = 'data';
}
