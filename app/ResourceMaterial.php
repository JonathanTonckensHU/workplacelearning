<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class ResourceMaterial extends Model
{
    // Override the table used for the User Model
    protected $table = 'resourcematerial';
    // Disable using created_at and updated_at columns
    public $timestamps = false;
    // Override the primary key column
    protected $primaryKey = 'rm_id';

    // Default
    protected $fillable = [
        'rm_id',
        'rm_label',
        'wplp_id'
    ];

    public function workplaceLearningPeriod()
    {
        return $this->belongsTo(\App\WorkplaceLearningPeriod::class, 'wplp_id', 'wplp_id');
    }

    public function learningActivityProducing()
    {
        return $this->belongsTo(\App\LearningActivityProducing::class, 'rm_id', 'res_material_id');
    }

    // Relations for query builder
    public function getRelationships()
    {
        return ["workplaceLearningPeriod", "learningActivityProducing"];
    }
}
