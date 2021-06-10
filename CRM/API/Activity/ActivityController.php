<?php

namespace CRM\API\Activity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CRM\API\Activity\ActivityRepository;
use Illuminate\Validation\ValidationException;

/**
 * ActivityController
 */
class ActivityController extends Controller
{
    protected $activityRepository;

    /**
     * __construct
     *
     * @param ActivityRepository $activityRepository
     */
    public function __construct(ActivityRepository $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    /**
     * index
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $activities = $this->activityRepository->getAll($request);

        return response()->json($activities);
    }

    /**
     * stored
     *
     * @param Request $request
     * @return void
     */
    public function stored(Request $request)
    {
        $request->validate([
            'activity' => 'required|min:2'
        ]);

        if (is_null($request->id)) {

            $arrayList = $this->activityRepository->getAll($request);
            $checked = is_data_exist('activity', $request->activity, get_data($request->locale, ['activity'], $arrayList));

            if ($checked) {
                throw ValidationException::withMessages(['activity' => $request->activity . ' is an existing Activity record.']);
            }

            $this->activityRepository->create($request->only('activity'));
        } else {
            $this->activityRepository->update($request->id, $request->only('activity', 'id', 'locale'));
        }

        return response()->json(true);
    }

    /**
     * destroy
     *
     * @param Request $request
     * @return void
     */
    public function destroy(Request $request)
    {
        $data = $this->activityRepository->getById($request->id);
        $data->delete();

        return response()->json(true);
    }
}
