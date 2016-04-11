<?php

namespace App;

use Moloquent;
use Fadion\Bouncy\BouncyTrait;

class Article extends Moloquent
{
  use BouncyTrait;

  public $timestamps  = false;
  protected $collection = 'Articles';
  protected $indexName = 'articles';
  protected $typeName = 'json';
}
