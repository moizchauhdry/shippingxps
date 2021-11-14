<template>
    <MainLayout>
		<div class="card mt-4">
            <div class="card-body">
                 <FlashMessages />
                <div class="row">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
                        Manage Packages
                    </h2>
                </div>
                <div class="row" style="margin:30px 0px;">
                    <div class="col-md-6" >
                        <form action="/orders" method="get">
                            <input type="text" name="search" class="form-control" placeholder="Search">
                        </form>
                    </div>
                </div>

                    <div class="row">
                        <div class="col-md-12">
                            <ul class="nav nav-pills nav-justified mb-3" id="pills-tab " role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button 
                                    v-on:click="setActiveTabAB('tab1')" 
                                    :class="getTabClass('tab1')" 
                                    id="pills-home-tab" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#pills-home" 
                                    type="button" 
                                    role="tab" 
                                    aria-controls="pills-home" 
                                    aria-selected="true">Packages In Account</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button 
                                    v-on:click="setActiveTabAB('tab2')" 
                                    :class="getTabClass('tab2')" 
                                    id="pills-profile-tab" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#pills-profile" 
                                    type="button" 
                                    role="tab" 
                                    aria-controls="pills-profile" 
                                    aria-selected="false">Ready for Mail Out</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button 
                                    v-on:click="setActiveTabAB('tab3')" 
                                    :class="getTabClass('tab3')" 
                                    id="pills-profile-tab" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#pills-profile" 
                                    type="button" 
                                    role="tab" 
                                    aria-controls="pills-profile" 
                                    aria-selected="false">Sent</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button 
                                    v-on:click="setActiveTabAB('tab4')" 
                                    :class="getTabClass('tab4')" 
                                    id="pills-profile-tab" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#pills-profile" 
                                    type="button" 
                                    role="tab" 
                                    aria-controls="pills-profile" 
                                    aria-selected="false">Thrashed</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="pills-tabContent">                                
                                <div :class="getTabPaneClass('tab1')" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" style="background-color:white;">                                                                        
                                    <table class="table">
                                        <thead>
                                            <tr>
                                            <th scope="col">#</th>
                                            <!-- <th scope="col">Image</th> -->
                                            <th scope="col">Package</th>
                                            <th scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="pkg in packages_account.data" :key="pkg.id">
                                            <td>{{ pkg.id }}</td>
                                            <!--
                                             <td>
                                                <div class="text-center">
                                                    <img style="width:100px;height:auto;" class="img-thumbnail" :src="pkgImgURL(pkg)" />
                                                </div>
                                            </td>
                                             -->
                                            <td>
                                                <div class="package-no"><a :href="route('packages.show', pkg.id)">{{ pkg.package_no }}</a></div>
                                                <p>Location: <strong>{{ pkg.warehouse.name }}</strong></p>
                                                <p>Status: <strong>{{ pkg.status }}</strong></p>
                                                <p>Tracking IN: <strong>N/A</strong></p>
                                                <p>Tracking Out: <strong>{{ pkg.tracking_number_out }}</strong></p>
                                                <p v-if="pkg.package_length > 0">Dimentions : {{ pkg.package_length }} {{ pkg.dim_unit }} x {{ pkg.package_width }} {{ pkg.dim_unit }} x {{ pkg.package_height }} {{ pkg.dim_unit }} </p>
                                                <p>Entered :  {{ pkg.created_at }}</p>
                                                <p>Shipped :  N/A</p>      
                                                <p>Cosolidated From:  N/A</p>                   
                                            </td>
                                            <td>
                                                <inertia-link class="link-primary" :href="route('packages.show', pkg.id)">                                                    
                                                    <span>Details</span>
                                                </inertia-link>
                                                <template v-if="pkg.status == 'consolidated'">
                                                    &nbsp; | &nbsp;
                                                    <a class="link-primary" :href="route('packages.custom', pkg.id)">Customs Declaration Form</a>
                                                </template>
                                                <template v-if="printable.includes(pkg.status)">
                                                    &nbsp; | &nbsp;
                                                    <a class="link-primary" target="_blank" :href="route('packages.pdf', pkg.id)">Print Commercial Invoice</a>
                                                </template>                                                
                                                <template v-if="pkg.status == 'labeled'">
                                                    &nbsp; | &nbsp;
                                                    <p style="color:green;">Package has been labeled, You can select shipping service and continue.</p>
                                                </template>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div :class="getTabPaneClass('tab2')" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" style="background-color:white;">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                            <th scope="col">#</th>
                                              <!-- <th scope="col">Image</th> -->
                                            <th scope="col">Package</th>
                                            <th scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="pkg in packages_ready.data" :key="pkg.id">
                                            <td>{{ pkg.id }}</td>
                                          <!-- <td>
                                                <div class="text-center">
                                                    <img style="width:100px;height:auto;" class="img-thumbnail" :src="pkgImgURL(pkg)" />
                                                </div>
                                            </td> -->
                                            <td>
                                                <div class="package-no"><a :href="route('packages.show', pkg.id)">{{ pkg.package_no }}</a></div>
                                                <p>Location: <strong>{{ pkg.warehouse.name }}</strong></p>
                                                <p>Status: <strong>{{ pkg.status }}</strong></p>
                                                <p>Tracking IN: <strong>N/A</strong></p>
                                                <p>Tracking Out: <strong>{{ pkg.tracking_number_out }}</strong></p>
                                                <p v-if="pkg.package_length > 0">Dimentions : {{ pkg.package_length }} {{ pkg.dim_unit }} x {{ pkg.package_width }} {{ pkg.dim_unit }} x {{ pkg.package_height }} {{ pkg.dim_unit }} </p>
                                                <p>Entered :  {{ pkg.created_at }}</p>
                                                <p>Shipped :  N/A</p>      
                                                <p>Cosolidated From:  N/A</p>                   
                                            </td>
                                            <td>
                                                <template v-if="pkg.status == !'open'">
                                                    <a class="link-primary" :href="route('packages.custom', pkg.id)">Customs Declaration Form</a>
                                                </template>
                                                <a class="link-primary" target="_blank" :href="route('packages.pdf', pkg.id)">Print Commercial Invoice</a>
                                                &nbsp; | &nbsp;
                                                <inertia-link class="link-primary" :href="route('packages.show', pkg.id)">                                                    
                                                    <span>Details</span>
                                                </inertia-link>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div :class="getTabPaneClass('tab3')" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" style="background-color:white;">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                            <th scope="col">#</th>
                                              <!-- <th scope="col">Image</th> -->
                                            <th scope="col">Package</th>
                                            <th scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="pkg in packages_sent.data" :key="pkg.id">
                                            <td>{{ pkg.id }}</td>
                                          <!-- <td>
                                                <div class="text-center">
                                                    <img style="width:100px;height:auto;" class="img-thumbnail" :src="pkgImgURL(pkg)" />
                                                </div>
                                            </td> -->
                                            <td>
                                                <div class="package-no"><a :href="route('packages.show', pkg.id)">{{ pkg.package_no }}</a></div>
                                                <p>Location: <strong>{{ pkg.warehouse.name }}</strong></p>
                                                <p>Status: <strong>{{ pkg.status }}</strong></p>
                                                <p>Tracking IN: <strong>N/A</strong></p>
                                                <p>Tracking Out: <strong>{{ pkg.tracking_number_out }}</strong></p>
                                                <p v-if="pkg.package_length > 0">Dimentions : {{ pkg.package_length }} {{ pkg.dim_unit }} x {{ pkg.package_width }} {{ pkg.dim_unit }} x {{ pkg.package_height }} {{ pkg.dim_unit }} </p>
                                                <p>Entered :  {{ pkg.created_at }}</p>
                                                <p>Shipped :  N/A</p>      
                                                <p>Cosolidated From:  N/A</p>                   
                                            </td>
                                            <td>
                                                <template v-if="pkg.status == 'open'">
                                                    <a class="link-primary" :href="route('packages.custom', pkg.id)">Customs Declaration Form</a>
                                                </template>
                                                <template v-else>
                                                    <a class="link-primary" target="_blank" :href="route('packages.pdf', pkg.id)">Print Commercial Invoice</a>
                                                </template>
                                                &nbsp; | &nbsp;
                                                <inertia-link class="link-primary" :href="route('packages.show', pkg.id)">                                                    
                                                    <span>Details</span>
                                                </inertia-link>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div :class="getTabPaneClass('tab4')" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" style="background-color:white;">                                    
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </MainLayout>
</template>
<style scoped>
.label{
    padding:5px;
}
.package-no{
    font-weight: bold;
    border-color: #f04c23 !important;
    display: block;
    background-color:#c1c1c1;
    text-align: center;
    padding: 5px;
    height: 33px;
    width:150px;
}
</style>
<script>
    import MainLayout from '@/Layouts/Main'
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
    import FlashMessages from '@/Components/FlashMessages'
    export default {
        components: {
            BreezeAuthenticatedLayout,
            MainLayout,
            FlashMessages
        },
        props: {
            auth: Object,
            packages_account:Object,
            packages_ready:Object,
            packages_sent:Object,
        },
        data() {
            return {
                form: this.$inertia.form({
                        search: ''
                }),
                params:{
                    search:null,
                },
                tabs : {
                    tab1:true,
                    tab2:false,
                    tab3:false,
                    tab4:false,
                },
                printable:['filled','labeled','shipped','delivered']
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

            setActiveTabAB(tab){
                
                for (var key in this.tabs) {
                    if(key === tab){
                        this.tabs[key] = true;
                    }else{
                        this.tabs[key] = false;
                    }
                }
            },
            getTabClass(tab){

                if(this.tabs[tab] === true){
                      return 'nav-link active';
                }else{
                    return 'nav-link';
                }
                
            },
            getTabPaneClass(tab){

                if(this.tabs[tab] === true){
                     return 'tab-pane show active';
                }else{
                    return 'tab-pane fade';
                }
            },

            pkgImgURL(pkg) {
                if(pkg.hasOwnProperty("image")){
                    return "/uploads/"+pkg.image;
                }
                return "/uploads/no-image.jpg";
            },
        }
    }
</script>
