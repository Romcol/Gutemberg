<?php

namespace App;

use Moloquent;
use Elasticquent\ElasticquentTrait;

class Article extends Moloquent
{
	use ElasticquentTrait;
	protected $collection = 'article';
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
