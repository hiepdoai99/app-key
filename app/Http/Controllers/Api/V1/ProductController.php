<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\PlanCollection;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\ProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function index(ProductRequest $request)
    {
        $products = QueryBuilder::for(Product::class)
            ->allowedFields(array_merge(app(Product::class)->getFillable(), ['id', 'created_at', 'updated_at', 'plans.*']))
            ->allowedIncludes(['plans', 'planSubscriptions','invoices'])
            ->allowedFilters([
                'code', 'bank_account', 'transaction','status','name',
                AllowedFilter::trashed(),
            ])
            ->allowedAppends('ranges', 'revenue', 'revenue_approve')
            ->paginate($request->per_page ?? 10)
            ->appends($request->all());
        return $this->respondSuccess(new ProductCollection($products));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\ProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        return $this->respondCreated(
            new ProductResource(Product::create($request->validated()))
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\ProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(ProductRequest $request, Product $product)
    {
        return $this->respondSuccess(new ProductResource($product));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\ProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {
        $product->fill($request->validated())->save();
        return $this->respondSuccess(new ProductResource($product));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\ProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductRequest $request, Product $product)
    {
        if ($product->delete()) {
            return $this->respondOk(__('trans.delete_success', ['resource', 'sản phẩm '.$product->name]));
        }
        return $this->respondError(__('trans.delete_fail', ['resource', 'sản phẩm '.$product->name]));
    }

    public function getPlans(ProductRequest $request, Product $product)
    {
        return $this->respondSuccess(
            new PlanCollection($product->plans())
        );
    }

    public function getSubscriptions(ProductRequest $request, Product $product)
    {
        return $this->respondSuccess(
            new PlanCollection($product->planSubscriptions())
        );
    }

}
