<template>
    <div class="row" v-if="packag.status!='open' && packag.pkg_dim_status == 'done'">
        <div class="col-md-12">
        <div class="card mt-2">
            <div class="card-header">
            <h3 class="text-uppercase">{{ packag.pkg_type }} Package</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4" v-for="box in packag.boxes" :key="box.id">
                        <div class="card shadow p-4">
                            <div>Dimension: <b>{{ box.length }} x {{ box.width }} x {{ box.height }} {{ box.dim_unit }}</b></div>
                            <div>Weight: <b>{{ box.weight }} {{ box.weight_unit}}</b></div>
                            <div>Tracking In: <b>{{ packag.tracking_number_in ?? 'N/A' }}</b></div>
                            <div>Tracking Out: <b>{{ box.tracking_number_out ?? 'N/A' }}</b></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Package Box Component",
        props: {
            packag:Object,
        },
        data() {
            return {
                // 
            }
        },
        methods:{
            // 
        }
    }
</script>