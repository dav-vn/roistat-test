<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Account
 *
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $amo_id
 *
 * @property-read Lead $leads
 * @property-read $contacts
 * @property-read Access $accesses
 */
class Account extends Model
{
    use HasFactory;

    /**
     * The guarded attributes on the model.
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'name',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'amo_id',
    ];

    /**
     * Get the leads associated with the account.
     *
     * @return HasMany
     */
    public function leads(): HasMany
    {
        return $this->hasMany(
            Lead::class,
            'amo_id',
            'amo_id'
        );
    }

    /**
     * Get the contacts associated with the account.
     *
     * @return HasMany
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(
            Contact::class,
            'amo_id',
            'amo_id'
        );
    }

    /**
     * Get the access associated with the account.
     *
     * @return HasOne
     */
    public function accesses(): HasOne
    {
        return $this->hasOne(
            Access::class,
            'amo_id',
            'amo_id'
        );
    }
}
