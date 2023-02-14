<?php
use App\Models\Get;
use App\Models\ViewStockClosedSql;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([
    'namespace' => 'App\Http\Controllers',
], function () {;

    // For Documentation Visit Link:
    // https://laracasts.com/discuss/channels/laravel/localize-app-with-jetstream
    include('routesFrtifyJetstream.php');
    Route::get('send_notification', 'HomeController@send_notification')->name('send_notification');
    Route::get('sendnotify', 'HomeController@sendnotify')->name('sendnotify');

    Route::get('clients/{id}', function ($id) {
        $client_id = $id;
        $get = Get::whereHas('invoice', function ($q) use($client_id)
        {
            return $q->where('client_id', $client_id);
        });

        // if($request->get_date_from){
        //     $get = $get
        //     ->where('get_date', '>=', $request->get_date_from);
        // }

        // if($request->get_date_to){
        //     $get = $get
        //     ->where('get_date', '<=', $request->get_date_to);
        // }

        $get = $get->selectRaw('sum(IFNULL(client_pay, 0)) as client_pay')->first();
        $client_pay = $get->client_pay;
        return $client_pay;
    });

    //================ Email Verify =================//
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->middleware('auth')->name('verification.notice');
    //================ /Email Verify =================//

    //================ webGuard Routes =================//
    Route::post('adminlogin', 'manageusersController@adminlogin')->name('manageusers.adminlogin');
    Route::get('adminlogin', 'manageusersController@adminloginget')->name('manageusers.adminloginget');
    
    
    Route::post('logintoken', 'MainUserController@logintoken')->name('mainUser.logintoken');
    
    
    Route::get('userlogin', 'MainUserController@userlogin')->name('mainUser.userlogin');
    Route::get('userloged', 'MainUserController@userloged')->name('mainUser.userloged');

    Route::group(['prefix' => 'dashboard', 'middleware' => ['auth:sanctum', 'verified', UpdateSession::class]], function () {
    // Route::group(['prefix' => 'dashboard', 'middleware' => ['auth:sanctum', 'verified', 'UpdateSession']], function () {
        // Route::middleware(['IsForceTransactionMiddleware'])->group(function () {
        Route::get('/', function () {
            // return $_SERVER['SERVER_NAME'] == 'test.marvel-inter.com';
            
            return view('dashboard.index');
        })->name('dashboard');

        // }); //IsForceTransactionMiddleware

        //================ MainUser =================//
        Route::get('/user/logout', 'MainUserController@logout')->name('mainUser.getlogout');
        // Route::post('/user/logout', 'MainUserController@logout')->name('mainUser.logout');
        Route::get('/user/profile', 'MainUserController@profile')->name('mainUser.profile');
        Route::put('/user/profile', 'MainUserController@update')->name('mainUser.update');
        Route::get('/user/changePass', 'MainUserController@changePass')->name('mainUser.changePass');
        Route::put('/user/updatePass', 'MainUserController@updatePass')->name('mainUser.updatePass');
        Route::post('/usertokens', 'manageusersController@usertokens')->name('manageusers.usertokens');

        //================ /MainUser =================//

        Route::middleware(['can:SupperAdmin'])->group(function () {
            //================ permissions =================//
            Route::resource('permissions', 'permissionsController');
            Route::get('permissions/{permission_id?}/delete', 'permissionsController@destroy')->name('permissions.destroy');
            Route::post('permissions/delete', 'permissionsController@delete')->name('permissions.delete');
            //================ /permissions =================//

            //================ roles =================//
            Route::resource('roles', 'rolesController');
            Route::get('roles/{role_id?}/delete', 'rolesController@destroy')->name('roles.destroy');
            Route::post('roles/delete', 'rolesController@delete')->name('roles.delete');
            //================ /roles =================//
        });

        //================ manageusers =================//
        Route::middleware(['can:manageusers'])->group(function () {
            Route::resource('manageusers', 'manageusersController');
            Route::get('manageusers/{manageuser_id?}/delete', 'manageusersController@destroy')->name('manageusers.destroy');
            Route::post('manageusers/delete', 'manageusersController@delete')->name('manageusers.delete');
            Route::get('manageusersUpdateIsActive/{user}', 'manageusersController@updateIsActive')->name('manageusers.updateIsActive');

            Route::get('/manageusers/changePass/{user_id}', 'manageusersController@changePass')->name('manageusers.changePass');
            Route::put('/manageuser/updatePass', 'manageusersController@updatePass')->name('manageusers.updatePass');
            Route::get('/manageusersreports/pdf', 'manageusersController@displayReport')->name('manageusers.displayReport');
        });
        Route::middleware(['can:loginUser'])->group(function () {
            Route::get('/manageusersauth/loginById/{user}', 'manageusersController@loginById')->name('manageusers.loginById');
        });
        //================ /manageusers =================//

        //================ stores =================//
        // Route::resource('stores', 'storesController');
        Route::get('stores', 'storesController@index')->name('stores.index');

        Route::middleware(['can:Cud stores'])->group(function () {
            Route::get('stores/create', 'storesController@create')->name('stores.create');
            Route::post('stores', 'storesController@store')->name('stores.store');

            Route::get('stores/{store}/edit', 'storesController@edit')->name('stores.edit');
            Route::put('stores/{store}', 'storesController@update')->name('stores.update');

            Route::get('stores/{store_id?}/delete', 'storesController@destroy')->name('stores.destroy');
            Route::post('stores/delete', 'storesController@delete')->name('stores.delete');

            Route::get('storesUpdateIsActive/{store_id}', 'storesController@updateIsActive')->name('stores.updateIsActive');
        });

        Route::get('stores/{store}', 'storesController@show')->name('stores.show');
        Route::get('storestocks/stock/{store_id?}/{view?}', 'storestocksController@stock')->name('storestocks.stock');
        //================ /stores =================//

        //================ suppliers =================//
        Route::middleware(['can:CRUD Suppliers'])->group(function () {
            Route::resource('suppliers', 'suppliersController');
            Route::get('suppliers/{supplier_id?}/delete', 'suppliersController@destroy')->name('suppliers.destroy');
            Route::post('suppliers/delete', 'suppliersController@delete')->name('suppliers.delete');
            Route::get('suppliersUpdateIsActive/{supplier_id}', 'suppliersController@updateIsActive')->name('suppliers.updateIsActive');
        });
        //================ /suppliers =================//

        //================ products =================//
        Route::middleware(['can:CRUD Products'])->group(function () {
            Route::resource('products', 'productsController');
            Route::get('products/{product_id?}/delete', 'productsController@destroy')->name('products.destroy');
            Route::post('products/delete', 'productsController@delete')->name('products.delete');
            Route::get('productsUpdateIsActive/{product_id}', 'productsController@updateIsActive')->name('products.updateIsActive');
        });
        //================ /products =================//

        //================ productsales =================//
        Route::middleware(['can:CRUD Productsales'])->group(function () {
            Route::resource('productsales', 'productsalesController')->only(['index', 'edit', 'update']);
            Route::get('productsales-indexAll', 'productsalesController@indexAll')->name('productsales.indexAll');
        });
        //================ /productsales =================//

        Route::middleware(['can:Inpermit_Outpermit'])->group(function () {
            //================ inpermits =================//
            // Route::resource('inpermits', 'inpermitsController');

            Route::middleware(['can:Inpermit_Index'])->group(function () {
                Route::get('inpermits', 'inpermitsController@index')->name('inpermits.index');
            });  
            Route::middleware(['can:Inpermit_Create'])->group(function () {
                Route::get('inpermits/create', 'inpermitsController@create')->name('inpermits.create');
                Route::post('inpermits', 'inpermitsController@store')->name('inpermits.store');
            });  
            
            Route::middleware(['can:Inpermit_Show'])->group(function () {
                Route::get('inpermits/{inpermit}', 'inpermitsController@show')->name('inpermits.show');
            });  
            
            Route::middleware(['can:Inpermit_Edit'])->group(function () {
                Route::get('inpermits/{inpermit}/edit', 'inpermitsController@edit')->name('inpermits.edit');
                Route::put('inpermits/{inpermit}', 'inpermitsController@update')->name('inpermits.update');
            });  
           
            Route::middleware(['can:Inpermit_Delete'])->group(function () {
                Route::get('inpermits/{inpermit_id?}/delete', 'inpermitsController@destroy')->name('inpermits.destroy');
                Route::post('inpermits/delete', 'inpermitsController@delete')->name('inpermits.delete');
            });  
            
            Route::get('inpermit/{pro_id}/{runID}', 'inpermitsController@runIDcheck')->name('inpermits.runIDcheck');
            Route::get('inpermitsgetrunid/{pro}', 'inpermitsController@getrunid')->name('inpermits.getrunid');
            //================ /inpermits =================//

            //================ outpermits =================//
            // Route::resource('outpermits', 'outpermitsController')->except(['create']);
            Route::middleware(['can:Outpermit_Index'])->group(function () {
                Route::get('outpermits', 'outpermitsController@index')->name('outpermits.index');
            });  
            Route::middleware(['can:Outpermit_Create'])->group(function () {
                Route::get('outpermit/create/{inpermit_id}', 'outpermitsController@create')->name('outpermits.create');
                Route::get('outpermit/find', 'outpermitsController@find')->name('outpermits.find');
                Route::post('outpermit/find_post', 'outpermitsController@find_post')->name('outpermits.find_post');
                Route::post('outpermits', 'outpermitsController@store')->name('outpermits.store');
            });  
            
            Route::middleware(['can:Outpermit_Show'])->group(function () {
                Route::get('outpermits/{outpermit}', 'outpermitsController@show')->name('outpermits.show');
            });  
            
            Route::middleware(['can:Outpermit_Edit'])->group(function () {
                Route::get('outpermits/{outpermit}/edit', 'outpermitsController@edit')->name('outpermits.edit');
            });  
            
            Route::middleware(['can:Outpermit_Update'])->group(function () {
                Route::put('outpermits/{outpermit}', 'outpermitsController@update')->name('outpermits.update');
            });  
           
            Route::middleware(['can:Outpermit_Delete'])->group(function () {
                Route::get('outpermits/{outpermit_id?}/delete', 'outpermitsController@destroy')->name('outpermits.destroy');
                Route::post('outpermits/delete', 'outpermitsController@delete')->name('outpermits.delete');
            });  
            //================ /outpermits =================//
        });

        Route::middleware(['IsForceTransactionMiddleware'])->group(function () {
            //================ transfers =================//
            Route::middleware(['can:CRUD Transfers'])->group(function () {
                Route::resource('transfers', 'transfersController');
                // Route::put('transfers/{transfer}', 'transfersController@update')->name('transfers.update');
                Route::get('transfers/{transfer_id?}/delete', 'transfersController@destroy')->name('transfers.destroy');
                Route::get('transfers/{transfer}', 'transfersController@show')->name('transfers.show');
                Route::get('transfers/{transfer}/edit', 'transfersController@edit')->name('transfers.edit');
                Route::post('transfers/delete', 'transfersController@delete')->name('transfers.delete');
                Route::get('transfer/fromto/{store_id}/{transfer?}', 'transfersController@fromto')->name('transfers.fromto');
            });

            Route::get('transfer/{store_id}/{product_id}/getrunids/{transfer?}', 'transfersController@getrunids')->name('transfers.getrunids');
            Route::get('transfer/{product_id}/{runID}/{store_id}/{transfer?}', 'transfersController@runIDQuantity')->name('transfers.runIDQuantity');

            Route::middleware(['can:show_his_store_transfers'])->group(function () {
                Route::get('transfers/{transfer}', 'transfersController@show')->name('transfers.show');
            });

            Route::middleware(['can:index_his_store_transfers'])->group(function () {
                Route::get('yajratransfers', 'transfersController@yajratransfers')->name('transfers.yajratransfers');
                Route::get('transfers', 'transfersController@index')->name('transfers.index');
                Route::get('transfers/{transfer}', 'transfersController@show')->name('transfers.show');
            });

            Route::middleware(['can:change_transfer_status'])->group(function () {
                Route::get('transferStatus/{transfer}/{status_id}', 'transfersController@changestatus')->name('transfers.changestatus');
            });
            //================ /transfers =================//

            //================ vouchers =================//
            Route::get('voucher/quantities/{voucher}', 'vouchersController@quantities')->name('vouchers.quantities');
            Route::get('voucher/invoice/{invoice}', 'vouchersController@invoice')->name('vouchers.invoice');
            Route::get('vouchers', 'vouchersController@index')->name('vouchers.index');
            Route::get('yajravouchers', 'vouchersController@yajravouchers')->name('vouchers.yajravouchers');

            Route::get('vouchersok', 'vouchersController@indexok')->name('vouchers.indexok');
            Route::get('yajravouchersok', 'vouchersController@yajravouchersok')->name('vouchers.yajravouchersok');

            Route::middleware(['can:Create Vouchers'])->group(function () {
                Route::get('vouchers/create', 'vouchersController@create')->name('vouchers.create');
                Route::post('vouchers', 'vouchersController@store')->name('vouchers.store');
            });

            Route::get('vouchers/{voucher}/{laod?}', 'vouchersController@show')->name('vouchers.show');
            Route::get('vouchers/{voucher}/edit', 'vouchersController@edit')->name('vouchers.edit');
            Route::put('vouchers/{voucher}', 'vouchersController@update')->name('vouchers.update');

            Route::get('vouchers/{voucher?}/delete', 'vouchersController@destroy')->name('vouchers.destroy');
            Route::post('vouchers/delete', 'vouchersController@delete')->name('vouchers.delete');
            Route::get('voucher/fromto/{store_id}', 'vouchersController@fromto')->name('vouchers.fromto');

            Route::middleware(['can:Accept Or Refuse Vouchers'])->group(function () {
                Route::get('voucher/accept/{voucher}', 'vouchersController@accept')->name('vouchers.accept');
                Route::get('voucher/refuse/{voucher}', 'vouchersController@refuse')->name('vouchers.refuse');
            });

            Route::get('voucher/accountantreturn/{voucher}', 'vouchersController@accountantreturn')->name('vouchers.accountantreturn');

            Route::middleware(['can:settlement_request'])->group(function () {
                Route::get('voucher/settlement_request/{voucher}', 'vouchersController@settlement_request')->name('vouchers.settlement_request');
            });

            Route::middleware(['can:Keeper Out Accept'])->group(function () {
                Route::get('voucher/keeperaccept/{voucher}', 'vouchersController@keeperaccept')->name('vouchers.keeperaccept');
            });

            Route::middleware(['can:Review Keeper Vouchers'])->group(function () {
                Route::get('voucher/keeperreturn/{voucher}', 'vouchersController@keeperreturn')->name('vouchers.keeperreturn');
            });

            Route::middleware(['can:openVoucher'])->group(function () {
                Route::get('voucher/openVoucher/{voucher}', 'vouchersController@openVoucher')->name('vouchers.openVoucher');
            });

            Route::get('voucher/{store_id}/{product_id}/getrunids/{voucher?}', 'vouchersController@getrunids')->name('vouchers.getrunids');
            Route::get('voucher/{product_id}/{runID}/{store_id}/{voucher?}', 'vouchersController@runIDQuantity')->name('vouchers.runIDQuantity');


            //================ /vouchers =================//

            //================ invoices =================//
            // Route::resource('invoices', 'invoicesController');
            Route::middleware(['can:Create Invoices'])->group(function () {
                Route::get('invoice/{voucher}/create/{payType?}', 'invoicesController@create')->name('invoices.create');
                Route::post('invoices', 'invoicesController@store')->name('invoices.store');
                // Route::get('invoices', 'invoicesController@store')->name('invoices.store');
            });

            Route::middleware(['can:list_linke_invoices'])->group(function () {
                Route::get('yajrainvoices/{next}', 'invoicesController@yajrainvoices')->name('invoices.yajrainvoices');

                Route::get('invoices/{invoice}', 'invoicesController@show')->name('invoices.show');
                Route::get('invoices_code/{invoice}', 'invoicesController@showcode')->name('invoices.showcode');

                Route::get('showinvoices/{next?}', 'invoicesController@index')->name('invoices.index');
            });

            Route::middleware(['can:change_invoice_status'])->group(function () {
                // Create invoice for each client for speciefic user
                Route::get('repclientsreset/{user}', 'invoicesController@repclientsreset')->name('invoices.repclientsreset');
                Route::get('excelinvoices/{user}', 'invoicesController@excelinvoices')->name('invoices.excelinvoices');
                Route::get('exceltransfer', 'transfersController@exceltransfer')->name('invoices.exceltransfer');
                
                Route::get('invoices/{invoice}/changestatus/{status}/{api?}', 'invoicesController@changestatus')->name('invoices.changestatus');
            });

            Route::middleware(['can:update_invoice_date'])->group(function () {
                Route::get('invoices/edit/date/{invoice}', 'invoicesController@editdate')->name('invoices.editdate');
                Route::put('invoices/update/date/{invoice}', 'invoicesController@updatedate')->name('invoices.updatedate');
            });

            Route::get('invoices/{invoice}/delete', 'invoicesController@destroy')->name('invoices.destroy');
            // Route::get('invoices/{city_id?}/delete', 'invoicesController@destroy')->name('invoices.destroy');
            Route::post('invoices/delete', 'invoicesController@delete')->name('invoices.delete');
            Route::get('invoice/getrunid/{product_id}/{voucher_id}/{client_id?}/{invoice_id?}', 'invoicesController@getrunid')->name('invoices.getrunid');
            Route::get('invoice/{product_id}/{runID}/{voucher_id}/{json?}', 'invoicesController@runIDQuantity')->name('invoices.runIDQuantity');
            Route::get('invoice/{product_id}/{runID}/{invoice_id}/edit/{json?}', 'invoicesController@runIDQuantityEdit')->name('invoices.runIDQuantity');

            Route::get('invoices/{invoice}/edit/{payType?}', 'invoicesController@edit')->name('invoices.edit');

            Route::put('invoices/{invoice}/{payType?}', 'invoicesController@update')->name('invoices.update');
            //================ /invoices =================//

            //================ gets =================//
            Route::middleware(['can:Create gets'])->group(function () {
                Route::get('gets/{invoice}/create/{pay_type?}', 'getsController@create')->name('gets.create');
                Route::post('gets', 'getsController@store')->name('gets.store');
                Route::get('zerowalit/{client}', 'getsController@zeroWalit')->name('gets.zeroWalit');
                Route::get('zerowalitall', 'getsController@zeroWalitAll')->name('gets.zeroWalitAll');
                
                
                Route::get('get/newgetinvoice/{invoice_id?}', 'getsController@newgetinvoice')->name('gets.newgetinvoice');
                Route::get('get/newget', 'getsController@newget')->name('gets.newget');
                Route::get('get/newgetdata/{client}/{json?}', 'getsController@newgetdata')->name('gets.newgetdata');
                Route::post('get/newget/{client?}', 'getsController@storenewget')->name('gets.storenewget');
            });

            Route::middleware(['can:Delete_get'])->group(function () {
                Route::get('gets/{get_id}/delete', 'getsController@delete')->name('gets.delete');
                Route::post('get/deleteget', 'getsController@deleteget')->name('gets.deleteget');
            });


            //================ /gets =================//

            //================ returns =================//
            Route::middleware(['can:Create_return'])->group(function () {
                Route::get('allreturns', 'returnsController@index')->name('returns.index');
                Route::get('indexReturnProducts', 'returnsController@indexReturnProducts')->name('returns.indexReturnProducts');
                Route::get('allreturns/{id}', 'returnsController@show')->name('returns.show');
                Route::get('yajrareturns', 'returnsController@yajrareturns')->name('returns.yajrareturns');
                Route::get('yajraindexReturnProducts', 'returnsController@yajraindexReturnProducts')->name('returns.yajraindexReturnProducts');

                Route::get('returns/{invoice}/create', 'returnsController@create')->name('returns.create');
                Route::post('returns', 'returnsController@store')->name('returns.store');
            });
        }); // End IsForceTransactionMiddleware
        //================ /returns =================//
        //================ regions states  cities =================//
        Route::middleware(['can:CRUD Regions'])->group(function () {
            Route::resource('states', 'statesController')->except(['edit', 'update']);
            Route::resource('cities', 'citiesController')->except(['edit', 'update']);
            Route::resource('regions', 'regionsController')->except(['edit', 'update']);
        });

        Route::middleware(['can:Regions_EditDel'])->group(function () {
            Route::resource('states', 'statesController')->only(['edit', 'update']);
            Route::resource('cities', 'citiesController')->only(['edit', 'update']);
            Route::resource('regions', 'regionsController')->only(['edit', 'update']);

            Route::get('states/{state_id?}/delete', 'statesController@destroy')->name('states.destroy');
            Route::post('states/delete', 'statesController@delete')->name('states.delete');

            Route::get('cities/{city_id?}/delete', 'citiesController@destroy')->name('cities.destroy');
            Route::post('cities/delete', 'citiesController@delete')->name('cities.delete');

            Route::get('regions/{city_id?}/delete', 'regionsController@destroy')->name('regions.destroy');
            Route::post('regions/delete', 'regionsController@delete')->name('regions.delete');
        });
        //================ /regions states  cities =================//

        //================ regions Select =================//
        Route::get('slect/regions/cities', 'regionsController@cities')->name('regions.cities');
        Route::get('slect/cities/regions', 'regionsController@regions')->name('cities.regions');
        //================ /regions Select =================//

        //================ clients =================//
        Route::middleware(['can:add_first_client'])->group(function () {
            Route::resource('clients', 'clientsController');
            Route::get('yajraclients', 'clientsController@yajraclients')->name('clients.yajraclients');
        });

        Route::middleware(['can:CRUD Clients'])->group(function () {
            Route::get('clients/{client_id?}/delete', 'clientsController@destroy')->name('clients.destroy');
            Route::post('clients/delete', 'clientsController@delete')->name('clients.delete');
            Route::get('clientsUpdateIsActive/{client_id}', 'clientsController@updateIsActive')->name('clients.updateIsActive');
        });
        Route::get('client/getOverPriceSum/{client}', 'clientsController@getOverPriceSum')->name('clients.getOverPriceSum');

        Route::middleware(['can:Accounting'])->group(function () {
            Route::get('client/accounting/{client_id?}', 'clientsController@accounting')->name('clients.accounting');
        });

        Route::middleware(['can:Accounting'])->group(function () {
            Route::post('client/accounting/{client_id?}', 'clientsController@accounting')->name('clients.accountingPost');
            Route::get('client/getclients', 'clientsController@getclients')->name('clients.getclients');
        });
        //================ /clients =================//

        //================ cats =================//
        Route::middleware(['can:CRUD_cats'])->group(function () {
            Route::resource('cats', 'catsController');
            Route::get('cats/{cat_id?}/delete', 'catsController@destroy')->name('cats.destroy');
            Route::post('cats/delete', 'catsController@delete')->name('cats.delete');
        });
        //================ /cats =================//

        //================ banks =================//
        Route::middleware(['can:CRUD_banks'])->group(function () {
            Route::resource('banks', 'banksController');
            Route::get('banks/{bank_id?}/delete', 'banksController@destroy')->name('banks.destroy');
            Route::post('banks/delete', 'banksController@delete')->name('banks.delete');
        });
        //================ /banks =================//

        //================ transactions =================//
        Route::middleware(['can:CRUD_transaction'])->group(function () {
            // Route::resource('transactions', 'transactionsController')->except(['destroy']);
            Route::get('transactions/create', 'transactionsController@create')->name('transactions.create');
            Route::post('transactions/', 'transactionsController@store')->name('transactions.store');
            Route::get('transactions/{transaction}/edit', 'transactionsController@edit')->name('transactions.edit');
            Route::put('transactions/{transaction}', 'transactionsController@update')->name('transactions.update');

            Route::get('transactions/{transaction_id?}/delete', 'transactionsController@destroy')->name('transactions.destroy');
            Route::post('transactions/delete', 'transactionsController@delete')->name('transactions.delete');
        });

        Route::middleware(['can:change_transaction_status'])->group(function () {
            Route::get('transactions/{transaction}/changestatus/{status}', 'transactionsController@changestatus')->name('transactions.changestatus');
        });

        Route::middleware(['can:show_his_transactions'])->group(function () {
            Route::get('transactions/', 'transactionsController@index')->name('transactions.index');
            Route::get('transactions/{transaction}', 'transactionsController@show')->name('transactions.show');
        });
        //================ /transactions =================//

        //================ spends =================//
        Route::middleware(['can:CRUD_spend'])->group(function () {
            Route::get('spends/create', 'spendsController@create')->name('spends.create');
            Route::post('spends/', 'spendsController@store')->name('spends.store');
            Route::get('spends/{spend}/edit', 'spendsController@edit')->name('spends.edit');
            Route::put('spends/{spend}', 'spendsController@update')->name('spends.update');

            Route::get('spends/{spend_id?}/delete', 'spendsController@destroy')->name('spends.destroy');
            Route::post('spends/delete', 'spendsController@delete')->name('spends.delete');
        });

        Route::middleware(['can:change_spend_status'])->group(function () {
            Route::get('spends/{spend}/changestatus/{status}', 'spendsController@changestatus')->name('spends.changestatus');
        });

        Route::middleware(['can:show_his_spends'])->group(function () {
            Route::get('spends/', 'spendsController@index')->name('spends.index');
            Route::get('spends/{spend}', 'spendsController@show')->name('spends.show');
        });
        //================ /spends =================//

        //================ banktransfers =================//
        Route::middleware(['can:CRUD_banktransfer'])->group(function () {
            Route::resource('banktransfers', 'banktransfersController');
            Route::get('banktransfers/{banktransfer}/changestatus/{status}', 'banktransfersController@changestatus')->name('banktransfers.changestatus');
            Route::get('banktransfers/{banktransfer_id?}/delete', 'banktransfersController@destroy')->name('banktransfers.destroy');
            Route::post('banktransfers/delete', 'banktransfersController@delete')->name('banktransfers.delete');
        });
        //================ /banktransfers =================//

        //================ inbanks =================//
        Route::middleware(['can:CRUD_inbank'])->group(function () {
            Route::resource('inbanks', 'inbanksController');
            Route::get('inbanks/{inbank}/changestatus/{status}', 'inbanksController@changestatus')->name('inbanks.changestatus');
            Route::get('inbanks/{inbank_id?}/delete', 'inbanksController@destroy')->name('inbanks.destroy');
            Route::post('inbanks/delete', 'inbanksController@delete')->name('inbanks.delete');
        });
        //================ /banktransfers =================//

        //================ reports =================//
        Route::get('reports/usergets/{user_id?}', 'reportsController@usergets')->name('reports.usergets');
        Route::middleware(['can:Reports'])->group(function () {
            Route::get('reports/sqlcheckdiff', 'reportsController@sqlcheckdiff')->name('reports.sqlcheckdiff');
            
            Route::get('reports/repprocesses', 'reportsController@repprocesses')->name('reports.repprocesses');
            Route::get('reports/repprocessesresponse', 'reportsController@repprocessesresponse')->name('reports.repprocessesresponse');

            Route::middleware(['can:Reports_Accounting'])->group(function () {
                Route::get('reports/clientbalancesum', 'reportsController@clientbalancesum')->name('reports.clientbalancesum');
                Route::get('reports/yajraclientbalancesum', 'reportsController@yajraclientbalancesum')->name('reports.yajraclientbalancesum');
            });
            
            Route::get('reports/invoiceget', 'reportsController@invoiceget')->name('reports.invoiceget');
            Route::get('reports/yajrainvoiceget', 'reportsController@yajrainvoiceget')->name('reports.yajrainvoiceget');
            Route::post('reports/yajrainvoiceget', 'reportsController@yajrainvoiceget')->name('reports.yajrainvoiceget');

            Route::middleware(['can:show_reports'])->group(function () {
                
                Route::get('reports/getsreport', 'reportsController@getsreport')->name('reports.getsreport');
                Route::get('reports/yajragetsreport', 'reportsController@yajragetsreport')->name('reports.yajragetsreport');

                Route::get('reports/clientbalance', 'reportsController@clientbalance')->name('reports.clientbalance');
                Route::get('reports/yajraclientbalance', 'reportsController@yajraclientbalance')->name('reports.yajraclientbalance');
                

                Route::get('reports/bankbalance', 'reportsController@bankbalance')->name('reports.bankbalance');
                Route::get('reports/yajrabankbalance', 'reportsController@yajrabankbalance')->name('reports.yajrabankbalance');

                Route::get('reports/catbalance', 'reportsController@catbalance')->name('reports.catbalance');
                Route::get('reports/yajracatbalance', 'reportsController@yajracatbalance')->name('reports.yajracatbalance');

                Route::get('reports/productcount', 'reportsController@productcount')->name('reports.productcount');
                Route::get('reports/yajraproductcount', 'reportsController@yajraproductcount')->name('reports.yajraproductcount');

                Route::get('reports/storeproducttransfer', 'reportsController@storeproducttransfer')->name('reports.storeproducttransfer');
                Route::get('reports/yajrastoreproducttransfer', 'reportsController@yajrastoreproducttransfer')->name('reports.yajrastoreproducttransfer');
                
                Route::get('reports/checkquantities/{group?}', 'reportsController@checkquantities')->name('reports.checkquantities');
            });
        });

        Route::middleware(['can:Shaw_all_safemoney'])->group(function () {
            Route::get('reports/usergetsall', 'reportsController@usergetsall')->name('reports.usergetsall');
        });

        Route::middleware(['can:show_usersreport'])->group(function () {
            Route::get('reports/usersreport', 'reportsController@usersreport')->name('reports.usersreport');
            Route::get('reports/yajrausersreport', 'reportsController@yajrausersreport')->name('reports.yajrausersreport');
        });
        //================ /reports =================//

        //================ notifs =================//
        Route::get('notifs', 'notifsController@index')->name('notifs.index');
        Route::get('notifsreaded/{notif}', 'notifsController@notifsreaded')->name('notifs.notifsreaded');
        Route::get('realtimenotifs', 'notifsController@realtimenotifs')->name('notifs.realtimenotifs');
        Route::get('yajranotifs', 'notifsController@yajranotifs')->name('notifs.yajranotifs');
        //================ notifs =================//

        //================ policys =================//
        //================ generalpolicys =================//
        Route::get('policys/generalpolicys/{generalpolicy}/edit', 'generalpolicysController@edit')->name('generalpolicys.edit');
        Route::PUT('policys/generalpolicys/{generalpolicy}', 'generalpolicysController@update')->name('generalpolicys.update');
        //================ userpolicys =================//
        //================ userpolicys =================//
        Route::get('policys/userpolicys', 'userpolicysController@index')->name('userpolicys.index');
        Route::get('policys/userpolicys/{userpolicy}/edit', 'userpolicysController@edit')->name('userpolicys.edit');
        Route::PUT('policys/userpolicys/{userpolicy}', 'userpolicysController@update')->name('userpolicys.update');
        //================ userpolicys =================//
        //================ regionpolicys =================//
        // Route::get('policys/regionpolicys', 'regionpolicysController@index')->name('regionpolicys.index');
        Route::get('policys/regionpolicys/editid/edit', 'regionpolicysController@edit')->name('regionpolicys.edit');
        Route::get('policys/regionpolicys/featch/{region_id}', 'regionpolicysController@featch')->name('regionpolicys.featch');
        Route::PUT('policys/regionpolicys', 'regionpolicysController@update')->name('regionpolicys.update');
        //================ regionpolicys =================//
        //================ productpolicys =================//
        Route::get('policys/productpolicys', 'productpolicysController@index')->name('productpolicys.index');
        Route::get('policys/productpolicys/{productpolicy}/edit', 'productpolicysController@edit')->name('productpolicys.edit');
        Route::PUT('policys/productpolicys/{productpolicy}', 'productpolicysController@update')->name('productpolicys.update');
        //================ productpolicys =================//
        //================ clientpolicys =================//
        Route::get('policys/clientpolicys', 'clientpolicysController@index')->name('clientpolicys.index');
        Route::get('policys/yajraclients', 'clientpolicysController@yajraclients')->name('clientpolicys.yajraclients');

        Route::get('policys/clientpolicys/{client}/edit', 'clientpolicysController@edit')->name('clientpolicys.edit');
        Route::PUT('policys/clientpolicys/{client}', 'clientpolicysController@update')->name('clientpolicys.update');
        Route::POST('policys/clientpolicys/productpolicys', 'clientpolicysController@productpolicys')->name('clientpolicys.productpolicys');
        Route::GET('policys/clientpolicys/productpolicys', 'clientpolicysController@productpolicys')->name('clientpolicys.productpolicysGET');
        Route::get('policys/clientpolicys/productpolicys/{productpolicy_id}', 'clientpolicysController@destroy')->name('clientpolicys.destroy');
        //================ clientpolicys =================//
        //================ policys =================//



    });
    //================ /webGuard Routes =================//

    //================ Fornt Site Routes =================//
    Route::get('/',  'HomeController@home')->name('site.home');
    Route::get('/transactions',  'HomeController@transactions')->name('site.transactions');
    Route::get('/testnotif/{notif?}',  'HomeController@testnotif')->name('site.testnotif');

    Route::get('/run',  function () {
        DB::table('storestocks')->truncate() ;
        $stock = ViewStockClosedSql::select([
            'store_id',
            'product_id',
            'runID',
            'q_in_store',
            'store_q_net',
            'q_reversed',
            'transfer_q_reserved',
            'transfer_in',
            'transfer_out'
        ])->where('store_id', '>', 0)->where('product_id', '>', 0)->get()->toArray();
        DB::table('storestocks')->insert($stock) ;
        return "done";
    });
    
    Route::get('/log',  function () {
        
        $activity = Activity::orderBy('id', "DESC")->take(20)->get();
        return  $activity; 
        return  $activity->created_at->timezone(config('app.timezone'))->format('Y-m-d H:i:s');; 

    });


    //================ /Fornt Site Routes =================//

});
