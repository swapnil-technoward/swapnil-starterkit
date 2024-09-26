<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">Sidebar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="list-group">
            <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action {{ (Request::is('dashboard') ? 'active' : '') }}" aria-current="true">
                <i class="fa fa-home"></i> Dashboard
            </a><br>
            <a href="{{ route('users') }}" class="list-group-item list-group-item-action {{ (Request::is('users') ? 'active' : '') }}"> <i class="fa fa-user"></i> Users</a><br>
            <a href="{{ route('logout') }}" class="list-group-item list-group-item-action"><i class="fa fa-sign-out"></i> Logout</a>
        </div>
    </div>
</div>
