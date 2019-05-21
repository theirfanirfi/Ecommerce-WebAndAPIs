@extends('Frontend.MasterLayout')
@section('content')
            <!-- ========================================= CONTENT ========================================= -->
            <section id="checkout-page">
                    <div class="container">
                        <div class="col-xs-12 no-margin">

                            <div class="billing-address">
                                <h2 class="border h1">billing address</h2>
                                <form>
                                    <div class="row field-row">
                                        <div class="col-xs-12 col-sm-6">
                                            <label>Full name*</label>
                                            <input class="le-input" name="name" value="{{ $user->name }}">
                                        </div>
                                    </div><!-- /.field-row -->

                                    <div class="row field-row">
                                        <div class="col-xs-12">
                                            <label>company name</label>
                                            <input class="le-input" name="company" >
                                        </div>
                                    </div><!-- /.field-row -->

                                    <div class="row field-row">
                                        <div class="col-xs-12 col-sm-6">
                                            <label>address*</label>
                                            <input class="le-input" data-placeholder="street address" name="address" >
                                        </div>
                                        <div class="col-xs-12 col-sm-6">
                                            <label>&nbsp;</label>
                                            <input class="le-input" data-placeholder="town" name="citytown">
                                        </div>
                                    </div><!-- /.field-row -->

                                    <div class="row field-row">
                                        <div class="col-xs-12 col-sm-4">
                                            <label>postcode / Zip*</label>
                                            <input class="le-input" name="postcode" >
                                        </div>
                                        <div class="col-xs-12 col-sm-4">
                                            <label>email address*</label>
                                            <input class="le-input" name="email" value="{{ $user->email }}">
                                        </div>

                                        <div class="col-xs-12 col-sm-4">
                                            <label>phone number*</label>
                                            <input class="le-input" name="phone">
                                        </div>
                                    </div><!-- /.field-row -->



                                </form>
                            </div><!-- /.billing-address -->



                            <section id="your-order">
                                <h2 class="border h1">your order</h2>
                                <form>
                                        @if(!empty($products))
                                        @foreach ($products as $p)
                                    <div class="row no-margin order-item">
                                        <div class="col-xs-12 col-sm-1 no-margin">
                                            <a href="{{ route('removeproductcart',['id' => $p->product_id]) }}" class="qty">{{  $quantities[$p->product_id] }} X</a>
                                        </div>

                                        <div class="col-xs-12 col-sm-9 ">
                                            <div class="title"><a href="{{ route('product',['id' => $p->product_id]) }}">{{ $p->product_name }} </a></div>
                                            <div class="brand">{{ $p->getCat() }}</div>
                                        </div>

                                        <div class="col-xs-12 col-sm-2 no-margin">
                                            <div class="price">   ${{ $p->product_price * $quantities[$p->product_id] }}</div>
                                        </div>
                                    </div><!-- /.order-item -->
                                    @endforeach
                                    @endif
                                </form>
                            </section><!-- /#your-order -->

                            <div id="total-area" class="row no-margin">
                                <div class="col-xs-12 col-lg-4 col-lg-offset-8 no-margin-right">
                                    <div id="subtotal-holder">
                                        <ul class="tabled-data inverse-bold no-border">
                                            <li>
                                                <label>cart subtotal</label>
                                                <div class="value ">$@if(Session()->has('total_cart_cost')){{ Session()->get('total_cart_cost') }} @else 0 @endif</div>
                                            </li>
                                         <!--   <li>
                                                <label>shipping</label>
                                                <div class="value">
                                                    <div class="radio-group">
                                                        <input class="le-radio" type="radio" name="group1" value="free"> <div class="radio-label bold">free shipping</div><br>
                                                        <input class="le-radio" type="radio" name="group1" value="local" checked>  <div class="radio-label">local delivery<br><span class="bold">$15</span></div>
                                                    </div>
                                                </div>
                                            </li> -->
                                        </ul><!-- /.tabled-data -->

                                        <ul id="total-field" class="tabled-data inverse-bold ">
                                            <li>
                                                <label>order total</label>
                                                <div class="value">$@if(Session()->has('total_cart_cost')){{ Session()->get('total_cart_cost') }} @else 0 @endif</div>
                                            </li>
                                        </ul><!-- /.tabled-data -->

                                    </div><!-- /#subtotal-holder -->
                                </div><!-- /.col -->
                            </div><!-- /#total-area -->

                            <div id="payment-method-options">
                                <form>

                                    <div class="payment-method-option">
                                        <input class="le-radio" type="radio" name="group2" value="paypal" checked>
                                        <div class="radio-label bold ">paypal system (payment method)</div>
                                    </div><!-- /.payment-method-option -->
                                </form>
                            </div><!-- /#payment-method-options -->

                            <div class="place-order-button">
                                <a href="{{ route('placeorder') }}" class="le-button big">place order</a>
                            </div><!-- /.place-order-button -->

                        </div><!-- /.col -->
                    </div><!-- /.container -->
                </section><!-- /#checkout-page -->
                <!-- ========================================= CONTENT : END ========================================= -->

@endsection
