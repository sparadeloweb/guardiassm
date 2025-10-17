<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pathology extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pathologies';

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Obtiene las atenciones que tienen esta patología.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function attentions(): BelongsToMany
    {
        return $this->belongsToMany(Attention::class, 'attention_pathology', 'pathology_id', 'attention_id');
    }
}
