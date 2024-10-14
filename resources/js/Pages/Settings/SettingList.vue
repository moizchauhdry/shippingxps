<template>
    <MainLayout>
        <div class="card">
            <div class="card-header font-bold">General Settings</div>
            <div class="card-body">

                <form @submit.prevent="importData()" enctype="multipart/form-data">
                    <table class="table table-sm table-striped table-bordered">
                        <tbody>
                            <tr>
                                <th colspan="3" class="text-center text-lg">FEDEX/DHL/UPS/USPS Net Charge Amount</th>
                            </tr>
                            <tr>
                                <td class="text-uppercase">
                                    <input type="file" class="form-control" id="input13" @change="handleFileChange">
                                </td>
                                <td>
                                    <button class="btn btn-primary" type="submit">Import File</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>

                <form @submit.prevent="submit">
                    <table class="table table-sm table-striped table-bordered">
                        <tbody>

                            <tr>
                                <th colspan="3" class="text-center text-lg">Fee & Charges</th>
                            </tr>
                            <tr v-for="setting in form.settings" :key="setting.id">
                                <td class="text-uppercase">{{ setting.name }}</td>
                                <td>
                                    <input name="title" id="title" type="text" class="form-control" placeholder="Title"
                                        v-model="setting.value" required />
                                </td>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-center text-lg">Shipping Services - Markup Percentage</th>
                            </tr>
                            <tr v-for="service in form.shipping_services" :key="service.id">
                                <td class="text-uppercase">{{ service.service_name }}</td>
                                <td>
                                    <input type="text" class="form-control" placeholder="Markup Percentage"
                                        v-model="service.markup_percentage" required />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <input type="submit" value="Update Settings" class="btn btn-success float-right" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </MainLayout>
</template>

<script>
import MainLayout from '@/Layouts/Main'
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import BreezeLabel from "@/Components/Label";
import Paginate from "@/Components/Paginate";
import Datepicker from "vue3-date-time-picker";
import "vue3-date-time-picker/dist/main.css";
import Pagination from "@/Components/Pagination.vue";
import { Inertia } from "@inertiajs/inertia";

export default {
    components: {
        BreezeAuthenticatedLayout,
        MainLayout,
    },
    data() {
        return {
            form: this.$inertia.form({
                settings: this.settings,
                shipping_services: this.shipping_services,
            }),
            carrier_cost_form: this.$inertia.form({
                file: null,
                type: 1,
            }),
        };
    },
    props: {
        auth: Object,
        settings: Object,
        shipping_services: Object,
    },
    methods: {
        submit() {
            this.form.post(this.route('settings.update'))
        },
        importData() {
            this.carrier_cost_form.post(this.route("report.import-carrier-cost"))
                .then(response => {
                    this.close();
                })
                .catch(error => {
                    //
                })
                .finally(() => {
                    //
                });
        },
        handleFileChange(event) {
            const selected_file = event.target.files[0];
            this.carrier_cost_form.file = selected_file;
        }
    }
}
</script>

<style scoped>
.label {
    padding: 5px;
}
</style>
