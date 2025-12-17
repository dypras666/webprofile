<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramStudi extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'name',
        'code',
        'uuid',
        'slug',
        'program_head_id',
        'faculty',
        'degree',
        'accreditation',
        'description',
        'vision',
        'mission',
        'competence',
        'email',
        'phone',
        'website_url',
        'address',
        'establishment_date',
        'decree_number',
        'decree_date',
        'image',
        'sort_order',
    ];

    /**
     * Get the program head (Kaprodi).
     */
    public function programHead()
    {
        return $this->belongsTo(TeamMember::class, 'program_head_id');
    }

    protected $casts = [
        'establishment_date' => 'date',
        'decree_date' => 'date',
    ];
}
