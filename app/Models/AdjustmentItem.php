<?php

namespace App\Models;

use App\Traits\HasManySyncable;

class AdjustmentItem extends Model
{
    use HasManySyncable;

    protected $fillable = [
        'adjustment_id', 'item_id', 'weight', 'quantity', 'unit_id', 'batch_no', 'expiry_date', 'account_id', 'draft', 'warehouse_id', 'type',
    ];

    public function adjustment()
    {
        return $this->belongsTo(Adjustment::class)->withTrashed();
    }

    public function createSerials($cId, $serials)
    {
        if ($cId && ! empty($serials)) {
            foreach ($serials as $serial) {
                Serial::updateOrCreate(
                    ['number' => $serial],
                    ['number' => $serial, 'adjustment_id' => $cId, 'adjustment_item_id' => $this->id, 'item_id' => $this->item_id]
                );
            }
        }
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function scopeOfCategory($query, $category)
    {
        return $query->whereHas('item', fn ($query) => $query->ofCategory($category));
    }

    public function serials()
    {
        return $this->belongsToMany(Serial::class);
    }

    public function stock()
    {
        return $this->hasMany(Stock::class, 'item_id', 'item_id');
    }

    public function stockTrails()
    {
        return $this->morphMany(StockTrail::class, 'subject');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function variations()
    {
        // return $this->hasMany(Variation::class);
        return $this->belongsToMany(Variation::class)->withPivot('weight', 'quantity', 'unit_id');
    }
}
