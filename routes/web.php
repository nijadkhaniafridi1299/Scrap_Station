<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\GroupController;
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

Route::get('/', [UserController::class, 'index'])->name('login');
Route::post('custom-login', [UserController::class, 'customLogin'])->name('login.custom'); 
 
Route::group(['middleware' => ['auth']], function() {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    // selller routes  
    Route::get('/seller_details/{id?}', [SellerController::class, 'seller_detailsgWeb'])->name('seller_details');  
    Route::put('/sellerupdate/{id?}', [SellerController::class, 'seller_Updated'])->name('seller.update');  
    Route::post('/seller_details/status', [SellerController::class, 'chang_seller_statusweb'])->name('seller_verified.update');  
    Route::post('/add_recharge/{id?}', [SellerController::class, 'add_wallet_amount'])->name('add_recharge');  
    Route::get('/seller_listing/{id?}', [SellerController::class, 'show_listingWeb'])->name('seller_listing');  
    Route::get('/seller_applicant/{id?}', [SellerController::class, 'get_applicant'])->name('seller_applicant');  
    Route::post('/seller_listing/status', [SellerController::class, 'chang_sellerlisting_status'])->name('seller_listing.update');
    Route::get('/seller_dashboard/{id?}', [SellerController::class, 'seller_dashboard'])->name('seller_dashboard');
    // Buyer routes  
    Route::get('/buyer_details/{id?}', [BuyerController::class, 'getBuyerDetailsWeb'])->name('buyer_details');  
    Route::get('/buyer_dashboard/{id?}', [BuyerController::class, 'getBuyerDashboardWeb'])->name('buyer_dashboard');
    Route::post('/add_recharge_for_buyer/{id?}', [BuyerController::class, 'add_wallet_amount'])->name('add_recharges');  
    Route::post('/buyer_details/status', [BuyerController::class, 'chang_buyer_statusweb'])->name('buyer_verified.update');  
    Route::get('/buyer_listing/{id?}', [BuyerController::class, 'buyer_listingweb'])->name('buyer_listing');  
    Route::post('/buyerlisting/status', [BuyerController::class, 'chang_buyerListing_statusweb'])->name('buyer_listing.update');  
    Route::post('/buyerupdate/{id?}', [BuyerController::class, 'buyer_Updated'])->name('buyer.update'); 
    Route::get('/buyer_applicant/{id?}', [BuyerController::class, 'get_applicant'])->name('buyer_applicant');  


    Route::get('/order/{id?}', [OrderController::class, 'getOrderDetailsweb'])->name('orderList'); 
    Route::post('/order_listing/status', [OrderController::class, 'chang_Orderlisting_statusweb'])->name('order_listing.update');  
    
    Route::get('/offer/{id?}', [OfferController::class, 'get_offer_listsweb'])->name('offerLists'); 
    Route::post('/offer_listing/update', [OfferController::class, 'chang_Offerlisting_statusweb'])->name('offer_listing.update');  

    //payment routes
    Route::get('/payment/{id?}', [PaymentController::class, 'showPaymentListWeb'])->name('paymentList');  
    Route::post('/pament/status', [PaymentController::class, 'chang_payment_statusweb'])->name('paymentstatus.update');
    
    Route::get('get-comments/{ref_type?}/{ref_id?}', [CommentController::class, 'getCommentOfType'])->name('get_chat');
   
    // track
    Route::get('/track/{id?}', [OrderController::class, 'showtrackWeb'])->name('getTrack');  

    //Notification
    Route::get('/getnotification', [NotificationController::class, 'getAllNotifications'])->name('getNotification');  

    //driver
    Route::get('/driver/{id?}', [DriverController::class, 'driverListsWeb'])->name('driverList');  
    Route::post('/driver_update/{id?}', [DriverController::class, 'driver_update_Web'])->name('driver_update');  
    Route::post('/driver_verification/{id?}', [DriverController::class, 'driververified'])->name('driververification');  
    Route::post('/add_driver', [DriverController::class, 'add_driverWeb'])->name('add_driver');  
    // Route::post('/edit_driver/{id?}', [DriverController::class, 'edit_driverWeb'])->name('driver_edit');  
  
    // Complaint
    Route::get('/complaint/{id?}', [ComplaintController::class, 'complaintListWeb'])->name('complaint_list');  
    Route::get('/complaint_status/{id?}', [ComplaintController::class, 'complaint_changeStatusWeb'])->name('status_change');  
   
    //user Role
    Route::get('/user_roles', [RoleController::class, 'index'])->name('admin.user.roles');
    Route::post('/add_roles/{id?}', [RoleController::class, 'Create'])->name('add_role');
    Route::post('/add_roles/{id?}', [RoleController::class, 'Create'])->name('add_role');
    Route::post('/updated_roles/{id?}', [RoleController::class, 'Update'])->name('update_role');

    //group
    Route::get('/group_list/{id?}', [GroupController::class, 'index'])->name('group_list');
    Route::post('/group_create/{id?}', [GroupController::class, 'create'])->name('add_group');
    Route::post('/group_updated/{id?}', [GroupController::class, 'update'])->name('update_group');

    //user
    Route::get('/user_list/{id?}', [UserController::class, 'userList'])->name('user_list');
    Route::post('/add_users/{id?}', [UserController::class, 'createUsers'])->name('add_user');
    Route::post('/update_users/{id?}', [UserController::class, 'updateUsers'])->name('update_user');
 
    Route::get('signout', [UserController::class, 'signOut'])->name('signout');
});