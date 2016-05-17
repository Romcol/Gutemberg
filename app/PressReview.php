<?php

namespace App;

use Moloquent;
use Fadion\Bouncy\BouncyTrait;

class PressReview extends Moloquent
{
  use BouncyTrait;

  protected $fillable = ['name', 'description', 'owner_id', 'owner_name', 'articles'];

  public $timestamps  = false;
  protected $collection = 'PressReviews';
  protected $indexName = 'pressreviews';
  protected $typeName = 'json';
}

?>