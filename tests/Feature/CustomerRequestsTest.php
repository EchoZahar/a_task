<?php

namespace Tests\Feature;

use App\Models\CustomerRequest;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CustomerRequestsTest extends TestCase
{
    public function testGetWelcomePage()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testGetListCustomerRequests()
    {
        $response = $this->get('/api/requests');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetCustomerRequestItem()
    {
        $customer = CustomerRequest::find(1);
        $response = $this->get('/api/requests/' . $customer->id);
        $response->assertJsonPath('data.name', $customer->name);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCreateNewCustomerRequest()
    {
        $count = CustomerRequest::count();
        $response = $this->post('/api/requests', [
            'name' => fake()->name(),
            'email' => 'testCase@example.com',
            'message' => fake()->realText(300),
        ]);
        $response->assertJsonPath('data.email', 'testCase@example.com');
        $this->assertDatabaseCount('customer_requests', $count + 1);
        $this->assertDatabaseHas('customer_requests', ['email' => 'testCase@example.com']);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUpdateCustomerRequestItemWithAdmin()
    {
        Sanctum::actingAs(
            User::where('type', 'admin')->first(),
            ['limited:token']
        );
        $response = $this->put('/api/requests/1', [
            'status' => CustomerRequest::RESOLVED,
            'comment' => fake()->realText(100),
        ]);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testTryToUpdateCustomerRequestItemNotAdmin()
    {
        Sanctum::actingAs(
            User::where('type', 'customer')->first(),
            ['limited:token']
        );
        $response = $this->put('/api/requests/1', [
            'status' => CustomerRequest::RESOLVED,
            'comment' => fake()->realText(100),
        ]);
        $response->assertJsonPath('message', "Administrator only make update request !");
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDeleteCustomerRequestWithAdmin()
    {
        Sanctum::actingAs(
            User::where('type', 'admin')->first(),
            ['limited:token']
        );
        $response = $this->delete('/api/requests/1');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDeleteCustomerRequestWithCustomer()
    {
        Sanctum::actingAs(
            User::where('type', 'customer')->first(),
            ['limited:token']
        );
        $response = $this->delete('/api/requests/1');
        $response->assertJsonPath('message', "Something wrong! Please try again later or resolved this request");
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testNotFoundCustomerRequest()
    {
        $response = $this->get('/api/requests/1');
        $this->assertEquals(404, $response->getStatusCode());
    }
}
