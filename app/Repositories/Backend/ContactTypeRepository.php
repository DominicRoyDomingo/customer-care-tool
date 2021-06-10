<?php

namespace App\Repositories\Backend;

use App\Exceptions\GeneralException;
use App\Models\ContactType;
use App\Notifications\Backend\Auth\UserAccountActive;
use App\Notifications\Frontend\Auth\UserNeedsConfirmation;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Class UserRepository.
 */
class ContactTypeRepository extends BaseRepository
{
    /**
     * UserRepository constructor.
     *
     * @param  User  $model
     */
    public function __construct(ContactType $model)
    {
        $this->model = $model;
    }

    /**
     * @param int    $paged
     * @param string $orderBy
     * @param string $sort
     *
     * @return mixed
     */
    public function allExtended()
    {
        $contact_types = $this->all();
        
        foreach($contact_types as $contactType){
            $contactType = $contactType->extendedInformation($contactType);
        }

        return $contact_types;
    }


    /**
     * @param int    $paged
     * @param string $orderBy
     * @param string $sort
     *
     * @return mixed
     */
    public function getPaginated($paged = 25, $orderBy = 'created_at', $sort = 'desc'): LengthAwarePaginator
    {
        return $this->model
            ->active()
            ->orderBy($orderBy, $sort)
            ->paginate($paged);
    }

    /**
     * @param int    $paged
     * @param string $orderBy
     * @param string $sort
     *
     * @return LengthAwarePaginator
     */
    public function getDeletedPaginated($paged = 25, $orderBy = 'created_at', $sort = 'desc'): LengthAwarePaginator
    {
        return $this->model
            ->with('roles', 'permissions', 'providers')
            ->onlyTrashed()
            ->orderBy($orderBy, $sort)
            ->paginate($paged);
    }

    /**
     * @param array $data
     *
     * @throws \Exception
     * @throws \Throwable
     * @return User
     */
    public function create(array $data): ContactType
    {
        return DB::transaction(function () use ($data) {
            $data["type_name"] = json_encode($data["type_name"]);
            
            $contact_type = $this->model::create([
                'type_name' => $data['type_name'],
            ]);


            if ($contact_type) {
                return $contact_type;
            }

            throw new GeneralException(__('exceptions.backend.access.users.create_error'));
        });
    }

    /**
     * @param User  $user
     * @param array $data
     *
     * @throws GeneralException
     * @throws \Exception
     * @throws \Throwable
     * @return User
     */
    public function update(ContactType $contact_type, array $data): ContactType
    {

        return DB::transaction(function () use ($contact_type, $data) {
            
            $data["type_name"] = json_encode($data["type_name"]);
            
            if ($contact_type->update([
                'type_name' => $data['type_name'],
            ])) {
                return $contact_type;
            }

            throw new GeneralException(__('exceptions.backend.access.contact_types.update_error'));
        });
    }

}
