<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property int $amo_id
 * @property Account $account
 */
class Contact extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'amo_id',
    ];

    /**
     * @return BelongsTo
     */
    public function accounts(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * @return BelongsToMany
     */
    public function leads(): BelongsToMany
    {
        return $this->belongsToMany(
            Lead::class,
            'leads_contacts',
            'id',
            'contact_id'
        );
    }
}
