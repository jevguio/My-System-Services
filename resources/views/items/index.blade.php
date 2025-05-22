<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Items') }}
        </h2>
    </x-slot>

    <style>
        .grid {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        form {
            border: 1px solid #ccc;
            padding: 1rem;
            border-radius: 8px;
            width: 200px;
        }

        button {
            background-color: #28a745;
            color: white;
            padding: 0.5rem;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }
    </style>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @foreach ($items as $item)
                <div class="item bg-gray-600 dark:bg-gray-800 p-3">
                    <h3 class="text-gray-900 dark:text-gray-200">{{ $item->name }}</h3>
                    <p class="text-gray-700 dark:text-gray-400">Price: ${{ $item->price }}</p>

                    @php
                        $isSubscribed = auth()
                            ->user()
                            ->subscriptions()
                            ->where('stripe_price', '=', $item->stripe_price_id)
                            ->first();
                    @endphp

                    @if ($isSubscribed)
                        <p>You’re subscribed to this item.</p>
                    @else
                        <form action="{{ route('subscribe', $item->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="name" value="{{  $item->name  }}">
                            <input type="hidden" name="price_id" value="{{ $item->stripe_price_id }}">
                            <button type="submit" class="btn btn-primary bg-green-700 text-white">Subscribe</button>
                        </form>
                    @endif
                </div>
            @endforeach





        </div>
    </div>
</x-app-layout>
