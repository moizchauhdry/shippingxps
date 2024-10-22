<template>
    <MainLayout>
        <form @submit.prevent="submit" enctype="multipart/form-data">
            <div class="card mb-5">
                <div class="card-header">
                    <span class="font-bold">Add Monthly Expense</span>
                </div>
                <div class="card-body">
                    <breeze-validation-errors class="mb-4 text-lg" />

                    <button type="button" @click="addItem" class="btn btn-success float-right mb-2">
                        <i class="fas fa-plus"></i> Add Item
                    </button>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="bg-primary text-white text-center" colspan="8">
                                        Expense Items
                                    </th>
                                </tr>
                                <tr>
                                    <th>Year</th>
                                    <th>Month</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="(item, index) in form.items">
                                    <tr>
                                        <td>
                                            <select v-model="item.year" class="form-control">
                                                <template v-for="year in years">
                                                    <option :value="year.value">{{ year.value }}</option>
                                                </template>
                                            </select>
                                        </td>
                                        <td>
                                            <select v-model="item.month" class="form-control">
                                                <template v-for="month in months">
                                                    <option :value="month.id">{{ month.name }}</option>
                                                </template>
                                            </select>
                                        </td>
                                        <td>
                                            <input v-model="item.title" type="text" class="form-control"
                                                placeholder="Title" />
                                        </td>
                                        <td>
                                            <input v-model="item.description" type="text" class="form-control"
                                                placeholder="Description" />
                                        </td>
                                        <td>
                                            <input v-model="item.amount" type="number" min="0" step="0.01"
                                                class="form-control" />
                                        </td>
                                        <td>
                                            <button type="button" @click="removeItem(index)" class="btn btn-link"
                                                :disabled="index == 0">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>

                                <tr>
                                    <th colspan="4" class="text-right">Total Amount</th>
                                    <td>
                                        {{ grandTotal }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="order-button">
                        <input type="submit" value="Save & Submit" class="btn btn-success float-right" />
                    </div>
                </div>
            </div>
        </form>
    </MainLayout>
</template>

<script>
import MainLayout from "@/Layouts/Main";
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import BreezeLabel from "@/Components/Label";
import BreezeValidationErrors from "@/Components/ValidationErrors";

export default {
    components: {
        BreezeAuthenticatedLayout,
        MainLayout,
        BreezeLabel,
        BreezeValidationErrors,
    },
    data() {
        return {
            form: this.$inertia.form({
                items: [
                    {
                        year: 2024,
                        month: 10,
                        title: "",
                        description: "",
                        amount: "",
                    },
                ],
                grand_total: 0
            }),
            months: [
                { id: 1, name: "January" },
                { id: 2, name: "February" },
                { id: 3, name: "March" },
                { id: 4, name: "April" },
                { id: 5, name: "May" },
                { id: 6, name: "June" },
                { id: 7, name: "July" },
                { id: 8, name: "August" },
                { id: 9, name: "September" },
                { id: 10, name: "October" },
                { id: 11, name: "November" },
                { id: 12, name: "December" },
            ],
            years: [
                { id: 1, value: "2020" },
                { id: 2, value: "2021" },
                { id: 3, value: "2022" },
                { id: 4, value: "2023" },
                { id: 5, value: "2024" },
                { id: 6, value: "2025" },
                { id: 7, value: "2026" },
                { id: 8, value: "2027" },
                { id: 9, value: "2028" },
                { id: 10, value: "2029" },
                { id: 11, value: "2030" },
            ]
        };
    },
    props: {
        auth: Object,
    },
    computed: {
        grandTotal() {
            return this.form.items.reduce((total, item) => total + (item.amount || 0), 0);
        }
    },
    methods: {
        submit() {
            this.form.post(this.route("expense.store"));
        },
        addItem() {
            this.form.items.push({
                year: 2024,
                month: 10,
                title: "",
                description: "",
                amount: "",
            });
        },
        removeItem(index) {
            this.form.items.splice(index, 1);
            this.getGrandTotal();
        },
        changeWarehouse() {
            this.$refs.price_online[0].click();
        },
        getGrandTotal() {
            var sum = 0;
            this.form.total = 0;

            this.form.items.forEach(function (n) {
                sum += parseFloat(n["sub_total"]);
            });

            this.form.sub_total = parseFloat(sum).toFixed(2);
            var total = parseFloat(sum) + parseFloat(this.form.service_charges);
            this.form.total = parseFloat(total).toFixed(2);
        }
    },
};
</script>
