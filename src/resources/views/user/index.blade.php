@include('swapnil-starterkit::components.header',['title' => 'Users'])
<div class="container-fluid">
    @include('swapnil-starterkit::components.nav')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h1 class="display-6">Users</h1>
                </div>
                <div class="card-body">
                    <div class="row row-cols-1 row-cols-md-3 g-4">
                        @foreach($users as $user)
                            <div class="col-lg-2">
                                <div class="card h-100">
                                    <img
                                        src="https://png.pngtree.com/png-vector/20240813/ourlarge/pngtree-vector-add-user-icon-png-image_13468811.png"
                                        height="150px" class="card-img-top rounded" alt="...">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $user->name }}</h5>
                                        <p class="card-text">{{ $user->email }}</p>
                                    </div>
                                    <div class="card-footer">
                                        <a href="#" class="btn btn-primary">Profile</a>
                                        <a data-id="{{$user->id}}" id="deleteUser" class="btn btn-danger">Delete</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="mx-5">
                    {{ $users->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('scripts')
    <script>
        $(document).on('click','#deleteUser', function(e) {
            "use strict";
            e.preventDefault();
            let url = '{{ route('delete_user',['id' => ':id']) }}';
            let id = $(this).data('id');
            let href = url.replace(':id', id);
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    </script>
@endpush
@include('swapnil-starterkit::components.footer')
