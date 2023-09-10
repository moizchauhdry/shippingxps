<template>
    <div class="card mt-2">
        <div class="card-header">
            <h3 class="text-uppercase">Storage Details</h3>
        </div>
        <div class="card-body">
            Start Date: {{ packag.created_at }} <br>
            Storage Days: {{ packag.storage_days }} <br>
            Storage Days Exceeded: {{ packag.storage_days_exceeded }} <br>
            Storage Fee: ${{ packag.storage_fee }} <br>
            <h6 class="mt-3">
                Note: The storage fee is added when a package exceeds 75 days, and after 80 days, the package is terminated
                or destroyed.
            </h6>
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
