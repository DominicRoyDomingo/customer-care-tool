<?php

namespace App\Repositories\Backend;

use App\Exceptions\GeneralException;
use App\Models\Brand;
use App\Notifications\Backend\Auth\UserAccountActive;
use App\Notifications\Frontend\Auth\UserNeedsConfirmation;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Class UserRepository.
 */
class BrandRepository extends BaseRepository
{
    /**
     * UserRepository constructor.
     *
     * @param  User  $model
     */
    public function __construct(Brand $model)
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
        $brands = $this
        ->orderBy('name')
        ->get();
        foreach($brands as $brand){
            $brand = $brand->extendedInformation($brand);
        }

        return $brands;
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
    public function create(array $data): Brand
    {
        return DB::transaction(function () use ($data) {
            
            $brand = $this->model::create([
                'name' => $data['name'],
                'logo' => $data['logo'],
                'website' => $data['website'],
            ]);


            if ($brand) {
                return $brand;
            }

            throw new GeneralException(__('exceptions.backend.access.brands.create_error'));
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
    public function update(Brand $brand, array $data): Brand
    {

        return DB::transaction(function () use ($brand, $data) {
            
            if ($brand->update([
                'name' => $data['name'],
                'logo' => $data['logo'],
                'website' => $data['website'],
            ])) {
                return $brand;
            }

            throw new GeneralException(__('exceptions.backend.access.brands.update_error'));
        });
    }

}
