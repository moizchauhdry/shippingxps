<template>
    <MainLayout>
		<div class="card mt-4">
            <div class="card-body">

                <div class="row">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
                        Manage Packages
                    </h2>
                </div>
                <div class="row" style="margin-top:30px;">
                    <div class="col-md-6" >
                        <form action="/orders" method="get">
                            <input type="text" name="search" class="form-control" placeholder="Search">
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead class="text-center">
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Package</th>
                      <th scope="col">Warehouse</th>
                      <th scope="col">Customer</th>
                      <th scope="col">Status</th>
                      <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <tr v-for="pkg in packages.data" :key="pkg.id">
                      <td>{{ pkg.id }}</td>
                      <td>{{ pkg.package_no }}</td>
                      <td>{{ pkg.warehouse.name }}</td>
                      <td>
                        <inertia-link :href="route('detail-customer', pkg.customer.id)" class="btn btn-link">
                          # {{ siuteNum(pkg.customer.id) }} - {{ pkg.customer.name }}
                        </inertia-link>
                      </td>
                      <td>
                        <span v-bind:class="getLabelClass(pkg.status)">{{ pkg.status }}</span>
                      </td>
                      <td>
                        <inertia-link class="link-primary" :href="route('packages.show', pkg.id)">
                          <span>View</span>
                        </inertia-link>

                        <template  v-if="$page.props.auth.user.type == 'admin' && pkg.status!='open'">
                          &nbsp;|&nbsp;
                          <a target= '_blank' class="link-success" :href="route('packages.pdf', pkg.id)">
                            <span>Print Declaration Form</span>
                          </a>
                        </template>
                      </td>
                    </tr>
                    </tbody>
                  </table>
                </div>
                <pagination class="mt-6" :links="packages.links" />
            </div>
        </div>
    </MainLayout>
</template>
<style scoped>
.label{
    padding:5px;
}
</style>
<script>
    import MainLayout from '@/Layouts/Main'
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'

    import Pagination from '@/Components/Pagination'
    export default {
        components: {
            BreezeAuthenticatedLayout,
            MainLayout,

            Pagination
        },
        props: {
            auth: Object,
            packages:Object,
        },
        data() {
            return {
                form: this.$inertia.form({
                        search: ''
                }),
                params:{
                    search:null,
                },
            }
        },
        watch:{
            params:{
                handler(){
                this.$inertia.get(this.route('orders'),this.params,{replace:true,preserveState:true});
                }
            }
        },
        methods:{
            getLabelClass(status){
                switch(status) {
                case 'open':
                    return 'label bg-success';
                    break;
                case 'labeled':
                    return 'label bg-info';
                    break;
                case 'shipped':
                    return 'label bg-warning';
                    break;
                case 'delivered':
                    return 'label bg-success';
                    break                   
                case 'rejected':
                    return 'label bg-danger';
                    break;
                default:
                    return 'label bg-primary';
                }
            },
            pkgImgURL(pkg) {
                if(pkg.hasOwnProperty("image")){
                    return "/uploads/"+pkg.image;
                }
                return "/uploads/no-image.jpg";
            },
            siuteNum(user_id){
                return 4000 + user_id;
            },
        }
    }
</script>
