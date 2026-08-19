<?php

namespace App\Actions\Categories;

use App\Http\Resources\Admin\Category\CategoryResource;
use App\Models\Category;
use App\Models\Project;
use App\Models\Service;
use App\Models\ServicePackage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the action to Get models categories with average prices
 */
class CategoriesAveragePricesAction
{
    /**
     * Get model categories with average prices
     */
    public function calculate(Model $model, string $modelCategoriesRate): array
    {

        if ($model instanceof Service) {
            return $this->calculateServiceAverages($modelCategoriesRate);
        }

        $query = $model::query();
        $this->applyDateFilter($query, $modelCategoriesRate);

        $priceExpression = $model instanceof Project
            ? DB::raw('AVG((min_price + max_price) / 2) as avg_price')
            : DB::raw('AVG(price) as avg_price');

        $averages = $query->select('category_id', $priceExpression)
            ->whereNotNull('category_id')
            ->groupBy('category_id')
            ->with(['category'])
            ->get();

        return $averages->map(function ($item) {
            if (!$item->category) {
                return null;
            }
            return [
                'category' => CategoryResource::make($item->category)->resolve(),
                'price_average' => round((float) $item->avg_price, 2)
            ];
        })->filter()->values()->all();
    }

    /**
     * Calculate averages for services through their packages
     */
    private function calculateServiceAverages(string $categoriesRate): array
    {
        $query = ServicePackage::query()
            ->join('services', 'service_packages.service_id', '=', 'services.id');

        $this->applyDateFilter($query, $categoriesRate, 'service_packages.created_at');

        $averages = $query->select('services.category_id', DB::raw('AVG(service_packages.price) as avg_price'))
            ->whereNotNull('services.category_id')
            ->groupBy('services.category_id')
            ->get();

        // Optimization: Fetch all categories in one go
        $categoryIds = $averages->pluck('category_id');
        $categories = Category::whereIn('id', $categoryIds)->get()->keyBy('id');

        return $averages->map(function ($item) use ($categories) {
            $category = $categories->get($item['category_id']);
            if (!$category) {
                return null;
            }

            return [
                'category' => CategoryResource::make($category)->resolve(),
                'price_average' => round((float) $item['avg_price'], 2)
            ];
        })->filter()->values()->all();
    }

    /**
     * Apply date filter to query
     */
    private function applyDateFilter($query, string $categoriesRate, string $dateColumn = 'created_at')
    {
        if ($categoriesRate === 'weekly') {
            $query->whereBetween($dateColumn, [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($categoriesRate === 'yearly') {
            $query->whereYear($dateColumn, now()->year);
        } else {
            $query->whereMonth($dateColumn, now()->month)
                ->whereYear($dateColumn, now()->year);
        }
    }
}
