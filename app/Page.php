<?php

namespace App;

use Moloquent;
use Fadion\Bouncy\BouncyTrait;

class Page extends Moloquent
{
  use BouncyTrait;
  protected $collection = 'Pages';
  protected $indexName = 'pages';
  protected $typeName = 'json';
}
