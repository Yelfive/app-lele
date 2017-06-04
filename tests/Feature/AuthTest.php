<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-14
 */

namespace Tests\Feature;

use App\Components\HttpStatusCode;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;
use PHPUnit_Framework_ExpectationFailedException as ExpectationFailedException;

class AuthTest extends TestCase
{
    public function testRegister()
    {
        /** @var \App\Models\User $user */
        $user = factory(\App\Models\User::class)->make();

        $requestData = [
            'name' => $user->name,
            'mobile' => $user->mobile,
            'address' => $user->address,
            'idcard' => $user->idcard,
            'password' => $user->password,
            'idcard_front' => UploadedFile::fake()->image('front.jpeg'),
            'idcard_back' => UploadedFile::fake()->image('back.jpeg'),
        ];
        try {
            $response = $this->post('api/user', $requestData)->assertJson(['code' => HttpStatusCode::SUCCESS_OK]);
            $data = $response->json()['data'];
            $data['password'] = $requestData['password'];
            return $data;
        } catch (ExpectationFailedException $e) {
            $message = $e->getMessage();
            $message .= '. ' . print_r($requestData, true);
            throw new ExpectationFailedException($message, $e->getComparisonFailure(), $e);
        }
    }

    /**
     * @depends testRegister
     * @param array $userInfo
     * @return array
     */
    public function testLogin(array $userInfo)
    {
        $response = $this->put('api/user', [
            'mobile' => $userInfo['mobile'],
            'password' => $userInfo['password'],
        ]);
        $response->assertJson(['code' => HttpStatusCode::SUCCESS_OK]);

        return [
            $response->json()['access_token'],
            Session::all()
        ];
    }

    /**
     * @depends testLogin
     *
     * @param array $data
     */
    public function testInfo(array $data)
    {
        list ($token, $session) = $data;
        $this->withSession($session)
            ->get('api/user', [
                'X-Access-Token' => $token
            ])
            ->assertJson(['code' => HttpStatusCode::SUCCESS_OK]);
    }
}