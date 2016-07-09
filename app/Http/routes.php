<?php

Route::resource('client', 'ClientsController');
Route::resource('product', 'ProductsController');
Route::get('order/line', 'OrdersController@getDataForLine');
Route::resource('order', 'OrdersController');
Route::post('email', 'EmailController@send');