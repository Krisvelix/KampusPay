<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $products = Product::query()
            ->with('merchant')
            ->where('is_available', true)
            ->where('stock', '>', 0)
            ->whereHas('merchant', fn ($query) => $query->where('status', Merchant::STATUS_APPROVED))
            ->when($search !== '', fn ($query) => $query->where('name', 'ilike', '%' . $search . '%'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('customer.catalog.index', compact('products', 'search'));
    }
}