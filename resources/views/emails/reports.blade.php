<table width="100%">
    <tr>
        <th>ID</th>
        <th>Client</th>
        <th>Product</th>
        <th>Total</th>
        <th>Date</th>
    </tr>
    @foreach ($table as $value)
    <tr>
        <td>{{$value['id']}}</td>
        <td>{{$value['client']['name']}}</td>
        <td>{{$value['product']['name']}}</td>
        <td>{{$value['total_price']}} {{$value['currency']}}</td>
        <td>{{date("Y-m-d", $value['created_at'])}}</td>
    </tr>
    @endforeach
</table>