



  <!--Test sidebar -->
  <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
      <a href="index.html" class="app-brand-link">
        
        <img src="{{ asset('/assets/img/ScrapStationLogo.png') }}" alt="">
      </a>

      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
        <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
        <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
      </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
      <li class="menu-item {{ request()->segment(1) == 'dashboard' || request()->segment(1)=='dashboard' ? 'active' : ''}}">
        <a href="{{ route('dashboard') }}" class="menu-link">
          <i class="menu-icon fa fa-tachometer"></i>
          <div data-i18n="Dashboard">Dashboard</div>
        </a>
      </li>
      @if(in_array("view_seller", Session::get('roles')))

      <li class="menu-item {{ request()->segment(1) == 'seller_details' || request()->segment(1)=='seller_listing' || request()->segment(1) == 'seller_dashboard' ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon fa fa-user"></i>
          <div data-i18n="Seller">Seller</div>
        </a>
        <ul class="menu-sub">
          @if(in_array("view_seller", Session::get('roles')))
          <li class="menu-item {{ request()->segment(1) == 'seller_details' || request()->segment(1) == 'seller_dashboard' ? 'active' : '' }}">
            <a href="{{ url('seller_details') }}" class="menu-link">
              <div data-i18n="List">List</div>
            </a>
          </li>
          @endif
          @if(in_array("view_seller_listing", Session::get('roles')))
          <li class="menu-item {{ request()->segment(1) == 'seller_listing' ? 'active' : '' }}">
            <a href="{{ route('seller_listing') }}" class="menu-link">
              <div data-i18n="Seller Listing">Seller Listing</div>
            </a>
          </li>
          @endif
        </ul>
      </li>
      @endif
     
      @if(in_array("view_buyer", Session::get('roles')))

      <li class="menu-item {{ request()->segment(1) == 'buyer_details' || request()->segment(1) == 'buyer_listing' || request()->segment(1) == 'buyer_dashboard' ? 'active open' : ''}}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon fa fa-user"></i>
          <div data-i18n="Buyer">Buyer</div>
        </a>
        <ul class="menu-sub">
          @if(in_array("view_buyer", Session::get('roles')))
          <li class="menu-item {{ request()->segment(1) == 'buyer_details' || request()->segment(1) == 'buyer_dashboard' ? 'active' : ''}}">
            <a href="{{ route('buyer_details') }}" class="menu-link">
              <div data-i18n="List">List</div>
            </a>
          </li>
          @endif
          @if(in_array("view_buyer_listing", Session::get('roles')))
          <li class="menu-item {{ request()->segment(1) == 'buyer_listing' ? 'active' : ''}}">
            <a href="{{ route('buyer_listing') }}" class="menu-link">
              <div data-i18n="Buyer Listing">Buyer Listing</div>
            </a>
          </li>
          @endif
        </ul>
      </li>
      @endif

      @if(in_array("view_offers", Session::get('roles')))

       <li class="menu-item {{ request()->segment(1) == 'offer' || request()->segment(1)=='offer' ? 'active' : ''}}">
         <a class="menu-link" href="{{ route('offerLists') }}">
          <i class="menu-icon fa fa-gift"></i>
          <div data-i18n="Offer">Offer</div>
         </a>
       </li><!-- End Profile Page Nav -->
      @endif
      @if(in_array("view_orders", Session::get('roles')))

       <li class="menu-item {{ request()->segment(1) == 'order' || request()->segment(1)=='order' ? 'active' : ''}}">
         <a class="menu-link" href="{{ route('orderList') }}">
          <i class="menu-icon ti ti-shopping-cart"></i>
          <div data-i18n="Order">Order</div>
         </a>
       </li><!-- End Profile Page Nav -->
      @endif
      @if(in_array("view_payment", Session::get('roles')))
       <li class="menu-item {{ Route::currentRouteNamed('paymentList') ? 'active' : '' }}">
         <a class="menu-link" href="{{ route('paymentList') }}">
          <i class="menu-icon fa fa-usd"></i>
          <div data-i18n="Payment">Payment</div>
         </a>
       </li>
      @endif
      @if(in_array("view_driver", Session::get('roles')))
       <li class="menu-item {{ request()->segment(1) == 'driver' || request()->segment(1)=='driver' ? 'active' : ''}}">
         <a class="menu-link" href="{{ route('driverList') }}">
          <i class="menu-icon fa fa-bus"></i>
          <div data-i18n="Driver">Driver</div>
         </a>
       </li>
      @endif
      @if(in_array("view_complaint", Session::get('roles')))
       <li class="menu-item {{ request()->segment(1) == 'complaint' || request()->segment(1)=='complaint' ? 'active' : ''}}">
         <a class="menu-link" href="{{ route('complaint_list') }}">
          <i class="menu-icon fa fa-envelope-open"></i>
          
          <div data-i18n="Complaint">Complaint</div>
         </a>
       </li>
      @endif
    
      @if(in_array("system", Session::get('roles')))

      <li class="menu-item {{ request()->segment(1) == 'user_roles' || request()->segment(1) == 'group_list' || request()->segment(1) == 'user_list' ? 'active open' : ''}}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon fa fa-user"></i>
          <div data-i18n="System">System</div>
        </a>
        <ul class="menu-sub">
          @if(in_array("role", Session::get('roles')))
          <li class="menu-item {{ request()->segment(1) == 'user_roles' ? 'active' : ''}}">
            <a href="{{ route('admin.user.roles') }}" class="menu-link">
              <div data-i18n="Role">Role</div>
            </a>
          </li>
          @endif
          @if(in_array("group", Session::get('roles')))
          <li class="menu-item {{ request()->segment(1) == 'group_list' ? 'active' : ''}}">
            <a href="{{ route('group_list') }}" class="menu-link">
              <div data-i18n="Group">Group</div>
            </a>
          </li>
          @endif
          @if(in_array("user", Session::get('roles')))
          <li class="menu-item {{ request()->segment(1) == 'user_list' ? 'active' : ''}}">
            <a href="{{ route('user_list') }}" class="menu-link">
              <div data-i18n="User">User</div>
            </a>
          </li>
          @endif
        </ul>
      </li>
      @endif

    </ul>
  </aside>