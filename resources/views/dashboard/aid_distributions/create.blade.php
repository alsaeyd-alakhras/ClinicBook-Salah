<x-front-layout>
    <form action="{{ route('dashboard.aid-distributions.store') }}" method="post" class="col-12">
        @csrf
        @include('dashboard.aid_distributions._form')
    </form>
</x-front-layout>
