<?php

namespace App\APIClients;

enum HttpMethod : string
{
    case GET = 'get';
    case POST = 'post';
    case PUT = 'put';
    case DELETE = 'delete';
    case CREATE = 'create';
}
