<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Elasticquent\ElasticquentTrait;

class Dispensary extends Model
{
    //
    use ElasticquentTrait;

    function getTypeName()
	{
	    return 'dispensary';
	}
}
