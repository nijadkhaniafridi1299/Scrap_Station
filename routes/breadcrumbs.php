<?php // routes/breadcrumbs.php

// Note: Laravel will automatically resolve `Breadcrumbs::` without
// this import. This is nice for IDE syntax and refactoring.
use Diglactic\Breadcrumbs\Breadcrumbs;

// This import is also not required, and you could replace `BreadcrumbTrail $trail`
//  with `$trail`. This is nice for IDE type checking and completion.
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard
Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
    $trail->push('Dashboard', route('dashboard'));
});


Breadcrumbs::for('seller_details', function (BreadcrumbTrail $trail) {
    $trail->push('Seller', route('seller_details'));
});

// Seller > Details
Breadcrumbs::for('seller_dashboard', function (BreadcrumbTrail $trail) {
    $trail->parent('seller_details');
    $trail->push('Details', route('seller_dashboard'));
});

Breadcrumbs::for('seller_listing', function (BreadcrumbTrail $trail) {
    $trail->push('Seller Listings', route('seller_listing'));
});

Breadcrumbs::for('buyer_details', function (BreadcrumbTrail $trail) {
    $trail->push('Buyer', route('buyer_details'));
});
Breadcrumbs::for('admin.user.roles', function (BreadcrumbTrail $trail) {
    $trail->push('Role', route('admin.user.roles'));
});
Breadcrumbs::for('group_list', function (BreadcrumbTrail $trail) {
    $trail->push('Group', route('group_list'));
});

// Buyer > Details
Breadcrumbs::for('buyer_dashboard', function (BreadcrumbTrail $trail) {
    $trail->parent('buyer_details');
    $trail->push('Details', route('buyer_dashboard'));
});

Breadcrumbs::for('buyer_listing', function (BreadcrumbTrail $trail) {
    $trail->push('Buyer Listings', route('buyer_listing'));
});

Breadcrumbs::for('offerLists', function (BreadcrumbTrail $trail) {
    $trail->push('Offers', route('offerLists'));
});

Breadcrumbs::for('orderList', function (BreadcrumbTrail $trail) {
    $trail->push('Orders', route('orderList'));
});

Breadcrumbs::for('paymentList', function (BreadcrumbTrail $trail) {
    $trail->push('Payments', route('paymentList'));
});

Breadcrumbs::for('driverList', function (BreadcrumbTrail $trail) {
    $trail->push('Driver', route('driverList'));
});
Breadcrumbs::for('complaint_list', function (BreadcrumbTrail $trail) {
    $trail->push('Complaint', route('complaint_list'));
});
Breadcrumbs::for('user_list', function (BreadcrumbTrail $trail) {
    $trail->push('User', route('user_list'));
});

// Home > Blog > [Category]
/*Breadcrumbs::for('category', function (BreadcrumbTrail $trail, $category) {
    $trail->parent('blog');
    $trail->push($category->title, route('category', $category));
});*/