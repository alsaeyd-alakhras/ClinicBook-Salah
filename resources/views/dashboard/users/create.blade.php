<x-front-layout>
    <form action="{{route('dashboard.users.store')}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @include("dashboard.users._form")
    </form>
</x-front-layout>
