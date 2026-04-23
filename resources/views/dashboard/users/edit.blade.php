<x-front-layout>
    <form action="{{route('dashboard.users.update',$user->id)}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @method('put')
        @include("dashboard.users._form")
    </form>
</x-front-layout>
