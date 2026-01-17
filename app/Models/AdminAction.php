<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'action_type',
        'entity_type',
        'entity_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Action types constants.
     */
    public const ACTION_VERIFY = 'verify';
    public const ACTION_SUSPEND = 'suspend';
    public const ACTION_ACTIVATE = 'activate';
    public const ACTION_DELETE = 'delete';
    public const ACTION_TOGGLE_VISIBILITY = 'toggle_visibility';
    public const ACTION_UPDATE = 'update';
    public const ACTION_CREATE = 'create';

    /**
     * Entity types constants.
     */
    const ENTITY_USER = 'user';
    const ENTITY_PROPERTY = 'property';
    const ENTITY_RESERVATION = 'reservation';
    const ENTITY_REVIEW = 'review';

    /**
     * Scope to filter by action type.
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action_type', $action);
    }

    /**
     * Scope to filter by entity type.
     */
    public function scopeByEntity($query, string $entityType)
    {
        return $query->where('entity_type', $entityType);
    }

    /**
     * Scope to filter by entity.
     */
    public function scopeForEntity($query, string $entityType, int $entityId)
    {
        return $query->where('entity_type', $entityType)
            ->where('entity_id', $entityId);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeInPeriod($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Get the admin who performed the action.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Log an admin action.
     */
    public static function log(
        int $adminId,
        string $actionType,
        string $entityType,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $notes = null
    ): self {
        return self::create([
            'admin_id' => $adminId,
            'action_type' => $actionType,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'notes' => $notes,
        ]);
    }
}
