<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'unit_type',
        'dosage_form',
        'strength',
        'unit_price',
        'reorder_level',
        'status',
        'image_path',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
        ];
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function prescriptionItems(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                if ($this->image_path !== null && $this->image_path !== '' && Storage::disk('public')->exists($this->image_path)) {
                    return Storage::disk('public')->url($this->image_path);
                }

                return asset('images/medication-placeholder.svg');
            },
        );
    }
}
