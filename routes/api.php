<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function() {
    $data = [
        'message' => "API Working"
    ];
    return response()->json($data, 200);
});

Route::group(['prefix' => 'v1'], function(){
    Route::group(['prefix' => 'seller', 'middleware'=>'SaveSellerMobileRequest'], function(){
        Route::post('login', ['as' => 'fm.seller.login', 'uses' => 'AuthController@sellerlogin'], );
        Route::middleware('seller')->group(function() {

            Route::get('dashboard', ['as' => 'fm.seller.dashboard', 'uses' => 'SellerController@getDashboardDetails']);

			Route::get('notifications', ['as' => 'fm.seller.notifications', 'uses' => 'NotificationController@getAllNotifications']);

			Route::patch('read-notifications/{notification_id}', ['as' => 'fm.seller.notificationsread', 'uses' => 'NotificationController@setNotificationAsRead']);
			Route::patch('read-notifications/{notification_id}/{yard_id}', ['as' => 'fm.seller.notificationsread', 'uses' => 'NotificationController@setNotificationAsRead']);

			Route::post('create-listing', ['as' => 'fm.seller.createListing', 'uses' => 'SellerController@createListing']);

			Route::patch('close-listing/{sell_list_id}', ['as' => 'fm.seller.closeListing', 'uses' => 'SellerController@closeListing']);

			Route::get('get-listings', ['as' => 'fm.seller.alllistings', 'uses' => 'SellerController@showListing']);
			Route::get('get-listings/{sell_list_id}', ['as' => 'fm.seller.specificlisting', 'uses' => 'SellerController@showListing']);

			Route::get('get-listing-offers/{sell_list_id}', ['as' => 'fm.seller.listingoffers', 'uses' => 'SellerController@getListingOffers']);

			Route::get('get-offers/{offer_id}', ['as' => 'fm.seller.getoffer', 'uses' => 'SellerController@getOffers']);

			Route::get('get-latest-offers', ['as' => 'fm.seller.latestoffers', 'uses' => 'SellerController@getLatestOffers']);

			Route::get('get-latest-buyer-list-offers', ['as' => 'fm.seller.latestbuyerlistoffers', 'uses' => 'SellerController@getLatestBuyerListOffers']);

			Route::patch('accept-offer', ['as' => 'fm.seller.acceptoffer', 'uses' => 'SellerController@updateOffer']);

			Route::patch('update-offer', ['as' => 'fm.seller.updateoffer', 'uses' => 'SellerController@updateOffer']);

			Route::get('get-orders', ['as' => 'fm.seller.getorders', 'uses' => 'OrderController@getOrders']);
			Route::get('get-orders/{order_id}', ['as' => 'fm.seller.getspecificorder', 'uses' => 'OrderController@getSpecificOrder']);

			Route::get('get-payments', ['as' => 'fm.seller.getpayments', 'uses' => 'PaymentController@getPayments']);
			Route::get('get-payments/{pay_id}', ['as' => 'fm.seller.getspecificpayment', 'uses' => 'PaymentController@getSpecificPayment']);

			Route::patch('verify-payment/{pay_id}', ['as' => 'fm.seller.verifypayment', 'uses' => 'PaymentController@verifyPayment']);

			Route::get('get-applicants/{sell_list_id}', ['as' => 'fm.seller.sellerapplicants', 'uses' => 'SellerController@getLatestOffers']);

			Route::post('add-comment', ['as' => 'fm.comment.addcomment', 'uses' => 'CommentController@addComment']);
			Route::get('get-comments/{ref_type}/{ref_id}', ['as' => 'fm.comment.getcomment', 'uses' => 'CommentController@getCommentOfType']);

			Route::get('get-buyer-listings/{cat_id}', ['as' => 'fm.seller.buyerlistings', 'uses' => 'SellerController@getMaterialsOfSubcategory']);
			Route::get('buyer-category-information', ['as' => 'fm.seller.getcategoryinformation', 'uses' => 'SellerController@getBuyerCategoriesInformation']);

			Route::patch('buyer-listing-apply/{buyer_list_id}', ['as' => 'fm.seller.applyforbuyerlisting', 'uses' => 'SellerController@applyOnBuyerLising']);

			Route::patch('update-seller/{seller_id}', ['as' => 'fm.seller.updateseller', 'uses' => 'SellerController@updateSeller']);

			Route::get('get-chat-list', ['as' => 'fm.seller.sellerchatlist', 'uses' => 'SellerController@getSellerChatList']);

			Route::post('review-order/{order_id}', ['as' => 'fm.seller.orderreview', 'uses' => 'OrderController@addReview']);

			Route::post('add-complaint', ['as' => 'fm.complaint.addcomplaint', 'uses' => 'ComplaintController@addComplaint']);

			Route::get('get-complaint-elements', ['as' => 'fm.complaint.getcomplaintelements', 'uses' => 'ComplaintController@getComplaintElements']);

			Route::get('get-my-complaints', ['as' => 'fm.complaint.getmycomplaints', 'uses' => 'ComplaintController@getComplaints']);

			Route::patch('read-notifications-all', ['as' => 'fm.seller.notificationsallread', 'uses' => 'NotificationController@setAllNotificationAsRead']);
			Route::patch('read-notifications-all/{yard_id}', ['as' => 'fm.seller.notificationsallread', 'uses' => 'NotificationController@setAllNotificationAsRead']);
			Route::post('logout', 'AuthController@sellerLogout');
        });
        Route::post('add', ['as' => 'fm.seller.add', 'uses' => 'SellerController@create']);
        Route::post('validate-otp', ['as' => 'fm.seller.validateOTP', 'uses' => 'AuthController@generateOTP']);
        Route::post('verify-otp', ['as' => 'fm.seller.verifyOTP', 'uses' => 'AuthController@verifyOTP']);
    });
    Route::group(['prefix' => 'buyer', 'middleware'=>'SaveBuyerMobileRequest'], function(){
        Route::post('login', ['as' => 'fm.buyer.login', 'uses' => 'AuthController@buyerlogin'], );
        Route::middleware('buyer')->group(function() {
            Route::get('dashboard', ['as' => 'fm.buyer.dashboard', 'uses' => 'BuyerController@getDashboardDetails']);

			Route::get('notifications', ['as' => 'fm.buyer.notifications', 'uses' => 'NotificationController@getAllNotifications']);

			Route::patch('read-notifications/{notification_id}', ['as' => 'fm.buyer.notificationsread', 'uses' => 'NotificationController@setNotificationAsRead']);
			Route::patch('read-notifications/{notification_id}/{yard_id}', ['as' => 'fm.buyer.notificationsread', 'uses' => 'NotificationController@setNotificationAsRead']);

			Route::get('seller-listings/{cat_id}', ['as' => 'fm.buyer.sellerlistings', 'uses' => 'BuyerController@getMaterialsOfSubcategory']);

			Route::get('seller-category-information', ['as' => 'fm.buyer.getcategoryinformation', 'uses' => 'BuyerController@getSellerCategoriesInformation']);

			Route::post('create-offer', ['as' => 'fm.buyer.createoffer', 'uses' => 'BuyerController@createOffer']);

			Route::post('create-buyer-list-offer', ['as' => 'fm.buyer.createoffer', 'uses' => 'BuyerController@createBuyerListingOffer']);

			Route::get('get-offers', ['as' => 'fm.buyer.getoffers', 'uses' => 'BuyerController@getOffers']);
			Route::get('get-offers/{offer_id}', ['as' => 'fm.buyer.getoffer', 'uses' => 'BuyerController@getOffers']);

			Route::get('get-buyer-list-offers', ['as' => 'fm.buyer.getbuyerlistoffers', 'uses' => 'BuyerController@getBuyerListOffers']);
			Route::get('get-buyer-list-offers/{offer_id}', ['as' => 'fm.buyer.getbuyerlistoffers', 'uses' => 'BuyerController@getBuyerListOffers']);
			
			Route::get('get-orders', ['as' => 'fm.buyer.getorders', 'uses' => 'OrderController@getOrders']);
			Route::get('get-orders/{order_id}', ['as' => 'fm.buyer.getspecificorder', 'uses' => 'OrderController@getSpecificOrder']);

			Route::get('get-payments', ['as' => 'fm.buyer.getpayments', 'uses' => 'PaymentController@getPayments']);
			Route::get('get-payments/{pay_id}', ['as' => 'fm.buyer.getspecificpayment', 'uses' => 'PaymentController@getSpecificPayment']);

			Route::post('add-order-checkpoint/{order_id}', ['as' => 'fm.buyer.addordercheckpoint', 'uses' => 'OrderController@addOrderCheckpoint']);

			Route::patch('update-order/{order_id}', ['as' => 'fm.buyer.updateorder', 'uses' => 'OrderController@updateOrder']);
			Route::patch('complete-order/{order_id}', ['as' => 'fm.buyer.completeorder', 'uses' => 'OrderController@completeOrder']);

			Route::patch('complete-payment/{pay_id}', ['as' => 'fm.buyer.completepayment', 'uses' => 'PaymentController@completePayment']);
			Route::patch('verify-payment/{pay_id}', ['as' => 'fm.buyer.verifypayment', 'uses' => 'PaymentController@verifyPayment']);

			Route::post('add-comment', ['as' => 'fm.comment.addcomment', 'uses' => 'CommentController@addComment']);
			Route::get('get-comments/{ref_type}/{ref_id}', ['as' => 'fm.comment.getcomment', 'uses' => 'CommentController@getCommentOfType']);

			Route::post('create-listing', ['as' => 'fm.buyer.createListing', 'uses' => 'BuyerController@createListing']);

			Route::patch('close-listing/{buyer_list_id}', ['as' => 'fm.buyer.closeListing', 'uses' => 'BuyerController@closeListing']);

			Route::get('get-listings', ['as' => 'fm.buyer.alllistings', 'uses' => 'BuyerController@showListing']);
			Route::get('get-listings/{buyer_list_id}', ['as' => 'fm.buyer.specificlisting', 'uses' => 'BuyerController@showListing']);

			Route::get('get-listing-offers/{buyer_list_id}', ['as' => 'fm.buyer.listingoffers', 'uses' => 'BuyerController@getListingOffers']);
			
			Route::get('get-applicants/{buyer_list_id}', ['as' => 'fm.buyer.buyerapplicants', 'uses' => 'BuyerController@getLatestOffers']);

			Route::get('get-chat-list', ['as' => 'fm.buyer.buyerchatlist', 'uses' => 'BuyerController@getBuyerChatList']);

			Route::patch('update-buyer/{buyer_id}', ['as' => 'fm.buyer.updatebuyer', 'uses' => 'BuyerController@updateBuyer']);

			Route::patch('read-notifications-all', ['as' => 'fm.buyer.notificationsallread', 'uses' => 'NotificationController@setAllNotificationAsRead']);
			Route::patch('read-notifications-all/{yard_id}', ['as' => 'fm.buyer.notificationsallread', 'uses' => 'NotificationController@setAllNotificationAsRead']);
        });
        Route::post('add', ['as' => 'fm.buyer.add', 'uses' => 'BuyerController@create']);
        Route::post('validate-otp', ['as' => 'fm.buyer.validateOTP', 'uses' => 'AuthController@generateOTP']);
        Route::post('verify-otp', ['as' => 'fm.buyer.verifyOTP', 'uses' => 'AuthController@verifyOTP']);

    
    });
});