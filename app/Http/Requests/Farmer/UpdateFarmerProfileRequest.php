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
            'shop_name.required' => __('farmer.validation.required', ['attribute' => __('farmer.fields.shop_name')]),
            'shop_name.max' => __('farmer.validation.max_string', ['attribute' => __('farmer.fields.shop_name'), 'max' => 255]),
            'region.required' => __('farmer.validation.required', ['attribute' => __('farmer.fields.region')]),
            'region.max' => __('farmer.validation.max_string', ['attribute' => __('farmer.fields.region'), 'max' => 255]),
            'group_name.max' => __('farmer.validation.max_string', ['attribute' => __('farmer.fields.group_name'), 'max' => 255]),
            'photo.image' => __('farmer.validation.photo_image'),
            'photo.mimes' => __('farmer.validation.photo_type'),
            'photo.extensions' => __('farmer.validation.photo_type'),
            'photo.max' => __('farmer.validation.photo_max'),
            'shop_name.string' => __('farmer.validation.string', ['attribute' => __('farmer.fields.shop_name')]),
            'group_name.string' => __('farmer.validation.string', ['attribute' => __('farmer.fields.group_name')]),
            'region.string' => __('farmer.validation.string', ['attribute' => __('farmer.fields.region')]),
            'story.string' => __('farmer.validation.string', ['attribute' => __('farmer.fields.story')]),
        ];
    }

    public function attributes(): array
    {
        return [
            'shop_name' => __('farmer.fields.shop_name'),
            'group_name' => __('farmer.fields.group_name'),
            'region' => __('farmer.fields.region'),
            'story' => __('farmer.fields.story'),
            'photo' => __('farmer.fields.photo'),
        ];
    }
}