<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class BagStatusDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'mini_pool_id',
        'blood_bank_id',
        'pickup_date',
        'ar_no',
        'status',
        'timestamp',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'pickup_date' => 'date',
        'timestamp' => 'datetime',
    ];

    public function barcodeEntry()
    {
        return $this->belongsTo(BarcodeEntry::class, 'mini_pool_id');
    }

    public function plasmaEntry()
    {
        return $this->belongsTo(PlasmaEntry::class, 'blood_bank_id');
    }

    public static function getMiniPoolDetails($bloodCentreName = null, $pickupDate = null)
    {
        try {
            \Log::info('Starting getMiniPoolDetails query for reactive:', [
                'bloodCentreName' => $bloodCentreName,
                'pickupDate' => $pickupDate
            ]);

            $query = DB::table('nat_test_report')
                ->select([
                    DB::raw('MAX(bag_entries.id) as id'),
                    DB::raw('MAX(bag_entries.blood_bank_id) as blood_bank_id'),
                    DB::raw('MAX(bag_entries.pickup_date) as pickup_date'),
                    DB::raw('MAX(bag_entries_details.donation_date) as donation_date'),
                    DB::raw('MAX(bag_entries_details.bag_volume_ml) as bag_volume_ml'),
                    DB::raw('MAX(bag_entries_details.blood_group) as blood_group'),
                    DB::raw('MAX(bag_entries_mini_pools.mini_pool_number) as mini_pool_number'),
                    'nat_test_report.mini_pool_id',
                    DB::raw('MAX(nat_test_report.status) as nat_status')
                ])
                ->leftJoin('bag_entries_mini_pools', 'bag_entries_mini_pools.mini_pool_number', '=', 'nat_test_report.mini_pool_id')
                ->leftJoin('bag_entries', 'bag_entries.id', '=', 'bag_entries_mini_pools.bag_entries_id')
                ->leftJoin('bag_entries_details', 'bag_entries_details.bag_entries_id', '=', 'bag_entries.id')
                ->where('nat_test_report.status', 'reactive');

            if ($pickupDate) {
                $query->whereDate('bag_entries.pickup_date', $pickupDate);
            }

            if ($bloodCentreName) {
                $query->where('bag_entries.blood_bank_id', $bloodCentreName);
            }

            $query->groupBy('nat_test_report.mini_pool_id')
                  ->orderBy('bag_entries.created_at', 'desc');

            \Log::info('Generated SQL query for reactive:', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);

            $results = $query->get();

            \Log::info('Query results for reactive:', [
                'results' => $results->toArray()
            ]);

            return $results;
        } catch (\Exception $e) {
            \Log::error('Error in getMiniPoolDetails: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public static function getNonReactiveMiniPoolDetails($bloodCentreName = null, $pickupDate = null)
    {
        try {
            \Log::info('Starting getNonReactiveMiniPoolDetails query:', [
                'bloodCentreName' => $bloodCentreName,
                'pickupDate' => $pickupDate
            ]);

            $query = DB::table('nat_test_report')
                ->select([
                    DB::raw('MAX(bag_entries.id) as id'),
                    DB::raw('MAX(bag_entries.blood_bank_id) as blood_bank_id'),
                    DB::raw('MAX(bag_entries.pickup_date) as pickup_date'),
                    DB::raw('MAX(bag_entries_details.donation_date) as donation_date'),
                    DB::raw('MAX(bag_entries_details.bag_volume_ml) as bag_volume_ml'),
                    DB::raw('MAX(bag_entries_details.blood_group) as blood_group'),
                    DB::raw('MAX(bag_entries_mini_pools.mini_pool_number) as mini_pool_number'),
                    'nat_test_report.mini_pool_id',
                    DB::raw('MAX(nat_test_report.status) as nat_status')
                ])
                ->leftJoin('bag_entries_mini_pools', 'bag_entries_mini_pools.mini_pool_number', '=', 'nat_test_report.mini_pool_id')
                ->leftJoin('bag_entries', 'bag_entries.id', '=', 'bag_entries_mini_pools.bag_entries_id')
                ->leftJoin('bag_entries_details', 'bag_entries_details.bag_entries_id', '=', 'bag_entries.id')
                ->where('nat_test_report.status', 'nonreactive');

            if ($pickupDate) {
                $query->whereDate('bag_entries.pickup_date', $pickupDate);
            }

            if ($bloodCentreName) {
                $query->where('bag_entries.blood_bank_id', $bloodCentreName);
            }

            $query->groupBy('nat_test_report.mini_pool_id')
                  ->orderBy('bag_entries.created_at', 'desc');

            \Log::info('Generated SQL query for non-reactive:', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);

            $results = $query->get();

            \Log::info('Query results for non-reactive:', [
                'results' => $results->toArray()
            ]);

            return $results;
        } catch (\Exception $e) {
            \Log::error('Error in getNonReactiveMiniPoolDetails: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public static function getBloodCentres()
    {
        try {
            \Log::info('Getting blood centres');
            $bloodCentres = DB::table('entities')
                ->select('id', 'name', 'city_id')
                ->where('entity_type_id', 2) // Blood Bank type
                ->where('account_status', 'active')
                ->orderBy('name')
                ->get()
                ->map(function ($item) {
                    $city = DB::table('cities')->where('id', $item->city_id)->first();
                    return [
                        'id' => $item->id,
                        'text' => $item->name . ' (' . ($city ? $city->name : '') . ')'
                    ];
                });

            \Log::info('Blood centres retrieved:', ['count' => count($bloodCentres)]);
            return $bloodCentres;
        } catch (\Exception $e) {
            \Log::error('Error in getBloodCentres: ' . $e->getMessage());
            return [];
        }
    }

    public static function getCities()
    {
        return \DB::table('blood_banks')
            ->select('city')
            ->where('status', 'active')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');
    }
} 