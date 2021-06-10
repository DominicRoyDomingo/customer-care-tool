<?php

namespace App\Listeners\Backend\Terms;

use App\Events\Backend\Terms\LinkedBrandEvent;
use DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LinkedBrandListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  LinkedBrandEvent  $event
     * @return void
     */
    public function handle(LinkedBrandEvent $event)
    {
        $tableName = $event->data['table_name'];
        $brandId = $event->data['brand_id'];
        $fieldKey = $event->data['field_key'];
        $brands = isset($event->data['brands']) ?  $event->data['brands'] : [];
        $value = isset($event->data['value']) ?  $event->data['value'] : null;
        $link = $event->data['link'];

        DB::beginTransaction();
        try {
            $brandType = DB::table($tableName);

            $this->remove_brands($tableName, $fieldKey, $value);

            if ($link) {
                $brandType->insert([
                    $fieldKey => $value,
                    'brand_id' => $brandId,
                    'created_at' => now(),
                ]);
            }

            if (!empty($brands)) {
                foreach ($brands as $brand) {
                    $brandType->insert([
                        $fieldKey => $value,
                        'brand_id' => $brand['id'],
                        'created_at' => now(),
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception  $e) {
            DB::rollback();
        }

        return true;
    }

    public function remove_brands($table, $field, $id)
    {
        $brandType = DB::table($table)->where($field, $id);

        if ($brandType->count() == 0) {
            return false;
        }

        $brandType->delete();

        return true;
    }
}
