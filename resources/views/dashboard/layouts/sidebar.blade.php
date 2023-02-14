
<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li class="user-pro">
                    <p class="userRole" style="text-align: center">
                        {{ Auth::user()->fName }} {{ Auth::user()->lName }}
                        <br> {{ Auth::user()->getRole('name') }}
                    </p>

                    <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                        <img src="{{ getSrc(Auth::user(), 'image') }}" alt="{{ Auth::user()->name }}" class="img-circle">
                        <span id="authId" authId="{{ Auth::id() }}" class="hide-menu"> {{ Auth::user()->name }} </span>
                    </a>

                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('mainUser.profile') }}"><i class="ti-user"></i> {{ trans('main.My Profile') }}</a></li>
                        {{-- <li><a href="javascript:void(0)"><i class="ti-wallet"></i> My Balance</a></li>
                        <li><a href="javascript:void(0)"><i class="ti-email"></i> Inbox</a></li>
                        <li><a href="javascript:void(0)"><i class="ti-settings"></i> Account Setting</a></li>
                        <li><a href="javascript:void(0)"><i class="fa fa-power-off"></i> Logout</a></li> --}}
                        <li>
                            <a  href="javascript:void(0)" onclick="event.preventDefault();
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
                                class="fas fa-key"></i><span class="hide-menu">الصلاحيات</span></a>
                        <ul aria-expanded="false" class="collapse">
                            <li><a href="{{ route('permissions.index') }}">كل الصلاحيات</a></li>
                            <li><a href="{{ route('permissions.create') }}">إنشاء صلاحية</a></li>
                        </ul>
                    </li>

                    <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                                class="fas fa-briefcase"></i><span class="hide-menu">الرتب</span></a>
                        <ul aria-expanded="false" class="collapse">
                            <li><a href="{{ route('roles.index') }}">كل الرتب</a></li>
                            <li><a href="{{ route('roles.create') }}">إنشاء رتبة</a></li>
                        </ul>
                    </li>
                @endif

                @if(Auth::user()->can(['manageusers']))
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                            class="fas fa-user"></i><span class="hide-menu">إدارة الأعضاء</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('manageusers.index') }}">كل الأعضاء</a></li>
                        <li><a href="{{ route('manageusers.create') }}">إضافة عضو</a></li>
                    </ul>
                </li>
                @endif

                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                            class="fas fa-cart-plus"></i><span class="hide-menu">نقاط البيع</span></a>
                    <ul aria-expanded="false" class="collapse">
                        @if(Auth::user()->can(['Create Vouchers']))
                        <li><a href="{{ route('vouchers.create') }}">إنشاء إذن صرف</a></li>
                        @endif

                        @if(Auth::user()->voucher && Auth::user()->can('Create Invoices') && !Auth::user()->voucher->user_keeper_return_id && !Auth::user()->voucher->user_accountant_return_id && Auth::user()->voucher->voucher_status == 3)
                        <li><a href="{{ route('invoices.create', Auth::user()->voucher->id) }}" class="">إنشاء فاتورة مبيعات</a></li>
                        @endif

                        {{-- @if(Auth::user()->voucher && Auth::user()->can('settlement_request') && Auth::user()->voucher->voucher_status == 3  && $canRequire) --}}
                        @if(Auth::user()->voucher && Auth::user()->can('settlement_request') && Auth::user()->voucher->voucher_status == 3)
                        <li><a href="{{ route('vouchers.show', Auth::user()->voucher->id) }}" class="">طلب تسوية اذن صرف</a>
                        @endif

                        <li><a href="{{ route('vouchers.index') }}">كل اذنات الصرف</a></li>
                        <li><a href="{{ route('vouchers.indexok') }}">أذونات تم التسوية</a></li>

                        @if(Auth::user()->can(['Show_his_invoices']) || Auth::user()->can(['View_all_invoices']))
                        <li><a href="{{ route('invoices.index', 'all') }}">كل فواتير المبيعات</a></li>
                        <li><a href="{{ route('invoices.index', 'wait') }}">فواتير لم يتم الموافقة عليها</a></li>
                        <li><a href="{{ route('invoices.index', 'next') }}">فواتير غير محصلة</a></li>
                        <li><a href="{{ route('invoices.index', 'paid') }}">فواتير محصلة</a></li>
                        @endif
                        <li><a href="{{ route('returns.index') }}">كل المرتجعات</a></li>
                        <li><a href="{{ route('returns.indexReturnProducts') }}">  كل المرتجعات بالمنتجات</a></li>

                    </ul>
                </li>

                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                            class="fas fa-box-open"></i><span class="hide-menu">المخازن</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('stores.index') }}"> كل  المخازن</a></li>
                        <li><a href="{{ route('storestocks.stock') }}"> جرد المخازن</a></li>
                        

                        @if(Auth::user()->can(['Cud stores']))
                        <li><a href="{{ route('stores.create') }}">إنشاء مخزن فرعي</a></li>
                        @endif

                        @if(Auth::user()->can(['CRUD Transfers']) || Auth::user()->can(['index_his_store_transfers']))
                            @if(Auth::user()->can(['CRUD Transfers']))
                            <li><a href="{{ route('transfers.create') }}">إنشاء تحويل مخزني</a></li>
                            @endif
                            <li><a href="{{ route('transfers.index') }}">كل التحويلات المخزنية</a></li>
                        @endif

                    </ul>
                </li>

                @if(Auth::user()->can(['CRUD Products']) || Auth::user()->can(['CRUD Productsales']))
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                            class="fas fa-capsules"></i><span class="hide-menu">الأصناف </span></a>
                    <ul aria-expanded="false" class="collapse">
                        @if(Auth::user()->can(['CRUD Products']))
                        <li><a href="{{ route('products.index') }}">تكويد الأصناف</a></li>
                        <li><a href="{{ route('productsales.indexAll') }}">كل الأصناف</a></li>
                        @endif

                        @if(Auth::user()->can(['CRUD Productsales']))
                        <li><a href="{{ route('productsales.index') }}">سياسة البيع</a></li>
                        @endif
                    </ul>
                </li>
                @endif

                @if(Auth::user()->can(['Inpermit_Outpermit_Supplier']))
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                            class="fas fa-parachute-box"></i><span class="hide-menu"> الموردين </span></a>
                    <ul aria-expanded="false" class="collapse">
                        @if(Auth::user()->can(['CRUD Suppliers']))
                        <li><a href="{{ route('suppliers.create') }}">إضافة مورد</a></li>
                        @endif

                        @if(Auth::user()->can(['Inpermit_Create']))
                        <li><a href="{{  route('inpermits.create') }}">إنشاء فاتورة مشتريات</a></li>
                        @endif

                        @if(Auth::user()->can(['Outpermit_Create']))
                        <li><a href="{{ route('outpermits.find') }}">إنشاء مردود مشتريات</a></li>
                        @endif

                        @if(Auth::user()->can(['CRUD Suppliers']))
                        <li><a href="{{ route('suppliers.index') }}">كل الموردين</a></li>
                        @endif

                        @if(Auth::user()->can(['Inpermit_Index']))
                        <li><a href="{{ route('inpermits.index') }}">فواتير المشتريات</a></li>
                        @endif

                        @if(Auth::user()->can(['Outpermit_Index']))
                        <li><a href="{{ route('outpermits.index') }}"> مردودات المشتريات</a></li>
                        @endif

                    </ul>
                </li>
                @endif


                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                            class="fas fa-people-carry"></i><span class="hide-menu">العملاء<span
                                class="badge badge-pill badge-cyan ml-auto"></span></span></a>
                    <ul aria-expanded="false" class="collapse">
                        @if(Auth::user()->can(['CRUD Clients']) || Auth::user()->can(['add_first_client']))
                        <li><a href="{{ route('clients.create') }}">إضافة عميل</a></li>
                        <li><a href="{{ route('clients.index') }}">كل العملاء</a></li>
                        @endif
                        @if(Auth::user()->can(['Accounting']))
                        <li><a href="{{ route('clients.accounting') }}">    كشف حساب</a></li>
                        @endif
                        @if(Auth::user()->can(['Create gets']))
                        <li><a href="{{ route('gets.newget') }}">تحصيل/مرتجع</a></li>
                        @endif
                    </ul>
                </li>


                @if(Auth::user()->can(['CRUD Regions']))
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i
                            class="fas fa-map-marker-alt"></i><span class="hide-menu">المناطق<span
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

                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                    <i class="fas fa-credit-card"></i><span class="hide-menu">الماليات<span
                                class="badge badge-pill badge-cyan ml-auto"></span></span></a>
                    <ul aria-expanded="false" class="collapse">
                        @if(Auth::user()->usergets)
                        <li><a href="{{ route('reports.usergets') }}">صندوق المندوب</a></li>
                        @endif

                        @if(Auth::user()->can(['Shaw_all_safemoney']))
                        <li><a href="{{ route('reports.usergetsall') }}">تحصيلات المندوبين</a></li>
                        @endif

                        @if(Auth::user()->can(['CRUD_transaction']))
                        <li><a href="{{ route('transactions.create') }}">إنشاء تحويل مالي </a></li>
                        @endif

                        @if(Auth::user()->can(['CRUD_transaction']) || Auth::user()->can(['show_all_transactions']))
                        <li><a href="{{ route('transactions.index') }}">إيداعات المندوبين</a></li>
                        @endif

                        @if(Auth::user()->can(['CRUD_transaction']) || Auth::user()->can(['main_safer_admin']))
                        <li><a href="{{ route('spends.create') }}">إنشاء مصروف</a></li>
                        @endif

                        @if(Auth::user()->can(['CRUD_transaction']) || Auth::user()->can(['show_all_transactions']))
                        <li><a href="{{ route('spends.index') }}">كل المصروفات</a></li>
                        @endif

                        @if(Auth::user()->can(['CRUD_banktransfer']))
                        <li><a href="{{ route('banktransfers.create') }}">إنشاء تحويل بين الحسابات المالية</a></li>
                        <li><a href="{{ route('banktransfers.index') }}">كل تحويلات الحسابات المالية</a></li>
                        @endif

                        @if(Auth::user()->can(['CRUD_inbank']))
                        <li><a href="{{ route('inbanks.create') }}">إنشاء إيداع مالي خارجي</a></li>
                        <li><a href="{{ route('inbanks.index') }}">كل الإيداعات المالية الخارجية</a></li>
                        @endif

                        @if(Auth::user()->can(['CRUD_banks']))
                        <li><a href="{{ route('banks.create') }}">أضف حساب مالي</a></li>
                        @endif

                        @if(Auth::user()->can(['CRUD_banks']))
                        <li><a href="{{ route('banks.index') }}">كل الحسابات المالية</a></li>
                        @endif

                        @if(Auth::user()->can(['CRUD_cats']))
                        <li><a href="{{ route('cats.create') }}">أضف فئة مصروف</a></li>
                        @endif

                        @if(Auth::user()->can(['CRUD_cats']))
                        <li><a href="{{ route('cats.index') }}">كل فئات المصروفات</a></li>
                        @endif
                    </ul>
                </li>

                @if(Auth::user()->can(['Reports']) || Auth::user()->can(['show_usersreport']))
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                    <i class="fas fa-clipboard"></i><span class="hide-menu">التقارير<span
                                class="badge badge-pill badge-cyan ml-auto"></span></span></a>
                    <ul aria-expanded="false" class="collapse">
                        @if(Auth::user()->can(['show_usersreport']))
                        <li><a href="{{ route('reports.usersreport') }}">تقرير المندوبين</a></li>
                        <li><a href="{{ route('reports.sqlcheckdiff') }}">فحص اتزان الجرد</a></li>
                        @endif
                        
                        <li><a href="{{ route('reports.repprocesses') }}">عمليات المندوب</a></li>

                        
                        @if(Auth::user()->can(['Reports_Accounting']))
                        <li><a href="{{ route('reports.clientbalancesum') }}"> كشف أرصدة العملاء</a></li>
                        @endif
                        
                        @if(Auth::user()->can(['Reports_His_Invoiceget']))
                        <li><a href="{{ route('reports.invoiceget') }}">تقرير المبيعات - التحصيل</a></li>
                        @endif
                        
                        @if(Auth::user()->can(['show_reports']))
                        <li><a href="{{ route('reports.checkquantities') }}"> كشف أرصدة الأصناف بالشركة</a></li>
                        <li><a href="{{ route('reports.getsreport') }}">تقرير التحصيل</a></li>


                        <li><a href="{{ route('reports.clientbalance') }}"> كشف حساب عميل</a></li>
                        <li><a href="{{ route('reports.bankbalance') }}"> كشف حساب بنكي</a></li>
                        <li><a href="{{ route('reports.catbalance') }}"> كشف حساب المصروفات</a></li>
                        <li><a href="{{ route('reports.productcount') }}">أرصدة الأصناف بالمخازن</a></li>
                        <li><a href="{{ route('reports.storeproducttransfer') }}">حركة الأصناف بالمخازن</a></li>
                        @endif
                    </ul>
                </li>

                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                    <i class="fas fa-universal-access"></i><span class="hide-menu">سياسات البيع<span
                                class="badge badge-pill badge-cyan ml-auto"></span></span></a>
                    <ul aria-expanded="false" class="collapse">
                        @if(Auth::user()->can(['show_reports']))
                        <li><a href="{{ route('generalpolicys.edit', 1) }}">سياسات عامة</a></li>
                        <li><a href="{{ route('userpolicys.index') }}">سياسات المندوبين</a></li>
                        <li><a href="{{ route('regionpolicys.edit') }}">سياسات المناطق</a></li>
                        <li><a href="{{ route('productpolicys.index') }}">سياسات الأصناف</a></li>
                        <li><a href="{{ route('clientpolicys.index') }}">سياسات العملاء</a></li>
                        @endif
                    </ul>
                </li>
                @endif
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
