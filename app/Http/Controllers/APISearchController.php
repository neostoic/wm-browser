<?php

namespace App\Http\Controllers;

class APISearchController extends BaseController
{
    //
    public function index()
    {
    	$in = $this->input();
    	if (!empty($this->payload['lat']) && !empty($this->payload['long']))
    	{
    		return $this->bomb('Error', 'Missing Parameters: lat(double), long(double)', 400);
    	}


		$query = [
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

    	$results = \App\Weedmaps::searchByQuery($query);

    	dd($results);
    }
}
