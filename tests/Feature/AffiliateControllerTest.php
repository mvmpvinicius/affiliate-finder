<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Tests\TestCase;

class AffiliateControllerTest extends TestCase
{    
    /**
     * It should assert status 200 with status as success
     *
     * @return void
     */
    public function testItShouldRetrieveAffiliates()
    {
        $response = $this->json(Request::METHOD_GET, route('affiliate'));

        $response
            ->assertStatus(200)
            ->assertJsonFragment(['status' => 'success']);
    }
}
