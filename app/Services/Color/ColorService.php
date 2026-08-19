<?php

namespace App\Services\Color;

use App\Models\Color;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * A class defines the color service
 */
class ColorService
{
    /**
     * Get paginated colors
     *
     * @param int $limit
     *
     * @return LengthAwarePaginator
     */
    public function index(int $limit): LengthAwarePaginator
    {
        return Color::orderBy('created_at', 'desc')->paginate($limit);
    }

    /**
     * Store new color
     *
     * @param array $data
     *
     * @return Color
     *
     * @throws Exception
     */
    public function store(array $data): Color
    {
        return Color::create($data);
    }

    /**
     * Update color
     *
     * @param Color $color
     * @param array $data
     *
     * @return Color
     *
     * @throws Exception
     */
    public function update(Color $color, array $data): Color
    {
        $color->update($data);

        return $color->fresh();
    }

    /**
     * Delete color
     *
     * @param Color $color
     *
     * @return bool
     *
     * @throws Exception
     */
    public function delete(Color $color): bool
    {
        return $color->delete();
    }

    /**
     * Get active colors for settings
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveColors()
    {
        return Color::where('is_active', true)->orderBy('name_en')->get();
    }
}
