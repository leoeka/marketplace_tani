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
            'photos.required' => __('farmer.validation.photos_required', [], 'id'),
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
            'harvest_date.required' => __('farmer.validation.harvest_date_required', [], 'id'),
            'harvest_date.date' => __('farmer.validation.harvest_date_invalid', [], 'id'),
            'harvest_date.after_or_equal' => __('farmer.validation.harvest_date_past', [], 'id'),
            'photos.*.required' => __('farmer.validation.photos_required_file', [], 'id'),
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
            'harvest_date' => __('farmer.fields.harvest_date', [], 'id'),
            'photos' => __('farmer.products.images', [], 'id'),
        ];
    }
}