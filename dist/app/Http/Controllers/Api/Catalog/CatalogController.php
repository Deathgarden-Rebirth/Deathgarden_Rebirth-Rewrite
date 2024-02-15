<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CatalogController extends Controller
{
    public function getCatalog(string $catalogVersion): string {
        return Storage::disk('local')->get('/catalog/'.$catalogVersion.'.json');
    }
}
