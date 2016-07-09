<?php

namespace App\Repositories;

use Carbon\Carbon;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\OrderRepository;
use App\Entities\Order;
use App\Validators\OrderValidator;
use Illuminate\Support\Facades\Input;
use App\Entities\Client;
use App\Entities\Product;

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
                    ->select('orders.*')
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

    public function updateData($request, $id){
        $order = $this->find($id);

        $product = Product::where(['name' => $request['product_name']])->first()->toArray();

        $client = Client::firstOrCreate(['name' => $request['client_name']])->toArray();

        $order->product_id = $product['id'];
        $order->total_price = $product['price'];
        $order->currency = $product['currency'];
        $order->client_id = $client['id'];
        $order->created_at = $request['created_at'];
        
        $order->save();

        return $this->with(['client', 'product'])->find($id);
    }

    public function getDataForLine(){

        $orders = $this->searchAllField()->all();

        $ordersArray = $orders->toArray();

        if(empty($ordersArray)){
            abort(204);
        }

        $result['labels'] = array_unique($orders->lists('created_at')->toArray());
        sort($result['labels']);
        
        foreach ($ordersArray as $order){
            $result['series'][$order['client']['name']][$order['created_at']] = $order['total_price'];
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
