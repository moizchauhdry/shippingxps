<template>
    <div class="table-responsive">
        <table class="table table-striped table-bordered text-center text-sm">
            <thead>
                <tr>
                    <th scope="col">SR #</th>
                    <th scope="col">Package #</th>
                    <th scope="col">Customer</th>
                    <th scope="col">Warehouse</th>
                    <th scope="col">Created Date</th>
                    <th scope="col">Status</th>
                    <template v-if="auth.user.type == 'admin' || auth.user.type == 'manager'">
                    <th scope="col"></th>
                    </template>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(pkg,index) in pkgs" :key="pkg.id">
                    <td>{{ ++index }}</td>
                    <td>
                        <a :href="route('packages.show', pkg.id)"><span class="badge badge-primary">PKG #{{ pkg.id }}</span></a>
                        <p>Location: <strong>{{ pkg.warehouse.name }}</strong></p>
                        <p v-if="pkg.package_length > 0">
                            Dimentions : {{ pkg.package_length }} {{ pkg.dim_unit }} x {{ pkg.package_width }} {{ pkg.dim_unit }} x {{ pkg.package_height }} 
                            {{ pkg.dim_unit }} 
                        </p>
                        <p>Shipped :  <strong>{{ pkg.status == "shipped" ? "Yes" : "No"}}</strong></p>
                    </td>
                    <td>
                        <inertia-link :href="route('detail-customer', pkg?.customer?.id)" class="btn btn-link">
                        # {{ siuteNum(pkg?.customer?.id) }} - {{ pkg?.customer?.name }}
                        </inertia-link>
                    </td>

                    <td>{{ pkg?.warehouse?.name }}</td>
                    <td>{{ pkg.created_at }}</td>
                    <td>
                        <span v-bind:class="getLabelClass(pkg.status)">{{ pkg.status }}</span>
                    </td>
                    <template v-if="auth.user.type == 'admin' || auth.user.type == 'manager'">
                    <td>
                        <!-- <inertia-link class="btn btn-primary btn-sm m-1" :href="route('package.edit', pkg.id)"><i class="fa fa-pencil-alt mr-1"></i>Edit</inertia-link> -->
                        <inertia-link class="btn btn-info btn-sm m-1" :href="route('packages.show', pkg.id)"><i class="fa fa-list mr-1"></i>Detail</inertia-link>
                    </td>
                    </template>
                </tr>
                <tr v-if="pkgs.length == 0">
                    <td class="text-primary text-center text-uppercase" colspan="9">There are no packages found.</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        name: "Order List",
        props: {
            auth: Object,
            pkgs:Object,
            filter:Object, 
        },
        data() {
            return {
                // 
            }
        },
        methods:{
            getLabelClass(status){
                switch(status) {
                    case 'arrived':
                    return 'text-uppercase badge badge-success p-2 text-white';
                    break;
                    case 'labeled':
                    return 'text-uppercase badge badge-info p-2 text-white';
                    break;
                    case 'shipped':
                    return 'text-uppercase badge badge-warning p-2';
                    break;
                    case 'delivered':
                    return 'text-uppercase badge badge-success p-2 text-white';
                    break
                    case 'rejected':
                    return 'text-uppercase badge badge-danger p-2 text-white';
                    break;
                    default:
                    return 'text-uppercase badge badge-primary p-2 text-white';
                }
            },
            siuteNum(user_id){
                return 4000 + user_id;
            },
        }
    }
</script>
