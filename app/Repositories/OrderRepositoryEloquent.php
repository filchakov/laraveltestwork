<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\OrderRepository;
use App\Entities\Order;
use App\Validators\OrderValidator;
use Illuminate\Support\Facades\Input;
/**
 * Class OrderRepositoryEloquent
 * @package namespace App\Repositories;
 */
class OrderRepositoryEloquent extends BaseRepository implements OrderRepository
{
    protected $fieldSearchable = [
        'total_price',
        'currency',
        'created_at',

        'client.id',
        'client.name' => 'like',

        'product.id',
        'product.name' => 'like',
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Order::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return OrderValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function searchAllField(){

        list($fieldSearch, $valueSearch) = explode(':', Input::get('search', ':'));

        $this->scopeQuery(function($query) use ($fieldSearch, $valueSearch){
            if($fieldSearch == 'all'){
                return $query
                    ->join('clients', 'orders.client_id', '=', 'clients.id')
                    ->join('products', 'orders.product_id', '=', 'products.id')
                    ->orWhere('orders.total_price', '=', $valueSearch)
                    ->orWhere('clients.name', 'like', '%'.$valueSearch.'%')
                    ->orWhere('products.name', 'like', '%'.$valueSearch.'%')
                    ->orWhere('orders.currency', '=', $valueSearch)
                    ->orWhere('orders.created_at', '=', $valueSearch);
            } else {
                return $query;
            }
        });
        return $this;
    }

    public function getDataForLine(){

        $orders = $this->searchAllField()->orderBy('orders.id', 'asc')->all();

        $ordersArray = $orders->toArray();

        if(empty($ordersArray)){
            abort(204);
        }

        $result['labels'] = array_unique($orders->lists('created_at')->toArray());

        foreach ($ordersArray as $order){
            $result['series'][$order['client_name']][$order['created_at']] = $order['total_price'];
        }

        foreach ($result['series'] as $client => $price){
            foreach ($result['labels'] as $labelTime){
                if(empty($price[$labelTime])){
                    $result['series'][$client][$labelTime] = 0;
                }
                ksort($result['series'][$client]);
            }
        }

        return $result;
    }
}
