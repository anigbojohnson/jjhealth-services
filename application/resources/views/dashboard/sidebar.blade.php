<div class="sidebar">

    <div class="section-title">SERVICES</div>

    <a href="{{ route('my-certificate') }}"
       class="menu-item {{ request()->routeIs('my-certificate') ? 'active' : '' }}">
        My Certificate
    </a>

    <a  href="{{ route('my-result') }}"
       class="menu-item {{ request()->routeIs('my-result') ? 'active' : '' }}">
        My Result
    </a>

    <a href="{{ route('my-referrals') }}"
       class="menu-item {{ request()->routeIs('my-referrals') ? 'active' : '' }}">
        My Referrals
    </a>

    <div class="section-title">SETTINGS</div>

    <a href="{{ route('view-profile') }}"
       class="menu-item {{ request()->routeIs('my-profile') ? 'active' : '' }}">
        My Profile
    </a>

</div>
    <style>  
        .dashboard{display:flex;min-height:80vh;}
        .menu-item:hover {background:#d7eee3;}
        .menu-item.active {background: #D7EEE3;color: #245C42;font-weight:600;}
        .section-title{font-size:12px;font-weight:bold;color:#7d7d7d;margin-bottom:12px;letter-spacing:1px;}
        .menu-item{display:block;padding:12px;margin-bottom:8px;text-decoration:none;color:#333;border-radius:8px;}
        .menu-item:hover{background:#d7eee3;}
        .menu-item{display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;
             margin-bottom:10px;text-decoration:none;color:#2d2d2d;font-size:15px;transition:.2s;}
        .menu-item:hover{transform:translateX(3px);}
        .menu-item i{font-size:18px;}    
        .sidebar{ width:240px; background: #E8F7EF;border-right:1px solid #e5e7eb;padding:18px;}
    </style>