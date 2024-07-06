<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Game\CatalogItem;
use Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class CatalogController extends Controller
{
    const CATALOG_DROPDOWN_PAGINATION_LIMIT = 20;

    public function getCatalog(string $catalogVersion): string
    {
        return Storage::disk('local')->get('/catalog/' . $catalogVersion . '.json');
    }

    public function catalogItemDropdown(Request $request)
    {
        if(!Auth::check())
            abort(403, 'You are not logged in.');

        $searchTerm = $request->get('term');

        if($searchTerm === null)
            abort(400, 'Search term must be provided.');

        /** @var Collection|LengthAwarePaginator $users */
        $items = CatalogItem::where('display_name', 'LIKE', "%{$searchTerm}%")
            ->select(['id', 'display_name'])
            ->paginate(static::CATALOG_DROPDOWN_PAGINATION_LIMIT);

        $options = [];
        $items->each(function ($item) use (&$options) {
            $options[] = [
                'id' => $item->id,
                'text' => $item->display_name,
            ];
        });

        return [
            'results' => $options,
            'pagination' => [
                'more' => $items->currentPage() < $items->lastPage(),
            ]
        ];
    }
}
