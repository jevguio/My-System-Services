<x-app-layout>
    <form id="payment-form" action="{{ route('subscribe') }}" method="POST">
        @csrf
        <input id="card-holder-name" type="text" placeholder="Cardholder Name">
        <div id="card-element"></div>
        <input type="hidden" name="paymentMethod" id="paymentMethod">
        <button id="card-button" data-secret="{{ $intent->client_secret }}">Subscribe</button>
    </form>
    
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe('{{ env("STRIPE_KEY") }}');
        const elements = stripe.elements();
        const cardElement = elements.create('card');
        cardElement.mount('#card-element');
    
        const form = document.getElementById('payment-form');
        const cardHolderName = document.getElementById('card-holder-name');
        const cardButton = document.getElementById('card-button');
        const clientSecret = cardButton.dataset.secret;
    
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
    
            const {paymentMethod, error} = await stripe.createPaymentMethod(
                'card', cardElement, {
                    billing_details: {name: cardHolderName.value}
                }
            );
    
            if (error) {
                alert(error.message);
            } else {
                document.getElementById('paymentMethod').value = paymentMethod.id;
                form.submit();
            }
        });
    </script>
    
</x-app-layout>
