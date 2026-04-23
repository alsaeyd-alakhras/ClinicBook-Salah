<x-front-layout>
    <form action="{{ route('dashboard.aid-distributions.update', $distribution->id) }}" method="post" class="col-12">
        @csrf
        @method('put')
        @include('dashboard.aid_distributions._form')
    </form>
</x-front-layout>
