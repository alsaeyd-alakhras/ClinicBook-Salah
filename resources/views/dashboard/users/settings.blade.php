<x-front-layout>
    <form action="{{route('dashboard.users.update',Auth::user()->id)}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @method('put')
        @include("dashboard.users._form",['settings_profile' => $settings_profile])
    </form>
</x-front-layout>
