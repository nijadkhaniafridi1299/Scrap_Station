


<!--testing header-->
<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
      <i class="ti ti-menu-2 ti-sm"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <div class="search-bar">
      {{ Breadcrumbs::render(\Request::route()->getName()) }}
      {{-- <form class="search-form d-flex align-items-center" method="POST" action="#">
        <input type="text" name="query" placeholder="Search" title="Enter search keyword">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
      </form> --}}
    </div>

    <ul class="navbar-nav flex-row align-items-center ms-auto">
     

      <!-- Style Switcher -->
      <li class="nav-item me-2 me-xl-0">
        <a class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);">
          <i class="ti ti-md"></i>
        </a>
      </li>
      <!--/ Style Switcher -->

   

      <!-- Notification -->
      <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
        <a
          class="nav-link dropdown-toggle hide-arrow"
          href="javascript:void(0);"
          data-bs-toggle="dropdown"
          data-bs-auto-close="outside"
          aria-expanded="false"
        >
          <i class="ti ti-bell ti-md"></i>
          <span class="badge bg-danger rounded-pill badge-notifications" id="notifications"></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end py-0">
          <li class="dropdown-menu-header border-bottom">
            <div class="dropdown-header d-flex align-items-center py-3">
              <h5 class="text-body mb-0 me-auto">Notification</h5>
              <a
                href="javascript:void(0)"
                class="dropdown-notifications-all text-body"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="Mark all as read"
                ><i class="ti ti-mail-opened fs-4"></i
              ></a>
            </div>
          </li>
          <li class="dropdown-notifications-list scrollable-container">
            <ul class="list-group list-group-flush">
              <div id="notification_list">
                
              </div>
       
            
            
            
             
             
            </ul>
          </li>
        
        </ul>
      </li>
      <!--/ Notification -->

      <!-- User -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar">
            <img src="{{asset('/scrap/admin.jpg')}}" alt class="h-auto rounded-circle w-75 h-75 mt-1" />
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
         
          <li>
            <a class="dropdown-item" href="{{ route('signout') }}">
              <i class="ti ti-logout me-2 ti-sm"></i>
              <span class="align-middle">Log Out</span>
            </a>
          </li>
        </ul>
      </li>
      <!--/ User -->
    </ul>
  </div>

  <!-- Search Small Screens -->
  <div class="navbar-search-wrapper search-input-wrapper d-none">
    <input
      type="text"
      class="form-control search-input container-xxl border-0"
      placeholder="Search..."
      aria-label="Search..."
    />
    <i class=" ti-sm search-toggler cursor-pointer"></i>
  </div>
</nav>
<!--end header-->
{{-- <script src="{{ asset('/assets/js/main.js') }}"></script> --}}
{{-- <script src="{{ asset('/assets/js/jquery-3.6.3.js')}}"></script> --}}

