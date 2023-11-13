<template>
    <MainLayout>
        <div class="card">
            <div class="card-header font-bold">Settings</div>
            <div class="card-body">
                <form @submit.prevent="submit">
                    <table class="table table-sm table-striped table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Description</th>
                                <th scope="col">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>General</td>
                            </tr>
                            <tr v-for="setting in form.settings" :key="setting.id">
                                <td>{{ setting.name }}</td>
                                <td>{{ setting.description ?? '-' }}</td>
                                <td>
                                    <input name="title" id="title" type="text" class="form-control" placeholder="Title"
                                        v-model="setting.value" required />
                                </td>
                            </tr>
                              <tr>
                                <td>Shipping Services</td>
                            </tr>
                            <tr v-for="service in form.shipping_services" :key="service.id">
                                <td>{{ service.service_name }}</td>
                                <td>-</td>
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
            })
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
        }
    }
}
</script>

<style scoped>
.label {
    padding: 5px;
}
</style>
