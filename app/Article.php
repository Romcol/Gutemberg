<?php

namespace App;

use Moloquent;
use Elasticquent\ElasticquentTrait;

class Article extends Moloquent
{
	use ElasticquentTrait;
	protected $collection = 'article';
}
