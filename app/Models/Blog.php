<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['blogTranslations'];

    public function scopeIsActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function collectTranslation($entity = '', $lang_key = '')
    {
        $lang_key = $lang_key ==  '' ? app()->getLocale() : $lang_key;
        $translations = $this->blogTranslations->where('lang_key', $lang_key)->first();
        return $translations != null && $translations->$entity ? $translations->$entity : $this->$entity;
    }

    public function blogTranslations()
    {
        return $this->hasMany(BlogTranslation::class);
    }

    public function blogCategory()
    {
        return $this->hasOne(BlogCategory::class, 'id', 'blog_category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'blog_tags', 'blog_id', 'tag_id');
    }
}
