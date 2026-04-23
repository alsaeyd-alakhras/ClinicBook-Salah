<x-front-layout>
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5 class="card-title mb-0">جدول المستخدمين</h5>
            <div class="d-flex align-items-center">
                @can('create', 'App\\Models\User')
                    <a class="btn btn-success" href="{{route('dashboard.users.create')}}">
                        <i class="fa-solid fa-plus"></i>
                    </a>
                @endcan
            </div>
        </div>
        <style>
            td{
                color: #000 !important;
            }
        </style>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>اسم المستخدم</th>
                            <th>البريد الالكتروني</th>
                            <th>الحالة</th>
                            <th>أخر موعد تواجد</th>
                            <th>المكتب</th>
                            <th>الحدث</th>
                        </tr>
                    </thead>
                    <tbody>
                        <style>
                            #user-1{
                                display: none;
                            }
                        </style>
                        @foreach($users as $user)
                        <tr  id="user-{{$user->id}}">
                            <td style="width: 10px">{{ $loop->iteration - 1 }}</td>
                            <td class="d-flex align-items-center">
                                <div class="avatar avatar-{{ $user->last_activity >= now()->subMinutes(5) ? 'online' : 'offline' }}">
                                    <img src="{{$user->avatar_url}}" alt="" class="rounded-circle">
                                </div>
                                {{$user->name}}
                            </td>
                            <td>{{$user->username}}</td>
                            <td>{{$user->email}}</td>
                            <td>
                                @if($user->last_activity >= now()->subMinutes(5))
                                <span class="badge bg-label-success me-1">نشط</span>
                                @else
                                <span class="badge bg-label-danger me-1">غير نشط</span>
                                @endif
                            </td>
                            <td>{{$user->last_activity}}</td>
                            <td>{{$user->office?->name}}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @can('view', 'App\\Models\User')
                                        <a class="dropdown-item" style="margin: 0.5rem -0.75rem; text-align: right;" href="{{route('dashboard.users.show',$user->id)}}">
                                            <i class="ti ti-eye me-1"></i>عرض
                                        </a>
                                        @endcan
                                        @can('update', 'App\\Models\User')
                                        <a class="dropdown-item" style="margin: 0.5rem -0.75rem; text-align: right;" href="{{route('dashboard.users.edit',$user->id)}}">
                                            <i class="ti ti-pencil me-1"></i>تعديل
                                        </a>
                                        @endcan
                                        @can('delete', 'App\\Models\User')
                                        <form action="{{route('dashboard.users.destroy',$user->id)}}" method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="dropdown-item" style="margin: 0.5rem -0.75rem; text-align: right;">
                                                <i class="ti ti-trash me-1"></i>حذف
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-front-layout>
