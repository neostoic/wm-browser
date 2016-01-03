<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class APISearchController extends BaseController
{
    //
    public function list()
    {
    	$in = $this->input();
    	if (!empty($this->payload['lat']) && !empty($this->payload['long']))
    	{
    		return $this->bomb('Error', 'Missing Parameters: lat(double), long(double)', 400)
    	}

		$post = [
    		'sort' => [
    			[
    				'package_level_raw' => [
    					"order"		=> "desc",
    					"missing"	=> "_last"
					],
    			],
    			[
    				'feature_order' => [
    					"order"		=> "asc",
    					"missing"	=> "_last"
    				],
    			],
    			[
    				"ranking" => [
    					"order"		=> "desc",
    					"missing"	=> "_last"	
    				],
    			],
    		],
    		"query" =>	[
    			"filtered" =>	[
    				"filter" =>	[
    					"and" =>	[
    						[
    							"geo_bounding_box" => [
    								"lat_lon" => [
    									'top_right' => [
    										"lat"	=> 32.884110,
    										"lon"	=> -116.712462,
    									],
    									'bottom_left' => [
    										'lat'	=> 32.541069,
    										'lon'	=> -117.123040,
    									],
    								],
    							],
    						],
							[
								'term'	=> [
									'published'	=> true,
								],
							],
    					],
    				],
    			],
    		],
    	];

    	$postJSON = json_encode($post);

    	// create a new cURL resource
		$ch = curl_init();

		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, "https://search-prod.weedmaps.com:9201/weedmaps/_search");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_POST);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postJSON);
		curl_setopt($ch, CURLOPT_HTTPHEADER, 'Content-Type: application/json');

		// grab URL and pass it to the browser
		$results = curl_exec($ch);

		// close cURL resource, and free up system resources
		curl_close($ch);


		$results = json_decode($results);

		dd($results);
    }
}
