<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Access
 *
 * @package App\Models
 * @property int $id
 * @property string $amo_id
 * @property string $api_key
 * @property string $base_domain
 * @property string $access_token
 * @property string $refresh_token
 * @property int|null $expires
 *
 * @property-read Account $account
 */
class Access extends Model
{
    use HasFactory;

    /**
     * The guarded attributes on the model.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'amo_id',
        'api_key',
        'base_domain',
        'access_token',
        'refresh_token',
        'expires',
    ];

    /**
     * Get the account that owns the access.
     *
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'amo_id', 'amo_id');
    }
}
