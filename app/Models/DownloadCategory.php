<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DownloadCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function downloads()
    {
        return $this->hasMany(Download::class, 'category_id');
    }
}
