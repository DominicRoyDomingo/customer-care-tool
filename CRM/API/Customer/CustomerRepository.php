<?php

namespace CRM\API\Customer;

use App\Helpers\General\ImageHelper;
use App\Models\Customer;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class CustomerRepository extends BaseRepository
{
   public function __construct(Customer $model)
   {
      $this->model = $model;
   }

   public function getAll()
   {
      $customers = $this->model->all();
      
      return  $customers;
   }

   public function create(array $data)
   {
      return DB::transaction(function () use ($data) {
         $request = request();

         $customer = $this->model->create([
            'profile_id' => $data['profile_id']
         ]);

         return $customer;
      });
   }

}
