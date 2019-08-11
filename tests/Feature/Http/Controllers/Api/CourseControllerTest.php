<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseControllerTest extends TestCase
{
    /**
     *
     *
     * @test
     */
    public function can_create_a_course()
    {
        //Given
        //When
        $response = $this->json('POST', '/api/login', [

        ]);
        //Then
        $response->assertStatus(200);
    }
}
