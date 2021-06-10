<?php

namespace CRM\API\Activity;

use App\Models\Actions\Activity;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

/**
 * ActivityRepository
 */
class ActivityRepository extends BaseRepository
{
    /**
     * __contruct
     *
     * @param Activity $model
     */
    public function __construct(Activity $model)
    {
        $this->model = $model;
    }

    /**
     * getAll
     *
     * @param [type] $request
     * @return void
     */
    public function getAll($request)
    {
        $activities = $this->model->select('activities.*')->get();

        return $activities;
    }

    /**
     * create
     *
     * @param array $data
     * @return void
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $activity = $this->model->create([
                'activity' => to_json_add(locale(), $data['activity'])
            ]);

            return $activity;
        });
    }

    /**
     * update
     *
     * @param [type] $id
     * @param array $data
     * @return Activity
     */
    public function update($id, array $data): Activity
    {
        return DB::transaction(function () use ($id, $data) {

            $lang = $data['locale'];
            $activity = $this->getById($id);
            $activity->activity = to_json_add($lang, $data['activity'], to_json_remove($lang, $activity->activity));
            $activity->save();

            return $activity;
        });
    }
}
