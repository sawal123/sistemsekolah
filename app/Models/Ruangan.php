<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUnit;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use BelongsToUnit;

    protected $guarded = ['id'];

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }
}
