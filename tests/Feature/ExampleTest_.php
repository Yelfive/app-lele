<?php

namespace Tests\Feature;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {

//        $this->json('POST', '/user', ['name' => 'Sally'])
//            ->seeJson([
//                'created' => true,
//            ]);
        $response = $this->get('/test');
        $response->macro('assertJsonCodeLessThan', function ($code) {
            /** @var TestResponse $this */
            $data = $this->json();
            \PHPUnit_Framework_Assert::assertTrue(
                ($data['code'] ?? 0) > $code,
                <<<ERR
code of $code is expected
code {$data['code']} given
ERR
            );
            return $this;
        });
        $response->assertJsonCodeLessThan(400);

//        $response->assertJson(['code' => 201])->assertJson(['code' => 200]);
//        $response->assertStatus(200);
    }
}
