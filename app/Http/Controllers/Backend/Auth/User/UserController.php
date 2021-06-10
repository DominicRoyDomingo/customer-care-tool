<?php

namespace App\Http\Controllers\Backend\Auth\User;

use App\Events\Backend\Auth\User\UserDeleted;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Auth\User\ManageUserRequest;
use App\Http\Requests\Backend\Auth\User\StoreUserRequest;
use App\Http\Requests\Backend\Auth\User\UpdateUserRequest;
use App\Models\Auth\User;
use App\Models\Organization;
use App\Models\OrganizationUser;
use App\Models\Brand;
use App\Repositories\Backend\Auth\PermissionRepository;
use App\Repositories\Backend\Auth\RoleRepository;
use App\Repositories\Backend\Auth\UserRepository;
use App\Notifications\Backend\Auth\InviteUser;
use Illuminate\Http\Request;

/**
 * Class UserController.
 */
class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * UserController constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param ManageUserRequest $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(ManageUserRequest $request)
    {
        $organization = \Session::get('organization');

        return view('backend.auth.user.index')
            ->withUsers($this->userRepository->getActivePaginated(25, 'id', 'asc', $organization));
    }

    /**
     * @param ManageUserRequest    $request
     * @param RoleRepository       $roleRepository
     * @param PermissionRepository $permissionRepository
     *
     * @return mixed
     */
    public function create(ManageUserRequest $request, RoleRepository $roleRepository, PermissionRepository $permissionRepository)
    {
        return view('backend.auth.user.create')
            ->withRoles($roleRepository->with('permissions')->get(['id', 'name']))
            ->withPermissions($permissionRepository->get(['id', 'name']));
    }

    /**
     * @param StoreUserRequest $request
     *
     * @throws \Throwable
     * @return mixed
     */
    public function store(StoreUserRequest $request)
    {
        $user = $this->userRepository->create($request->only(
            'first_name',
            'last_name',
            'email',
            'password',
            'active',
            'confirmed',
            'confirmation_email',
            'roles',
            'permissions'
        ));

        $user->organizationUsers()->create([
            'organization' => \Session::get('organization')
        ]);

        return redirect()->route('admin.auth.user.index')->withFlashSuccess(__('alerts.backend.users.created'));
    }

    /**
     * @param ManageUserRequest $request
     * @param User              $user
     *
     * @return mixed
     */
    public function show(ManageUserRequest $request, User $user)
    {
        return view('backend.auth.user.show')
            ->withUser($user);
    }

    /**
     * @param ManageUserRequest    $request
     * @param RoleRepository       $roleRepository
     * @param PermissionRepository $permissionRepository
     * @param User                 $user
     *
     * @return mixed
     */
    public function edit(ManageUserRequest $request, RoleRepository $roleRepository, PermissionRepository $permissionRepository, User $user)
    {
        return view('backend.auth.user.edit')
            ->withUser($user)
            ->withRoles($roleRepository->get())
            ->withUserRoles($user->roles->pluck('name')->all())
            ->withPermissions($permissionRepository->get(['id', 'name']))
            ->withUserPermissions($user->permissions->pluck('name')->all());
    }

    /**
     * @param UpdateUserRequest $request
     * @param User              $user
     *
     * @throws \App\Exceptions\GeneralException
     * @throws \Throwable
     * @return mixed
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user = $this->userRepository->update($user, $request->only(
            'first_name',
            'last_name',
            'email',
            'roles',
            'permissions'
        ));

        return redirect()->route('admin.auth.user.index')->withFlashSuccess(__('alerts.backend.users.updated'));
    }

    /**
     * @param UpdateUserRequest $request
     * @param User              $user
     *
     * @throws \App\Exceptions\GeneralException
     * @throws \Throwable
     * @return mixed
     */
    public function inviteUser(Request $request)
    {
        $request->validate(
            [
                "email_invite" => "required|exists:users,email",
            ],
            [
                'email_invite.required'    => 'Email is required.',
                'email_invite.exists'      => 'Email is not registered.',
            ]
        );
        // dd('test');
        $organization = Organization::where('id', \Session::get('organization'))->first();
        // dd(\Session::get('organization'));
        $organization_exec_code = \Auth::user()->confirmation_code;
        $user = User::where('email', $request->email_invite)->first();
        // dd($user);
        $user->notify(new InviteUser($organization, $user->confirmation_code, $organization_exec_code));

        return response()->json(alert_success($request->email_invite));
    }

    public function switchOrg($organizationId)
    {

        $organization = OrganizationUser::where('user', Auth()->user()->id)->where('organization', $organizationId)->with('organizationModel')->first();

        $brands = Brand::where('organization', $organizationId)->get();
        if (!$brands->isEmpty()) {
            $brand = $brands->first(function ($brand) {
                return $brand->isDefault == 1;
            });
            if ($brand == null) {
                $brand = $brands->first();
            }
            $brandObj = array();
            $brandObj['id'] = $brand->id;
            $brandObj['name'] = $brand->name;
            $brandObj = collect($brandObj);
            \Session::put('brand', $brandObj);
        } else {
            \Session::forget('brand');
        }

        \Session::put('active_organization', $organization);
        $organization = json_decode($organization);
        \Session::put('organization', $organization->organization);

        return redirect()->back();
    }

    public function readNotif(Request $request)
    {
        $notif_id = $request->notif_id;

        $notif = \Auth::user()->notifications->where('id', $notif_id)->first();

        if ($notif == null) {
            return;
        }

        if ($notif->read_at == null) {
            $notif->markAsRead();

            return;
        }

        return;
    }

    public function userNotifs(Request $request)
    {
        $notifs = \Auth::user()->notifications()->get();
        $notifs->map(function ($notif) {
            $notif['isClicked'] = false;
            return $notif;
        });
        $unreadNotif = \Auth::user()->unreadNotifications()->get()->isEmpty();


        return response()->json(['notifs' => $notifs, 'unreadNotif' => $unreadNotif]);
    }

    public function inviteConfirm($organization, $token, $organization_exec_code, $notif_id)
    {
        $notif = \Auth::user()->notifications()->where('id', $notif_id);

        if ($notif->get()->isEmpty()) {
            abort(404);
        }

        $user = User::where('confirmation_code', $organization_exec_code)
            ->with('organizations')
            ->whereHas('organizations', function ($query) use ($organization) {
                return $query->where('id', '=', $organization);
            })->first();
        // dd($user);
        if ($user != null) {
            $user = $this->userRepository->confirmInvite($token, $organization);

            $notif->delete();

            return redirect()->back()->withFlashSuccess(__('alerts.backend.users.confirmed'));
        }
    }

    public function declineNotif($notif_id)
    {
        \Auth::user()->notifications()->where('id', $notif_id)->delete();

        return;
    }


    /**
     * @param ManageUserRequest $request
     * @param User              $user
     *
     * @throws \Exception
     * @return mixed
     */
    public function destroy(ManageUserRequest $request, User $user)
    {
        $this->userRepository->deleteById($user->id);

        event(new UserDeleted($user));

        return redirect()->route('admin.auth.user.deleted')->withFlashSuccess(__('alerts.backend.users.deleted'));
    }
}
