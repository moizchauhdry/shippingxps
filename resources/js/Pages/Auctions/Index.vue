<template>
    <Header></Header>

    <div class="container">
        <div class="card shadow p-4">
            <div class="row mb-4">
                <div class="col-md-12">
                    <h1 class="text-primary text-center text-lg"><u>Auctions</u></h1>
                </div>
            </div>
            <div class="row mb-4">
                <template v-for="pkg in packages.data" :key="pkg.id">
                    <div class="col-md-3 mb-2">
                        <div class="card">
                            <div class="card-header">{{ pkg.id }}</div>
                            <div class="card-body">
                                <img src="" class="card-img-top" alt="...">
                            </div>
                            <div class="card-footer">Buy it now - <b>$100</b></div>
                        </div>
                    </div>
                </template>
            </div>
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <Pagination :links="packages.links" class="float-right"></Pagination>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

import Header from '@/Components/Header.vue'
import Pagination from "@/Components/Pagination.vue";

export default {
    props: {
        errors: Object,
        packages: Object,
    },
    components: {
        Header, Pagination
    },
    data() {
        return {

        }
    },
    computed: {
    },
    methods: {


    }
}
</script>