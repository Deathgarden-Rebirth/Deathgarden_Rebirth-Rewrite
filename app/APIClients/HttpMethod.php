<?php

namespace App\APIClients;

enum HttpMethod : string
{
    case GET = 'get';
    case POST = 'post';
}
