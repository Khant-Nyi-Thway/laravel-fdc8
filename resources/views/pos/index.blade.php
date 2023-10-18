@extends('pos.layouts.app')

@section('style')
@endsection

@section('navbar')    
    @parent
@endsection

@section('content')
    <div class="mb-3">            
        <input type="text" class="form-control" id="item-search" placeholder="Search item.">
    </div>
    <hr>
    <div id="items">
        <div class="row">
            @foreach($items as $item)   
            <div class="col-md-3">                        
                <div class="item text-center">
                    <img src="{{ asset('/storage/' . $item->item_image) }}" 
                    alt="{{ $item->item_code }}" style="height:200px;width:100%">
                    <p>{{ $item->item_name }}</p>
                    <p>{{ $item->item_code }}</p>
                    <p>{{ $item->purchase_price }}</p>
                    <button class="btn btn-primary add-to-cart" data-item-id="{{ $item->id }}">Add To Cart</button>
                </div>                    
            </div>
            @endforeach
        </div>
    </div>

    
@endsection

@section('script')
<script>
    $(document).ready(()=>{
        $('#item-search').focus();

        updateItemQtyOnLoad();

        $('#item-search').keydown((event)=>{
            let itemCode = $('#item-search').val()? $('#item-search').val(): 'empty' ;
            if(event.key == "Enter"){
                $.ajax({
                    type: "get",
                    dataType: "json",
                    url: `{{ url('/pos/search-item/${itemCode}' ) }}`,
                    success: (data)=>{
                        let items = data;
                        $('#items .row').empty();
                        items.forEach(item => {
                            let imagePath = "http://localhost:8000/storage/" + item.item_image;                                
                            let itemElement =   `<div class="col-md-3">                        
                                                    <div class="item text-center">
                                                        <img src="${imagePath}" 
                                                        alt="${item.item_code}" style="height:200px;width:100%">
                                                        <p>${item.item_name}</p>
                                                        <p>${item.item_code}</p>
                                                        <p>${item.purchase_price}</p>
                                                        <button class="btn btn-primary add-to-cart" data-item-id="${item.id}">Add To Cart</button>
                                                    </div>                    
                                                </div>`;
                            $('#items .row').append(itemElement);
                        });
                    },
                    error: (error)=>{
                        console.log(error);
                    }
                });
            }
        });            

        $(document).on('click','.add-to-cart',(event)=>{
            
            let user = @json(Auth()->user());
            if(user){
                let button = $(event.currentTarget);
                let itemId = button.data('item-id');
                $.ajax({
                    type:"post",
                    dataType: "json",
                    url: "{{ url('/pos/add-to-cart') }}",
                    data:{
                        itemId: itemId,
                        _token : "{{ csrf_token() }}"
                    },
                    success: (data)=>{
                        console.log(data);
                        $('#item-qty').html(data.length);
                    },
                    error: (error)=>{
                        console.log(error);
                    }
                });
            }
            else{
                alert("Please sign in.");
            }
        })

        function updateItemQtyOnLoad(){
            let cart = @json(session()->has('cart')? session()->get('cart') : [] );
            $('#item-qty').html(cart.length);
        }
    });
</script>
@endsection
