<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Payment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="container">
                <h1>❌ Payment Cancelled 
                    @if (isset($subscriptionName))
                        {{ $subscriptionName }}
                    @endif</h1>
                <p>You can try again anytime.</p>
            </div>

        </div>

    </div>
</x-app-layout>
