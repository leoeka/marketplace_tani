@props(['category'])
@php($categoryKey = 'export.categories.'.$category->slug)
{{ \Illuminate\Support\Facades\Lang::has($categoryKey, app()->getLocale()) ? __($categoryKey, [], app()->getLocale()) : $category->name }}