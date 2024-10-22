<template>
    <div class="card mt-2">
        <div class="card-header">
            <h3 class="text-uppercase">Storage Details</h3>
        </div>
        <div class="card-body">
            Start Date: {{ packag.created_at }} <br>
            Total Days: {{ packag.storage_days }} <br>
            Days Exceeded: {{ packag.storage_days_exceeded }} <br>
            Storage Charges: ${{ packag.storage_fee }} <br>
            <h6 class="mt-3">
                Note: The storage fee is added when a package exceeds 75 days, and after 80 days, the package is
                terminated
                and becomes the property of ShippingXPS.
            </h6>

            <br>

            <h1 class="text-danger text-lg">Effective December 1, 2024, the storage policy will be updated as follows: </h1>
            <ul class="">
                <li>
                    * Storage days will change from 75 days to 30 days.
                </li>
                <li>
                    * A storage fee will apply to packages held for more than 30 days.
                </li>
                <li>
                    * Packages remaining beyond 35 days will be terminated and become the property of ShippingXPS.
                </li>
                <li>
                    * For the five-day period between 30 and 35 days, a daily fee will be charged.
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
    name: "Storage Component",
    props: {
        packag: Object,
    },
    data() {
        return {
            // 
        };
    },
    methods: {
        // 
    },
};
</script>
