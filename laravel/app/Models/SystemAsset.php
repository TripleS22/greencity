<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemAsset extends Model
{
    protected $fillable = ['name', 'url', 'zip_file', 'db_backup', 'github_url', 'version', 'tech_stack', 'hoster'];
}
