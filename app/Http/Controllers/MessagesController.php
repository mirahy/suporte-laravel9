<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class MessagesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function messagesHttp( int $status = null,  $response = null , String $msg = null){

        switch ($status ? $status : $response->status()) {
            case 200:
                return $msg ? $msg : "200 OK";
                break;

            case 201:
                return $msg ? $msg : "201 Created";
                break;

            case 202:
                return $msg ? $msg : "202 Accepted";
                break;

            case 204:
                abort(204, $msg ? $msg : "204 No Content");
                break;

            case 301:
                return $msg ? $msg : "301 Moved Permanently";
                break;

            case 302:
                return $msg ? $msg : "302 Found";
                break;

            case 400:
                abort(400, $msg ? $msg : "400 Bad Request");
                break;

            case 401:
                abort(401, $msg ? $msg : "401 Unauthorized");
                break;

            case 402:
                abort(402, $msg ? $msg : "402 Payment Required");
                break;

            case 403:
                abort(403, $msg ? $msg : "403 Forbidden");
                break;

            case 404:
                abort(404, $msg ? $msg : "404 Not Found");
                break;

            case 408:
                abort(408, $msg ? $msg : "408 Request Timeout");
                break;

            case 409:
                abort(409, $msg ? $msg : "409 Conflict");
                break;

            case 422:
                abort(422, $msg ? $msg : "422 Unprocessable Entity");
                break;

            case 429:
                abort(429, $msg ? $msg : "429 Too Many Requests");
                break;

            case 500:
                abort(500, $msg ? $msg : "500 Internal Server Error");
                break;

            default:
                abort($response->status(), $response->body());
                break;
        }

    }
}
