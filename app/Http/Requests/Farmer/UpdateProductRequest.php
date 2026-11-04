<?php

namespace App\Http\Requests\Farmer;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        $product = $this->route('product');

        return $product instanceof Product && ($this->user()?->can('update', $product) ?? false);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['required', 'string'],
            'unit' => ['required', 'in:kg,ton'],
            'price_idr' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'stock' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'action' => ['required', 'in:draft,submit'],
            'photos' => ['sometimes', 'array', 'min:1', 'max:5'],
            'photos.*' => ['required', 'image', 'mimes:jpg,png,webp', 'extensions:jpg,png,webp', 'max:2048'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            $product = $this->route('product');

            if ($product instanceof Product && $product->images()->doesntExist() && empty($this->file('photos'))) {
                $validator->errors()->add('photos', __('farmer.validation.photos_required', [], 'id'));
            }
        }];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('farmer.validation.required', ['attribute' => __('farmer.fields.name', [], 'id')], 'id'),
            'category_id.required' => __('farmer.validation.required', ['attribute' => __('farmer.fields.category', [], 'id')], 'id'),
            'category_id.exists' => __('farmer.validation.category_invalid', [], 'id'),
            'description.required' => __('farmer.validation.required', ['attribute' => __('farmer.fields.description', [], 'id')], 'id'),
            'unit.required' => __('farmer.validation.required', ['attribute' => __('farmer.fields.unit', [], 'id')], 'id'),
            'unit.in' => __('farmer.validation.unit_invalid', [], 'id'),
            'price_idr.required' => __('farmer.validation.required', ['attribute' => __('farmer.fields.price', [], 'id')], 'id'),
            'price_idr.integer' => __('farmer.validation.integer', ['attribute' => __('farmer.fields.price', [], 'id')], 'id'),
            'price_idr.min' => __('farmer.validation.non_negative', ['attribute' => __('farmer.fields.price', [], 'id')], 'id'),
            'stock.required' => __('farmer.validation.required', ['attribute' => __('farmer.fields.stock', [], 'id')], 'id'),
            'stock.integer' => __('farmer.validation.integer', ['attribute' => __('farmer.fields.stock', [], 'id')], 'id'),
            'stock.min' => __('farmer.validation.non_negative', ['attribute' => __('farmer.fields.stock', [], 'id')], 'id'),
            'action.required' => __('farmer.validation.action_required', [], 'id'),
            'action.in' => __('farmer.validation.action_invalid', [], 'id'),
            'photos.array' => __('farmer.validation.photos_invalid', [], 'id'),
            'photos.min' => __('farmer.validation.photos_min', [], 'id'),
            'photos.max' => __('farmer.validation.photos_max', [], 'id'),
            'photos.*.image' => __('farmer.validation.photo_image', [], 'id'),
            'photos.*.mimes' => __('farmer.validation.photo_type', [], 'id'),
            'photos.*.extensions' => __('farmer.validation.photo_type', [], 'id'),
            'photos.*.max' => __('farmer.validation.photo_max', [], 'id'),
            'name.string' => __('farmer.validation.string', ['attribute' => __('farmer.fields.name', [], 'id')], 'id'),
            'name.max' => __('farmer.validation.max_string', ['attribute' => __('farmer.fields.name', [], 'id'), 'max' => 255], 'id'),
            'category_id.integer' => __('farmer.validation.integer', ['attribute' => __('farmer.fields.category', [], 'id')], 'id'),
            'description.string' => __('farmer.validation.string', ['attribute' => __('farmer.fields.description', [], 'id')], 'id'),
            'price_idr.max' => __('farmer.validation.max_numeric', ['attribute' => __('farmer.fields.price', [], 'id')], 'id'),
            'stock.max' => __('farmer.validation.max_numeric', ['attribute' => __('farmer.fields.stock', [], 'id')], 'id'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('farmer.fields.name', [], 'id'),
            'category_id' => __('farmer.fields.category', [], 'id'),
            'description' => __('farmer.fields.description', [], 'id'),
            'unit' => __('farmer.fields.unit', [], 'id'),
            'price_idr' => __('farmer.fields.price', [], 'id'),
            'stock' => __('farmer.fields.stock', [], 'id'),
            'photos' => __('farmer.products.images', [], 'id'),
        ];
    }
}