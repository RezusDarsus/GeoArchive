<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CategoryManager
{
    public function __construct(private readonly PublicImageStorage $images) {}

    public function create(array $data, ?UploadedFile $image): Category
    {
        unset($data['image'], $data['remove_image']);

        return $this->images->storeSafely($image, 'categories', function (?string $path) use ($data): Category {
            if ($path) {
                $data['image'] = $path;
            }

            return DB::transaction(fn () => Category::create($data));
        });
    }

    public function update(Category $category, array $data, ?UploadedFile $image, bool $removeImage): Category
    {
        unset($data['image'], $data['remove_image']);
        $oldImage = $category->image;
        $newImage = $this->images->storeSafely($image, 'categories', function (?string $path) use ($category, $data, $removeImage): ?string {
            if ($path) {
                $data['image'] = $path;
            } elseif ($removeImage) {
                $data['image'] = null;
            }
            DB::transaction(fn () => $category->update($data));

            return $path;
        });
        if ($newImage || $removeImage) {
            $this->images->delete($oldImage);
        }

        return $category->refresh();
    }

    public function delete(Category $category): void
    {
        $image = $category->image;
        DB::transaction(fn () => $category->delete());
        $this->images->delete($image);
    }
}
