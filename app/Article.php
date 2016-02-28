<?php

namespace App;

use Moloquent;
use Fadion\Bouncy\BouncyTrait;

class Article extends Moloquent
{
  use BouncyTrait;
	protected $collection = 'Articles';
  protected $indexName = 'articles';
  protected $typeName = 'json';
	//public $fillable = ['titre', 'date', 'titrejournal','auteur'];

	/*protected $mappingProperties = array(
    'date' => [
      'type' => 'string',
      'analyzer' => 'standard'
    ],
    'titre' => [
      'type' => 'string',
      'analyzer' => 'standard'
    ],
    'titrejournal' => [
      'type' => 'string',
      'analyzer' => 'standard'
    ],
    'auteur' => [
      'type' => 'string',
      'analyzer' => 'standard'
    ],
  );*/

}
