@extends('pos.layouts.app')

@section('style')
    <style>
        td{
            vertical-align: middle !important;
        }
    </style>
@endsection

@section('navbar')    
    @parent
@endsection

@section('content')
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Item Image</th>
                <th scope="col">Item Code</th>
                <th scope="col">Item Name</th>
                <th scope="col">Qty</th>
                <th scope="col">Unit Price</th>
                <th scope="col">Price</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody id="tbody">
            @foreach($cartItems as $item)
            <tr>                
                <td> 
                    <img width="100px" height="100px" 
                    src="{{ asset('/storage/' . $item->item_image) }}" alt="item-image">
                </td>
                <td>
                    {{ $item->item_code }}
                </td>
                <td>
                    {{ $item->item_name }}
                </td>
                <td>
                    {{ $item->qty }}
                </td>
                <td>
                    {{ $item->purchase_price }}
                </td>
                <td>
                    {{ $item->qty * $item->purchase_price }}
                </td>
                <td>
                    <button class="btn btn-danger btn-remove-item" 
                    data-id="{{ $item->id }}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="4"></td>
                <td class="fw-bold">Total Price</td>
                <td id="total-price"></td>
                <td></td>
            </tr>        
        </tbody>
    </table>   
    <div class="d-flex justify-content-between">
        <button class="btn btn-danger" id="btn-empty-cart">
            <i class="fas fa-trash-alt"></i>&nbsp;
            Empty Cart
        </button>
        <button class="btn btn-primary" id="btn-checkout">
            <i class="fas fa-money-check"></i>&nbsp;
            Checkout
        </button>
    </div> 
    
@endsection

@section('script')

<script>
    $(document).ready(()=>{      
        updateItemQtyOnLoad();   
        updateTotalPrice();   

        function updateItemQtyOnLoad(){
            let cart = @json(session()->has('cart')? session()->get('cart') : [] );
            $('#item-qty').html(cart.length);
        }

        function updateTotalPrice(){
            let cart = @json(session()->has('cart')? session()->get('cart') : [] );
            let totalPrice =  0;
            cart.forEach(item => {
                console.log(item);
                totalPrice += item.purchase_price * item.qty;
            });
            $('#total-price').html(totalPrice);            
        }

        $('#btn-empty-cart').click(()=>{
            $.ajax({
                type: "post",
                url: "{{ url('/pos/empty-cart') }}",
                data: {
                    "_token" : "{{ csrf_token() }}"
                },
                success: (data)=>{
                    console.log(data);
                    $('#tbody').empty();
                    $('#item-qty').html(0);
                },
                error: (error)=>{
                    console.log(error);
                }

            });
        });

        $('.btn-remove-item').click((event)=>{
            let removeButton = $(event.currentTarget);
            let itemId = removeButton.data('id');
            $.ajax({
                type: "post",
                url: "{{ url('/pos/remove-item') }}",
                data: {
                    "_token" : "{{ csrf_token() }}",
                    'id': itemId
                },
                success: (data)=>{                    
                    removeButton.parent().parent().remove();
                    $('#item-qty').html(data.length);                    
                    //calculate total price
                    let totalPrice =  0;
                    data.forEach(item => {
                        console.log(item);
                        totalPrice += item.purchase_price * item.qty;
                    });
                    $('#total-price').html(totalPrice);       
                    //end
                },
                error: (error)=>{
                    console.log(error);
                }

            });
        });

        $('#btn-checkout').click((event)=>{
            let totalPrice =   $('#total-price').html();
            $.ajax({
                type: "post",
                url: "{{ url('/pos/checkout') }}",
                data: {
                    "_token" : "{{ csrf_token() }}",
                    'total_price': totalPrice
                },
                success: (data)=>{                    
                    alert("you make the order successfully");
                    window.location.replace("http://localhost:8000/pos");
                },
                error: (error)=>{
                    console.log(error);
                }

            });
        });
    });
</script>
@endsection
