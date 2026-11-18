<?php

namespace App\Http\Requests\Farmer;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Product::class) ?? false;
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
            'is_preorder' => ['sometimes', 'boolean'],
            'harvest_date' => ['exclude_unless:is_preorder,1', 'required', 'date', 'after_or_equal:today'],
            'action' => ['required', 'in:draft,submit'],
            'photos' => ['required', 'array', 'min:1', 'max:5'],
            'photos.*' => ['required', 'image', 'mimes:jpg,png,webp', 'extensions:jpg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('farmer.validation.required', ['attribute' => __('farmer.fields.name')]),
            'category_id.required' => __('farmer.validation.required', ['attribute' => __('farmer.fields.category')]),
            'category_id.exists' => __('farmer.validation.category_invalid'),
            'description.required' => __('farmer.validation.required', ['attribute' => __('farmer.fields.description')]),
            'unit.required' => __('farmer.validation.required', ['attribute' => __('farmer.fields.unit')]),
            'unit.in' => __('farmer.validation.unit_invalid'),
            'price_idr.required' => __('farmer.validation.required', ['attribute' => __('farmer.fields.price')]),
            'price_idr.integer' => __('farmer.validation.integer', ['attribute' => __('farmer.fields.price')]),
            'price_idr.min' => __('farmer.validation.non_negative', ['attribute' => __('farmer.fields.price')]),
            'stock.required' => __('farmer.validation.required', ['attribute' => __('farmer.fields.stock')]),
            'stock.integer' => __('farmer.validation.integer', ['attribute' => __('farmer.fields.stock')]),
            'stock.min' => __('farmer.validation.non_negative', ['attribute' => __('farmer.fields.stock')]),
            'action.required' => __('farmer.validation.action_required'),
            'action.in' => __('farmer.validation.action_invalid'),
            'photos.required' => __('farmer.validation.photos_required'),
            'photos.array' => __('farmer.validation.photos_invalid'),
            'photos.min' => __('farmer.validation.photos_min'),
            'photos.max' => __('farmer.validation.photos_max'),
            'photos.*.image' => __('farmer.validation.photo_image'),
            'photos.*.mimes' => __('farmer.validation.photo_type'),
            'photos.*.extensions' => __('farmer.validation.photo_type'),
            'photos.*.max' => __('farmer.validation.photo_max'),
            'name.string' => __('farmer.validation.string', ['attribute' => __('farmer.fields.name')]),
            'name.max' => __('farmer.validation.max_string', ['attribute' => __('farmer.fields.name'), 'max' => 255]),
            'category_id.integer' => __('farmer.validation.integer', ['attribute' => __('farmer.fields.category')]),
            'description.string' => __('farmer.validation.string', ['attribute' => __('farmer.fields.description')]),
            'price_idr.max' => __('farmer.validation.max_numeric', ['attribute' => __('farmer.fields.price')]),
            'stock.max' => __('farmer.validation.max_numeric', ['attribute' => __('farmer.fields.stock')]),
            'harvest_date.required' => __('farmer.validation.harvest_date_required'),
            'harvest_date.date' => __('farmer.validation.harvest_date_invalid'),
            'harvest_date.after_or_equal' => __('farmer.validation.harvest_date_past'),
            'photos.*.required' => __('farmer.validation.photos_required_file'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('farmer.fields.name'),
            'category_id' => __('farmer.fields.category'),
            'description' => __('farmer.fields.description'),
            'unit' => __('farmer.fields.unit'),
            'price_idr' => __('farmer.fields.price'),
            'stock' => __('farmer.fields.stock'),
            'harvest_date' => __('farmer.fields.harvest_date'),
            'photos' => __('farmer.products.images'),
        ];
    }
}