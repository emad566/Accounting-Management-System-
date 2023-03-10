<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li class="user-pro">
                    <p class="" style="text-align: center">{{ Auth::user()->getRole('name') }}</p>
                    <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                        <img src="{{ getSrc(Auth::user(), 'image') }}" alt="{{ Auth::user()->name }}" class="img-circle">
                        <span class="hide-menu"> {{ Auth::user()->name }} </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('mainUser.profile') }}"><i class="ti-user"></i> {{ trans('main.My Profile') }}</a></li>
                        {{-- <li><a href="javascript:void(0)"><i class="ti-wallet"></i> My Balance</a></li>
                        <li><a href="javascript:void(0)"><i class="ti-email"></i> Inbox</a></li>
                        <li><a href="javascript:void(0)"><i class="ti-settings"></i> Account Setting</a></li>
                        <li><a href="javascript:void(0)"><i class="fa fa-power-off"></i> Logout</a></li> --}}
                        <li>
                            <a class="logoutLink" href="javascript:void(0)" onclick="event.preventDefault();
                                localStorage.clear();
                                $('#logOutForm2').submit();"
                            ><i class="fa fa-power-off"></i> {{ trans('main.Logout') }}</a>
                            <form id="logOutForm2" method="POST" action="{{ (Auth::guard('admin')->check()) ? route('admin.logout') : route('logout') }}">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
                {{-- <li class="nav-small-cap">--- PERSONAL</li> --}}

                @if(Auth::user()->can(['SupperAdmin']))
                    <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                                class="icon-speedometer"></i><span class="hide-menu">??????????????????<span
                                    class="badge badge-pill badge-cyan ml-auto">{{ Spatie\Permission\Models\Permission::all()->count() }}</span></span></a>
                        <ul aria-expanded="false" class="collapse">
                            <li><a href="{{ route('permissions.index') }}">???? ??????????????????</a></li>
                            <li><a href="{{ route('permissions.create') }}">?????????? ????????????</a></li>
                        </ul>
                    </li>

                    <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                                class="icon-speedometer"></i><span class="hide-menu">??????????<span
                                    class="badge badge-pill badge-cyan ml-auto">{{ Spatie\Permission\Models\Role::all()->count() }}</span></span></a>
                        <ul aria-expanded="false" class="collapse">
                            <li><a href="{{ route('roles.index') }}">???? ??????????</a></li>
                            <li><a href="{{ route('roles.create') }}">?????????? ????????</a></li>
                        </ul>
                    </li>
                @endif

                @if(Auth::user()->can(['manageusers']))
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                            class="icon-speedometer"></i><span class="hide-menu">?????????? ?????????????? <span
                                class="badge badge-pill badge-cyan ml-auto">{{ App\Models\User::all()->count() }}</span></span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('manageusers.index') }}">???? ??????????????</a></li>
                        <li><a href="{{ route('manageusers.create') }}">?????????? ??????</a></li>
                    </ul>
                </li>
                @endif

                @if(Auth::user()->can(['CRUD Suppliers']))
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                            class="icon-speedometer"></i><span class="hide-menu">?????????? ???????????????? <span
                                class="badge badge-pill badge-cyan ml-auto">{{ App\Models\Supplier::all()->count() }}</span></span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('suppliers.index') }}">???? ????????????????</a></li>
                        <li><a href="{{ route('suppliers.create') }}">?????????? ????????</a></li>
                    </ul>
                </li>
                @endif

                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                            class="icon-speedometer"></i><span class="hide-menu">?????????? ?????????????? <span
                                class="badge badge-pill badge-cyan ml-auto">{{ App\Models\Store::all()->count() }}</span></span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('stores.index') }}"> ????  ??????????????</a></li>
                        <li><a href="{{ route('stores.stock') }}"> ?????? ??????????????</a></li>

                        @if(Auth::user()->can(['Cud stores']))
                        <li><a href="{{ route('stores.create') }}">?????????? ???????? ????????</a></li>
                        @endif

                        @if(Auth::user()->can(['CRUD Inpermits']) || Auth::user()->can(['CRUD Outpermits']))
                        <li> <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">???????????? ????????????????</a>
                            <ul aria-expanded="false" class="collapse">
                                @if(Auth::user()->can(['CRUD Inpermits']))
                                <li><a href="{{ route('inpermits.index') }}">???????????? ???????????? ???? ????????????????</a></li>
                                @endif

                                @if(Auth::user()->can(['CRUD Outpermits']))
                                <li><a href="{{ route('outpermits.find') }}">?????????? ???????????? ???????????? ????????????</a></li>
                                <li><a href="{{ route('outpermits.index') }}">???? ???????????? ????????????????</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif

                        @if(Auth::user()->can(['CRUD Transfers']) || Auth::user()->can(['index_his_store_transfers']))
                        <li> <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">?????????????? ?????? ??????????????</a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="{{ route('transfers.index') }}">???? ??????????????????</a></li>

                                @if(Auth::user()->can(['CRUD Transfers']))
                                <li><a href="{{ route('transfers.create') }}">?????????? ?????? ?????????? ?????? ??????????????</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif

                    </ul>
                </li>


                <li>
                    <?php
                        $count = '';
                        if(Auth::user()->can(['Shaw all Vouchers'])){
                            $count = App\Models\Voucher::orderBy('voucher_status', 'ASC')->count();
                        }elseif(Auth::user()->can(['Delegate'])){
                            $count = Auth::user()->vouchers->count();
                        }elseif(Auth::user()->can(['Keeper'])){
                            $count = App\Models\Voucher::where('voucher_status', '>', 1)->whereIn('store_id', Auth::user()->stores->pluck('id'))->orderBy('voucher_status', 'ASC')->count();
                        }
                    ?>
                    <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                        <i class="icon-speedometer"></i>
                        <span class="hide-menu">???????????? ??????????<span class="badge badge-pill badge-cyan ml-auto">{{ $count }}</span></span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('vouchers.index') }}">???? ????????????????</a></li>
                        @if(Auth::user()->can(['Create Vouchers']))
                        <li><a href="{{ route('vouchers.create') }}">?????????? ?????? ??????</a></li>
                        @endif
                    </ul>
                </li>
                @if(Auth::user()->can(['Show_his_invoices']) || Auth::user()->can(['View_all_invoices']))
                <li>
                    <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                        <i class="icon-speedometer"></i>
                        <span class="hide-menu">????????????????<span class="badge badge-pill badge-cyan ml-auto"></span></span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('invoices.index', 'all') }}">???? ????????????????</a></li>
                        <li><a href="{{ route('invoices.index', 'wait') }}">???????????? ???? ?????? ???????????????? ??????????</a></li>
                        <li><a href="{{ route('invoices.index', 'next') }}">???????????? ?????? ??????????</a></li>
                        <li><a href="{{ route('invoices.index', 'paid') }}">???????????? ??????????</a></li>
                    </ul>
                </li>
                @endif

                @if(Auth::user()->can(['CRUD Products']) || Auth::user()->can(['CRUD Productsales']))
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                            class="icon-speedometer"></i><span class="hide-menu">?????????? ?????????????? <span
                                class="badge badge-pill badge-cyan ml-auto">{{ App\Models\Product::all()->count() }}</span></span></a>
                    <ul aria-expanded="false" class="collapse">
                        @if(Auth::user()->can(['CRUD Products']))
                        <li><a href="{{ route('productsales.indexAll') }}">???? ??????????????</a></li>
                        <li><a href="{{ route('products.index') }}">?????????? ??????????????</a></li>
                        @endif

                        @if(Auth::user()->can(['CRUD Productsales']))
                        <li><a href="{{ route('productsales.index') }}">?????????? ??????????</a></li>
                        @endif
                    </ul>
                </li>
                @endif

                @if(Auth::user()->can(['CRUD Clients']))
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                            class="icon-speedometer"></i><span class="hide-menu">?????????? ?????????????? <span
                                class="badge badge-pill badge-cyan ml-auto"></span></span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('clients.index') }}">???? ??????????????</a></li>
                        <li><a href="{{ route('clients.create') }}">?????????? ????????</a></li>
                        <li><a href="{{ route('clients.accounting') }}">?????? ????????</a></li>
                    </ul>
                </li>
                @endif

                @if(Auth::user()->can(['CRUD Regions']))
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                            class="icon-speedometer"></i><span class="hide-menu">?????????? ?????????????? <span
                                class="badge badge-pill badge-cyan ml-auto"></span></span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('states.index') }}">???? ??????????????????</a></li>
                        <li><a href="{{ route('states.create') }}">?????????? ????????????</a></li>

                        <li><a href="{{ route('cities.index') }}">???? ??????????</a></li>
                        <li><a href="{{ route('cities.create') }}">?????????? ??????????</a></li>

                        <li><a href="{{ route('regions.index') }}">???? ??????????????</a></li>
                        <li><a href="{{ route('regions.create') }}">?????????? ??????????</a></li>
                    </ul>
                </li>
                @endif

                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                            class="icon-speedometer"></i><span class="hide-menu">????????????????<span
                                class="badge badge-pill badge-cyan ml-auto"></span></span></a>
                    <ul aria-expanded="false" class="collapse">
                        @if(Auth::user()->usergets)
                        <li><a href="{{ route('reports.usergets') }}">?????????? ??????????????</a></li>
                        @endif
                    </ul>
                </li>

            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
