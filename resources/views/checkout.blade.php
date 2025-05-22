<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <form action="{{ route('checkout.process') }}" method="POST">
        @csrf
        <input id="card-holder-name" type="text" placeholder="Cardholder Name">

        <!-- Stripe Elements Placeholder -->
        <div id="card-element"></div>

        <button id="card-button" type="submit" data-secret="{{ $intent->client_secret }}">
            Pay
        </button>
    </form>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe('{{ env('STRIPE_KEY') }}');
        const elements = stripe.elements();
        const card = elements.create('card');
        card.mount('#card-element');
    </script>

</x-app-layout>