<script>
  $(document).ready(function(){
    showNotification();
    setInterval("showNotification()", 30000);
  });

  function showNotification(){
    
    $.ajax({
            url: "{{ route('getNotification')}}",
		        type: "GET",
		        //data: {"_token": "{{csrf_token()}}"},
		        datatype: "json",
               
                success:function (response){
                 var notifications = response.data;
                $('#notifications').text(notifications.unread_count);
                $('#notifications_message').text(notifications.unread_count);
                 var notification_text = '';
                 $.each(notifications.notifications, function(index, notification_list) {
                    if(notification_list.reference_type == 'seller'){
                      notification_text +='<li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read"> <div class="d-flex"> <div class="flex-shrink-0 me-3"> <div class="avatar">  <i class=" fa fa-user"></i> </div> </div> <div class="flex-grow-1"> <h6 class="mb-1">'+notification_list.reference_type.charAt(0).toUpperCase() + notification_list.reference_type.slice(1)+'</h6> <p class="mb-0">'+notification_list.notification_body+'</p> <small class="text-muted">'+notification_list.created_at+'</small> </div> <div class="flex-shrink-0 dropdown-notifications-actions"> <a href="javascript:void(0)" class="dropdown-notifications-read" ><span class="badge badge-dot"></span ></a> <a href="javascript:void(0)" class="dropdown-notifications-archive" ><span ></span ></a> </div> </div> </li>';                  
                    }
                    else if(notification_list.reference_type == 'buyer'){
                      notification_text +='<li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read"> <div class="d-flex"> <div class="flex-shrink-0 me-3"> <div class="avatar">  <i class=" fa fa-user"></i> </div> </div> <div class="flex-grow-1"> <h6 class="mb-1">'+notification_list.reference_type.charAt(0).toUpperCase() + notification_list.reference_type.slice(1)+' </h6> <p class="mb-0">'+notification_list.notification_body+'</p> <small class="text-muted">'+notification_list.created_at+'</small> </div> <div class="flex-shrink-0 dropdown-notifications-actions"> <a href="javascript:void(0)" class="dropdown-notifications-read" ><span class="badge badge-dot"></span ></a> <a href="javascript:void(0)" class="dropdown-notifications-archive" ><span></span ></a> </div> </div> </li>';                  
                    }  
                    else if(notification_list.reference_type == 'Seller Listing'){
                      notification_text +='<li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read"> <div class="d-flex"> <div class="flex-shrink-0 me-3"> <div class="avatar"><i class="fa fa-shopping-bag" aria-hidden="true"></i> </div> </div> <div class="flex-grow-1"> <h6 class="mb-1">'+notification_list.reference_type.charAt(0).toUpperCase() + notification_list.reference_type.slice(1)+' </h6> <p class="mb-0">'+notification_list.notification_body+'</p> <small class="text-muted">'+notification_list.created_at+'</small> </div> <div class="flex-shrink-0 dropdown-notifications-actions"> <a href="javascript:void(0)" class="dropdown-notifications-read" ><span class="badge badge-dot"></span ></a> <a href="javascript:void(0)" class="dropdown-notifications-archive" ></a> </div> </div> </li>';                  
                    }
                    else if(notification_list.reference_type == 'Buyer Listing'){
                      notification_text +='<li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read"> <div class="d-flex"> <div class="flex-shrink-0 me-3"> <div class="avatar"> <i class="fa fa-shopping-bag" aria-hidden="true"></i> </div> </div> <div class="flex-grow-1"> <h6 class="mb-1">'+notification_list.reference_type.charAt(0).toUpperCase() + notification_list.reference_type.slice(1)+' </h6> <p class="mb-0">'+notification_list.notification_body+'</p> <small class="text-muted">'+notification_list.created_at+'</small> </div> <div class="flex-shrink-0 dropdown-notifications-actions"> <a href="javascript:void(0)" class="dropdown-notifications-read" ><span class="badge badge-dot"></span ></a> <a href="javascript:void(0)" class="dropdown-notifications-archive" ></a> </div> </div> </li>';                  
                    } 
                    else if(notification_list.reference_type == 'Order'){
                      notification_text +='<li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read"> <div class="d-flex"> <div class="flex-shrink-0 me-3"> <div class="avatar">    <i class="menu-icon ti ti-shopping-cart"></i> </div> </div> <div class="flex-grow-1"> <h6 class="mb-1">'+notification_list.reference_type.charAt(0).toUpperCase() + notification_list.reference_type.slice(1)+' </h6> <p class="mb-0">'+notification_list.notification_body+'</p> <small class="text-muted">'+notification_list.created_at+'</small> </div> <div class="flex-shrink-0 dropdown-notifications-actions"> <a href="javascript:void(0)" class="dropdown-notifications-read" ><span class="badge badge-dot"></span ></a> <a href="javascript:void(0)" class="dropdown-notifications-archive" ></a> </div> </div> </li>';                  
                    }  
                    else if(notification_list.reference_type == 'Offer'){
                      notification_text +='<li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read"> <div class="d-flex"> <div class="flex-shrink-0 me-3"> <div class="avatar">   <i class="fa fa-gift"></i> </div> </div> <div class="flex-grow-1"> <h6 class="mb-1">'+notification_list.reference_type.charAt(0).toUpperCase() + notification_list.reference_type.slice(1)+' </h6> <p class="mb-0">'+notification_list.notification_body+'</p> <small class="text-muted">'+notification_list.created_at+'</small> </div> <div class="flex-shrink-0 dropdown-notifications-actions"> <a href="javascript:void(0)" class="dropdown-notifications-read" ><span class="badge badge-dot"></span ></a> <a href="javascript:void(0)" class="dropdown-notifications-archive" ></a> </div> </div> </li>';                  
                    }  
                    else if(notification_list.reference_type == 'Payment'){
                      notification_text +='<li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read"> <div class="d-flex"> <div class="flex-shrink-0 me-3"> <div class="avatar">     <i class="menu-icon fa fa-usd"></i> </div> </div> <div class="flex-grow-1"> <h6 class="mb-1">'+notification_list.reference_type.charAt(0).toUpperCase() + notification_list.reference_type.slice(1)+' </h6> <p class="mb-0">'+notification_list.notification_body+'</p> <small class="text-muted">'+notification_list.created_at+'</small> </div> <div class="flex-shrink-0 dropdown-notifications-actions"> <a href="javascript:void(0)" class="dropdown-notifications-read" ><span class="badge badge-dot"></span ></a> <a href="javascript:void(0)" class="dropdown-notifications-archive" ></a> </div> </div> </li>';                  
                    }  
                    else if(notification_list.reference_type == 'Buyer Listing Applicant'){
                      notification_text +='<li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read"> <div class="d-flex"> <div class="flex-shrink-0 me-3"> <div class="avatar">     <i class="menu-icon fa fa-envelope-open"></i> </div> </div> <div class="flex-grow-1"> <h6 class="mb-1">'+notification_list.reference_type.charAt(0).toUpperCase() + notification_list.reference_type.slice(1)+' </h6> <p class="mb-0">'+notification_list.notification_body+'</p> <small class="text-muted">'+notification_list.created_at+'</small> </div> <div class="flex-shrink-0 dropdown-notifications-actions"> <a href="javascript:void(0)" class="dropdown-notifications-read" ><span class="badge badge-dot"></span ></a> <a href="javascript:void(0)" class="dropdown-notifications-archive" ></a> </div> </div> </li>';                  
                    }
                    else if(notification_list.reference_type == 'complaint'){
                      notification_text +='<li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read"> <div class="d-flex"> <div class="flex-shrink-0 me-3"> <div class="avatar">     <i class="menu-icon fa fa-envelope-open"></i> </div> </div> <div class="flex-grow-1"> <h6 class="mb-1">'+notification_list.reference_type.charAt(0).toUpperCase() + notification_list.reference_type.slice(1)+' </h6> <p class="mb-0">'+notification_list.notification_body+'</p> <small class="text-muted">'+notification_list.created_at+'</small> </div> <div class="flex-shrink-0 dropdown-notifications-actions"> <a href="javascript:void(0)" class="dropdown-notifications-read" ><span class="badge badge-dot"></span ></a> <a href="javascript:void(0)" class="dropdown-notifications-archive" ></a> </div> </div> </li>';                  
                    }  
                    else if(notification_list.reference_type == 'driver'){
                      notification_text +='<li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read"> <div class="d-flex"> <div class="flex-shrink-0 me-3"> <div class="avatar">  <i class="menu-icon fa fa-bus"></i> </div> </div> <div class="flex-grow-1"> <h6 class="mb-1">'+notification_list.reference_type.charAt(0).toUpperCase() + notification_list.reference_type.slice(1)+' </h6> <p class="mb-0">'+notification_list.notification_body+'</p> <small class="text-muted">'+notification_list.created_at+'</small> </div> <div class="flex-shrink-0 dropdown-notifications-actions"> <a href="javascript:void(0)" class="dropdown-notifications-read" ><span class="badge badge-dot"></span ></a> <a href="javascript:void(0)" class="dropdown-notifications-archive" ></a> </div> </div> </li>';                  
                    }
                    else{
                      notification_text +='<li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read"> <div class="d-flex"> <div class="flex-shrink-0 me-3"> <div class="avatar">     <i class="menu-icon fa fa-envelope-open"></i> </div> </div> <div class="flex-grow-1"> <h6 class="mb-1">'+notification_list.reference_type.charAt(0).toUpperCase() + notification_list.reference_type.slice(1)+' </h6> <p class="mb-0">'+notification_list.notification_body+'</p> <small class="text-muted">'+notification_list.created_at+'</small> </div> <div class="flex-shrink-0 dropdown-notifications-actions"> <a href="javascript:void(0)" class="dropdown-notifications-read" ><span class="badge badge-dot"></span ></a> <a href="javascript:void(0)" class="dropdown-notifications-archive" ></a> </div> </div> </li>';                  

                    }
                });
                 $("#notification_list").html(notification_text);
                //  myVar = setTimeout("showNotification()", 100);

                }
        });
}

  
</script>
<!-- ======= Header ======= -->
  @include('admin.scrapstation_firebase')
<!-- End Header -->
<style>
#loader {
  position: fixed; display: flex; justify-content: center; align-items: center; width: 100%; height: 100%;
  top: 0; left: 0; opacity: 0.7; background-color: #fff; z-index: 99999;
}
#loading-image { z-index: 100; }

</style>
<div id="loader" style="display: none">
  <img id="loading-image" src="{{ asset('/assets/img/loader.gif')}}" alt="Loading..." />
</div>