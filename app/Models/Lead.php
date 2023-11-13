<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Lead
 *
 * @package App\Models
 * @property int $id
 * @property int $account_id
 * @property string $name
 * @property string $description
 */
class Lead extends Model
{
    use HasFactory;

    /**
     * Get the account that the lead belongs to.
     *
     * @return BelongsTo
     */
    public function accounts(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the contacts that belong to the lead.
     *
     * @return BelongsToMany
     */
    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(
            Contact::class,
            'leads_contacts',
            'id',
            'lead_id'
        );
    }
}
