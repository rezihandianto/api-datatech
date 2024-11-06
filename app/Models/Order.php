<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
    ];

    public static function autonumber()
    {
        $query = DB::table('orders')->select(DB::raw('MAX(SUBSTRING(order_number,6,5)) as kd_max'));
        //DT00001
        if ($query->count() > 0) {
            foreach ($query->get() as $key) {
                $tmp = ((int) $key->kd_max) + 1;
                $generateNumber = 'DT' . sprintf("%05s", $tmp);
            }
        } else {
            $generateNumber = 'DT00001';
        }
        return $generateNumber;
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
