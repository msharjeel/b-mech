{{-- This file is used to store sidebar items, inside the Backpack admin panel --}}
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
    @if(backpack_user()->can('manage services'))
    <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-tools"></i> Services</a>
        <ul class="nav-dropdown-items">
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('service') }}"><i class="nav-icon la  la-user"></i> List services</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('service-category') }}"><i class="nav-icon la la-user"></i> Services categories</a></li>
        </ul>
    </li>
    @endif

    @if(backpack_user()->can('manage make') || backpack_user()->can('manage model'))
    <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-bus"></i>Vehicles</a>
        <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('vehicle-make') }}"><i class="nav-icon  la la-taxi"></i> Makes</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('vehicle-model') }}"><i class="nav-icon  la la-taxi"></i> Models</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('vehicle-transmission') }}"><i class="nav-icon la la-taxi"></i> Transmissions</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('vehicle-cylinder') }}"><i class="nav-icon la la-taxi"></i> Cylinders</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('vehicle-class') }}"><i class="nav-icon la la-taxi"></i> Classes</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('vehicle-drive') }}"><i class="nav-icon la la-taxi"></i> Drives</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('vehicle-displacement') }}"><i class="nav-icon la la-taxi"></i> Displacements</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('country') }}"><i class="nav-icon la la-globe"></i> Countries</a></li>
        </ul>
    </li>
    @endif

    @if(backpack_user()->can('manage vehicles') || backpack_user()->can('manage locations') )
    <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-taxi"></i> Customers</a>
        <ul class="nav-dropdown-items">
            @if(backpack_user()->can('manage vehicles'))
                <li class="nav-item"><a class="nav-link" href="{{ backpack_url('customer-vehicle') }}"><i class="nav-icon la la-car-alt"></i> <span>List Vehicles</span></a></li>
            @endif
            @if(backpack_user()->can('manage locations'))
                <li class="nav-item"><a class="nav-link" href="{{ backpack_url('location') }}"><i class="nav-icon la la-home"></i> <span>List Addresses</span></a></li>
            @endif
        </ul>
    </li>
    @endif


    <!-- @if(backpack_user()->can('manage service request'))
    <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-wave-square"></i> Service requests</a>
        <ul class="nav-dropdown-items">
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('service-request') }}"><i class="nav-icon la la-user"></i> <span>List requests</span></a></li>
            
        </ul>
    
    </li>
    @endif -->

    <!-- @if(backpack_user()->can('manage reviews'))
    <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-hand-point-left"></i> Reviews</a>
        <ul class="nav-dropdown-items">
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('review') }}"><i class="nav-icon la la-user"></i> <span>List Reviews</span></a></li>
            
        </ul>
    </li>
    @endif -->

    @if(backpack_user()->can('manage orders'))
    <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-cart-arrow-down"></i> Orders</a>
        <ul class="nav-dropdown-items">
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('service-request') }}"><i class="nav-icon la la-user"></i> <span>List requests</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('order') }}"><i class="nav-icon la la-user"></i> <span>List Orders</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('transaction') }}"><i class="nav-icon la la-user"></i> <span> List Transactions</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user-request-log') }}"><i class="nav-icon la la-microchip"></i> User request logs</a></li>
        </ul>
    </li>
    @endif

    <!-- @if(backpack_user()->can('manage transactions'))
    <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-comment-dollar"></i> Payment Gateway</a>
        <ul class="nav-dropdown-items">
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('transaction') }}"><i class="nav-icon la la-use"></i> List Transactions</a></li>
        </ul>
    </li>
    @endif -->


    @if(backpack_user()->can('manage vendors'))
    <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-user-alt"></i> Vendors</a>
        <ul class="nav-dropdown-items">
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('vendor') }}"><i class="nav-icon la la-user"></i> <span>List vendors</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('vendor-location') }}"><i class="nav-icon  la la-location-arrow"></i> Locations</a></li>
        </ul>
    </li>
    @endif

    @if(backpack_user()->can('manage authentication'))
    <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-users"></i> Settings </a>
        <ul class="nav-dropdown-items">
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> <span>Users</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i class="nav-icon la la-id-badge"></i> <span>Roles</span></a></li>
            <!-- <li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}"><i class="nav-icon la la-key"></i> <span>Permissions</span></a></li> -->
        </ul>
    </li>
    @endif

    @if(backpack_user()->can('manage filemanager'))
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('elfinder') }}"><i class="nav-icon la la-files-o"></i> <span>{{ trans('backpack::crud.file_manager') }}</span></a></li>
    @endif

    <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-industry"></i>Marketing</a>
        <ul class="nav-dropdown-items">
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('banner') }}"><i class="nav-icon la la-desktop"></i> Banners</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('coupon') }}"><i class="nav-icon la la-money-check-alt"></i> Coupons</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('notification') }}"><i class="nav-icon la la-bell"></i> Notifications</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('review') }}"><i class="nav-icon la la-user"></i> <span>List Reviews</span></a></li>
        </ul>
    </li>

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('help') }}"><i class="nav-icon la la-ticket-alt"></i> Inbox</a></li>



<li class="nav-item"><a class="nav-link" href="{{ backpack_url('reports') }}"><i class="nav-icon la la-file-text"></i> Reports</a></li>
