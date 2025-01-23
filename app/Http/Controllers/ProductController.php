<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    public function index()
    {
        return Product::with('categories', 'tags')->get();
    }

    public function show(Product $product)
    {
        return $product->load('categories', 'tags');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Product::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
        ]);

        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'price' => 'numeric',
            'quantity' => 'integer',
        ]);

        $product->update($validated);

        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $product->delete();

        return response()->json(null, 204);
    }

    public function addCategory(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);

        $category = Category::find($validated['category_id']);
        $product->categories()->attach($category);

        return response()->json(['message' => 'Category added to product']);
    }

    public function removeCategory(Product $product, Category $category)
    {
        $product->categories()->detach($category);

        return response()->json(['message' => 'Category removed from product']);
    }

    public function addTag(Request $request, Product $product)
    {
        $validated = $request->validate([
            'tag_id' => 'required|exists:tags,id',
        ]);

        $tag = Tag::find($validated['tag_id']);
        $product->tags()->attach($tag);

        return response()->json(['message' => 'Tag added to product']);
    }

    public function removeTag(Product $product, Tag $tag)
    {
        $product->tags()->detach($tag);

        return response()->json(['message' => 'Tag removed from product']);
    }

    // 1. For a given list of products, get the names of all categories that contain this products
    public function getCategoriesForProducts(Request $request)
    {
        $productIds = $request->input('product_ids');

        $categories = Category::join('product_categories', 'categories.id', '=', 'product_categories.category_id')
            ->whereIn('product_categories.product_id', $productIds)
            ->select('categories.name AS category_name')
            ->distinct()
            ->get();

        return response()->json($categories);
    }

    // 2. For a given category, get a list of offers for all products in this category and its child categories.
    public function getProductsInCategoryAndChildren($categoryId)
    {
        $products = DB::table('categories as c')
            ->join('product_categories as pc', 'c.id', '=', 'pc.category_id')
            ->join('products as p', 'p.id', '=', 'pc.product_id')
            ->whereIn('c.id', function ($query) use ($categoryId) {
                $query->select('id')
                    ->from('categories')
                    ->where('id', $categoryId)
                    ->orWhere('parent_id', $categoryId);
            })
            ->select('p.name as product_name', 'p.price', 'p.quantity_in_stock')
            ->get();

        return response()->json($products);
    }

    // 3. For a given list of categories, get the number of product offers in each category
    public function getProductCountInCategories(Request $request)
    {
        $categoryIds = $request->input('category_ids');

        $counts = Category::leftJoin('product_categories', 'categories.id', '=', 'product_categories.category_id')
            ->whereIn('categories.id', $categoryIds)
            ->select('categories.name as category_name', DB::raw('COUNT(product_categories.product_id) as product_count'))
            ->groupBy('categories.id')
            ->get();

        return response()->json($counts);
    }

    // 4. For a given list of categories, get the total number of unique product offers
    public function getUniqueProductCountInCategories(Request $request)
    {
        $categoryIds = $request->input('category_ids');

        $count = DB::table('product_categories')
            ->join('categories', 'categories.id', '=', 'product_categories.category_id')
            ->whereIn('categories.id', $categoryIds)
            ->distinct('product_categories.product_id')
            ->count('product_categories.product_id');

        return response()->json(['unique_product_count' => $count]);
    }

    // 5. For a given category, get its full path in the tree (breadcrumb)
    public function getCategoryBreadcrumb($categoryId)
    {
        $breadcrumb = DB::withRecursiveExpression('category_tree', function ($query) use ($categoryId) {
            $query->select('id', 'parent_id', 'name')
                ->from('categories')
                ->where('id', $categoryId)
                ->unionAll(
                    $query->select('c.id', 'c.parent_id', 'c.name')
                        ->from('categories as c')
                        ->join('category_tree as ct', 'c.parent_id', '=', 'ct.id')
                );
        })
            ->from('category_tree')
            ->orderBy('id')
            ->pluck('name')
            ->toArray();

        $breadcrumb = implode(' > ', $breadcrumb);

        return response()->json(['breadcrumb' => $breadcrumb]);
    }
}
