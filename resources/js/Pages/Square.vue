<template>
    <MainLayout>
        <h1>Square</h1>
        <div>
            <form @submit.prevent="handlePayment">
                <div id="card-container"></div>
                <button id="card-button" type="submit" :disabled="submitting">Pay $1.00</button>
            </form>
            <div id="payment-status-container"
                :class="{ 'is-success': paymentSuccess, 'is-failure': paymentFailure, 'missing-credentials': missingCredentials }"
                v-if="paymentStatusVisible">
                {{ paymentStatusMessage }}
            </div>
        </div>
    </MainLayout>
</template>

<script>
import MainLayout from "@/Layouts/Main";

export default {
    data() {
        return {
            appId: 'sandbox-sq0idb-jeE29DTw_SfJ52vT7ZM7IA',
            locationId: 'L8PVP5B7XVYDR',
            submitting: false,
            paymentSuccess: false,
            paymentFailure: false,
            missingCredentials: false,
            paymentStatusVisible: false,
            paymentStatusMessage: ''
        };
    },
    mounted() {
        // this.loadSquareScript();
        const payments = Square.payments('sq0idp-P9dzLXrd8KM4Zat_hu82RQ', 'LBYSV1XNZV0FX'); // production

        const card = await payments.card();
        await card.attach('#card-container');

        const cardButton = document.getElementById('card-button');
        cardButton.addEventListener('click', async () => {
            const statusContainer = document.getElementById('payment-status-container');

            try {
                const result = await card.tokenize();
                if (result.status === 'OK') {
                    $.ajax({
                        method: "POST",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            'payment_token': result.token,
                            'package_id': '{{$package->id}}',
                        },
                        url: '/test',
                        beforeSend: function () {
                            $(".livewire-loader").removeClass('hidden');
                        },
                        success: function (response) {
                            console.log(response);
                            if (response.code == 200) {
                                var url = "{{ route('square.complete') }}";
                                location.href = url;
                            } else {
                                alert('PAYMENT ERROR!');
                                $(".livewire-loader").addClass('hidden');
                            }
                        },
                        error: function (errors) {
                            console.log(errors);
                            alert('SYSTEM ERROR!');
                            $(".livewire-loader").addClass('hidden');
                        }
                    });

                } else {
                    let errorMessage = `Tokenization failed with status: ${result.status}`;
                    if (result.errors) {
                        errorMessage += ` and errors: ${JSON.stringify(
                            result.errors
                        )}`;
                    }

                    throw new Error(errorMessage);
                }
            } catch (e) {
                console.error(e);
                statusContainer.innerHTML = "Payment Failed";
            }
        });
    },
    components: {
        MainLayout,
    },
    methods: {
        loadSquareScript() {
            const script = document.createElement('script');
            script.src = 'https://sandbox.web.squarecdn.com/v1/square.js';
            script.type = 'text/javascript';
            script.async = true;
            script.onload = () => {
                this.initializeSquare();
            };
            document.head.appendChild(script);
        },
        initializeSquare() {
            if (!window.Square) {
                this.missingCredentials = true;
                this.paymentStatusVisible = true;
                return;
            }

            try {
                const payments = window.Square.payments(this.appId, this.locationId);
                this.card = this.initializeCard(payments);
            } catch (error) {
                console.error('Initializing Card failed', error);
            }
        },
        initializeCard(payments) {
            const card = payments.card();
            card.attach('#card-container');
            return card;
        },
        async handlePayment(event) {
            this.submitting = true;
            try {
                const token = await this.tokenize(this.card);
                const verificationToken = await this.verifyBuyer(token);
                await this.createPayment(token, verificationToken);
                this.paymentSuccess = true;
                this.paymentFailure = false;
                this.paymentStatusVisible = true;
                this.paymentStatusMessage = 'Payment successful!';
            } catch (error) {
                console.error(error.message);
                this.paymentSuccess = false;
                this.paymentFailure = true;
                this.paymentStatusVisible = true;
                this.paymentStatusMessage = 'Payment failed!';
            } finally {
                this.submitting = false;
            }
        },
        async tokenize(paymentMethod) {
            const tokenResult = await paymentMethod.tokenize();
            if (tokenResult.status === 'OK') {
                return tokenResult.token;
            } else {
                let errorMessage = `Tokenization failed with status: ${tokenResult.status}`;
                if (tokenResult.errors) {
                    errorMessage += ` and errors: ${JSON.stringify(tokenResult.errors)}`;
                }
                throw new Error(errorMessage);
            }
        },
        async verifyBuyer(token) {
            // Your verifyBuyer function logic here
        },
        async createPayment(token, verificationToken) {
            // Your createPayment function logic here
        }
    }
};
</script>