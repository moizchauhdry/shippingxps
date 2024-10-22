<template>
    <MainLayout>
        <div class="card mb-5 mt-2">
            <div class="card-header">Manage Expense</div>
            <div class="card-body">
                <form @submit.prevent="submit" class="expense-form">
                    <div class="d-flex search">
                        <div class="form-group">
                            <label for="">Month</label>
                            <select v-model="form.month" class="form-control">
                                <template v-for="month in months">
                                    <option :value="month.id">{{ month.name }}</option>
                                </template>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Year</label>
                            <select v-model="form.year" class="form-control">
                                <template v-for="year in years">
                                    <option :value="year.value">{{ year.value }}</option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <button type="submit" class="btn btn-primary mr-1">Search</button>
                            <button type="button" class="btn btn-info" @click="clear()">Clear</button>
                        </div>
                    </div>
                </form>

                <div class="col-md-12">
                    <inertia-link :href="route('expense.create')" class="btn btn-success float-right">
                        <i class="fa fa-plus mr-1"></i>Add Expense</inertia-link>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" style="white-space: nowrap;text-transform: uppercase">
                        <thead class="bg-light">
                            <tr>
                                <th>SR #</th>
                                <th>Year</th>
                                <th>Month</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(expense, index) in expenses.data" :key="expense.id">
                                <td>{{ (expenses.current_page - 1) * expenses.per_page + index + 1 }}</td>
                                <td>{{ expense.year }}</td>
                                <td>{{ expense.month_name }}</td>
                                <td>{{ expense.title }}</td>
                                <td>{{ expense.description }}</td>
                                <td>${{ format_number(expense.amount) }}</td>
                                <td>
                                    <button class="btn btn-danger btn-sm" @click="deleteExpense(expense.id)">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="expenses.data.length == 0">
                                <td class="text-primary text-center" colspan="14">
                                    There are no expense found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <pagination :links="expenses.links" class="float-right"></pagination>
            </div>
        </div>
    </MainLayout>
</template>

<script>
import MainLayout from "@/Layouts/Main";
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import BreezeLabel from "@/Components/Label";
import Paginate from "@/Components/Paginate";
import Datepicker from "vue3-date-time-picker";
import "vue3-date-time-picker/dist/main.css";
import Pagination from "@/Components/Pagination.vue";
import { Inertia } from "@inertiajs/inertia";

export default {
    data() {
        return {
            form: {
                year: this.filters.year,
                month: this.filters.month,
            },

            expense_delete_form: this.$inertia.form({
                expense_id: "",
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
    components: {
        BreezeAuthenticatedLayout,
        MainLayout,
        BreezeLabel,
        Paginate,
        Datepicker,
        Pagination
    },
    props: {
        auth: Object,
        expenses: Object,
        filters: Object,
    },
    mounted() { },
    methods: {
        submit() {
            const queryParams = new URLSearchParams(this.form);
            const url = `${route("expense.index")}?${queryParams.toString()}`;
            Inertia.visit(url, { preserveState: true });
        },
        clear() {
            const current_date = new Date();
            const current_year = current_date.getFullYear();
            const current_month = current_date.getMonth() + 1;

            this.form = {
                year: current_year,
                month: current_month,
            };
            this.submit();
        },
        format_number(number) {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(number);
        },
        deleteExpense(id) {
            if (confirm('Are you sure you want to delete this expense?')) {
                this.expense_delete_form.expense_id = id;
                this.expense_delete_form.post(this.route("expense.destroy"));
            }
        }
    },
    created() {
        // 
    },
};
</script>


<style>
.dp__input {
    border-radius: 0px;
    padding: 4px 12px;
}

.expense-form .form-group {
    min-width: 200px;
    margin-right: 1px
}

/* Handle mobile view better */
@media (max-width: 768px) {
    .search {
        flex-wrap: wrap;
    }

    .form-group {
        flex: 1 1 100%;
        margin-bottom: 5px;
        width: 100%;
    }

    .stats-table {
        display: block;
        /* Convert table into a block layout */
    }

    .stats-table tr {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    .stats-table td {
        width: 100%;
        /* Full width on mobile */
        display: flex;
        justify-content: space-between;
        /* margin-bottom: 10px; */
        border: none;
        /* Remove borders between items */
        border-bottom: 1px solid #dee2e6;
        /* Optional separator */
    }

    .stats-table td:last-child {
        border-bottom: none;
        /* Remove last border */
    }
}
</style>