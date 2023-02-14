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
                                class="icon-speedometer"></i><span class="hide-menu">الصلاحيات<span
                                    class="badge badge-pill badge-cyan ml-auto">{{ Spatie\Permission\Models\Permission::all()->count() }}</span></span></a>
                        <ul aria-expanded="false" class="collapse">
                            <li><a href="{{ route('permissions.index') }}">كل الصلاحيات</a></li>
                            <li><a href="{{ route('permissions.create') }}">إنشاء صلاحية</a></li>
                        </ul>
                    </li>

                    <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                                class="icon-speedometer"></i><span class="hide-menu">الرتب<span
                                    class="badge badge-pill badge-cyan ml-auto">{{ Spatie\Permission\Models\Role::all()->count() }}</span></span></a>
                        <ul aria-expanded="false" class="collapse">
                            <li><a href="{{ route('roles.index') }}">كل الرتب</a></li>
                            <li><a href="{{ route('roles.create') }}">إنشاء رتبة</a></li>
                        </ul>
                    </li>
                @endif

                @if(Auth::user()->can(['manageusers']))
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                            class="icon-speedometer"></i><span class="hide-menu">إدارة الأعضاء <span
                                class="badge badge-pill badge-cyan ml-auto">{{ App\Models\User::all()->count() }}</span></span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('manageusers.index') }}">كل الأعضاء</a></li>
                        <li><a href="{{ route('manageusers.create') }}">إضافة عضو</a></li>
                    </ul>
                </li>
                @endif

                @if(Auth::user()->can(['CRUD Suppliers']))
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                            class="icon-speedometer"></i><span class="hide-menu">إدارة الموردين <span
                                class="badge badge-pill badge-cyan ml-auto">{{ App\Models\Supplier::all()->count() }}</span></span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('suppliers.index') }}">كل الموردين</a></li>
                        <li><a href="{{ route('suppliers.create') }}">إضافة مورد</a></li>
                    </ul>
                </li>
                @endif

                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                            class="icon-speedometer"></i><span class="hide-menu">إدارة المخازن <span
                                class="badge badge-pill badge-cyan ml-auto">{{ App\Models\Store::all()->count() }}</span></span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('stores.index') }}"> كل  المخازن</a></li>
                        <li><a href="{{ route('stores.stock') }}"> جرد المخازن</a></li>

                        @if(Auth::user()->can(['Cud stores']))
                        <li><a href="{{ route('stores.create') }}">إضافة مخزن فرعي</a></li>
                        @endif

                        @if(Auth::user()->can(['CRUD Inpermits']) || Auth::user()->can(['CRUD Outpermits']))
                        <li> <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">فواتير الموردين</a>
                            <ul aria-expanded="false" class="collapse">
                                @if(Auth::user()->can(['CRUD Inpermits']))
                                <li><a href="{{ route('inpermits.index') }}">فواتير الشراء من الموردين</a></li>
                                @endif

                                @if(Auth::user()->can(['CRUD Outpermits']))
                                <li><a href="{{ route('outpermits.find') }}">إنشاء فاتورة إرتجاع للمورد</a></li>
                                <li><a href="{{ route('outpermits.index') }}">كل فواتير الإرتجاع</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif

                        @if(Auth::user()->can(['CRUD Transfers']) || Auth::user()->can(['index_his_store_transfers']))
                        <li> <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">التحويل بين المخازن</a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="{{ route('transfers.index') }}">كل التحويلات</a></li>

                                @if(Auth::user()->can(['CRUD Transfers']))
                                <li><a href="{{ route('transfers.create') }}">إنشاء أمر تحويل بين المخازن</a></li>
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
                        <span class="hide-menu">إذونات الصرف<span class="badge badge-pill badge-cyan ml-auto">{{ $count }}</span></span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('vouchers.index') }}">كل الأذونات</a></li>
                        @if(Auth::user()->can(['Create Vouchers']))
                        <li><a href="{{ route('vouchers.create') }}">إنشاء إذن صرف</a></li>
                        @endif
                    </ul>
                </li>
                @if(Auth::user()->can(['Show_his_invoices']) || Auth::user()->can(['View_all_invoices']))
                <li>
                    <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                        <i class="icon-speedometer"></i>
                        <span class="hide-menu">الفواتير<span class="badge badge-pill badge-cyan ml-auto"></span></span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('invoices.index', 'all') }}">كل الفواتير</a></li>
                        <li><a href="{{ route('invoices.index', 'wait') }}">فواتير لم يتم الموافقة عليها</a></li>
                        <li><a href="{{ route('invoices.index', 'next') }}">فواتير غير محصلة</a></li>
                        <li><a href="{{ route('invoices.index', 'paid') }}">فواتير محصلة</a></li>
                    </ul>
                </li>
                @endif

                @if(Auth::user()->can(['CRUD Products']) || Auth::user()->can(['CRUD Productsales']))
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                            class="icon-speedometer"></i><span class="hide-menu">إدارة الأصناف <span
                                class="badge badge-pill badge-cyan ml-auto">{{ App\Models\Product::all()->count() }}</span></span></a>
                    <ul aria-expanded="false" class="collapse">
                        @if(Auth::user()->can(['CRUD Products']))
                        <li><a href="{{ route('productsales.indexAll') }}">كل الأصناف</a></li>
                        <li><a href="{{ route('products.index') }}">تكويد الأصناف</a></li>
                        @endif

                        @if(Auth::user()->can(['CRUD Productsales']))
                        <li><a href="{{ route('productsales.index') }}">سياسة البيع</a></li>
                        @endif
                    </ul>
                </li>
                @endif

                @if(Auth::user()->can(['CRUD Clients']))
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                            class="icon-speedometer"></i><span class="hide-menu">إدارة العملاء <span
                                class="badge badge-pill badge-cyan ml-auto"></span></span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('clients.index') }}">كل العملاء</a></li>
                        <li><a href="{{ route('clients.create') }}">إضافة عميل</a></li>
                        <li><a href="{{ route('clients.accounting') }}">كشف حساب</a></li>
                    </ul>
                </li>
                @endif

                @if(Auth::user()->can(['CRUD Regions']))
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                            class="icon-speedometer"></i><span class="hide-menu">إدارة المناطق <span
                                class="badge badge-pill badge-cyan ml-auto"></span></span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('states.index') }}">كل المحافظات</a></li>
                        <li><a href="{{ route('states.create') }}">إضافة محافظة</a></li>

                        <li><a href="{{ route('cities.index') }}">كل المدن</a></li>
                        <li><a href="{{ route('cities.create') }}">إضافة مدينة</a></li>

                        <li><a href="{{ route('regions.index') }}">كل المناطق</a></li>
                        <li><a href="{{ route('regions.create') }}">إضافة منطقة</a></li>
                    </ul>
                </li>
                @endif

                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                            class="icon-speedometer"></i><span class="hide-menu">التقارير<span
                                class="badge badge-pill badge-cyan ml-auto"></span></span></a>
                    <ul aria-expanded="false" class="collapse">
                        @if(Auth::user()->usergets)
                        <li><a href="{{ route('reports.usergets') }}">صندوق المندوب</a></li>
                        @endif
                    </ul>
                </li>

            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
