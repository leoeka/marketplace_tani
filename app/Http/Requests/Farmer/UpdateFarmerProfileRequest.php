<?php

namespace App\Http\Requests\Farmer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFarmerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'farmer';
    }

    public function rules(): array
    {
        return [
            'shop_name' => ['required', 'string', 'max:255'],
            'group_name' => ['nullable', 'string', 'max:255'],
            'region' => ['required', 'string', 'max:255'],
            'story' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,png,webp', 'extensions:jpg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'shop_name.required' => __('farmer.validation.required', ['attribute' => __('farmer.fields.shop_name', [], 'id')], 'id'),
            'shop_name.max' => __('farmer.validation.max_string', ['attribute' => __('farmer.fields.shop_name', [], 'id'), 'max' => 255], 'id'),
            'region.required' => __('farmer.validation.required', ['attribute' => __('farmer.fields.region', [], 'id')], 'id'),
            'region.max' => __('farmer.validation.max_string', ['attribute' => __('farmer.fields.region', [], 'id'), 'max' => 255], 'id'),
            'group_name.max' => __('farmer.validation.max_string', ['attribute' => __('farmer.fields.group_name', [], 'id'), 'max' => 255], 'id'),
            'photo.image' => __('farmer.validation.photo_image', [], 'id'),
            'photo.mimes' => __('farmer.validation.photo_type', [], 'id'),
            'photo.extensions' => __('farmer.validation.photo_type', [], 'id'),
            'photo.max' => __('farmer.validation.photo_max', [], 'id'),
            'shop_name.string' => __('farmer.validation.string', ['attribute' => __('farmer.fields.shop_name', [], 'id')], 'id'),
            'group_name.string' => __('farmer.validation.string', ['attribute' => __('farmer.fields.group_name', [], 'id')], 'id'),
            'region.string' => __('farmer.validation.string', ['attribute' => __('farmer.fields.region', [], 'id')], 'id'),
            'story.string' => __('farmer.validation.string', ['attribute' => __('farmer.fields.story', [], 'id')], 'id'),
        ];
    }

    public function attributes(): array
    {
        return [
            'shop_name' => __('farmer.fields.shop_name', [], 'id'),
            'group_name' => __('farmer.fields.group_name', [], 'id'),
            'region' => __('farmer.fields.region', [], 'id'),
            'story' => __('farmer.fields.story', [], 'id'),
            'photo' => __('farmer.fields.photo', [], 'id'),
        ];
    }
}