<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Auth\SessionGuard;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\EloquentUserProvider;

class LoteSalasSimplificadosControllerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        //$response = $this->actingAs($this->getUserBadeco())->call('GET', '/lote-salas-simplificados/rota', ["test"=>"test"]);
        $response = $this->actingAs($this->getUserAdmin())->get('/lote-salas-simplificados/rota');

        //var_dump($request);
        $response->assertSeeText("test");
        //$this->assertTrue(true);
        $response->assertStatus(200);
    }

    public function testForbid()
    {
        $response = $this->actingAs($this->getUserBadeco())->get('/lote-salas-simplificados/rota');
        //$response = $this->actingAs($this->getUserBadeco())->call('GET', '/lote-salas-simplificados/rota', ["test"=>"test"]);

        $response->assertStatus(4010);
    }
}
