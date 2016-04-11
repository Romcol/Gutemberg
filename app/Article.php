<?php

namespace App;

use Moloquent;
use Fadion\Bouncy\BouncyTrait;

class Article extends Moloquent
{
  use BouncyTrait;
  protected $connection = 'mongodb';
  protected $collection = 'Articles';
  protected $indexName = 'articles';
  protected $typeName = 'json';
}
