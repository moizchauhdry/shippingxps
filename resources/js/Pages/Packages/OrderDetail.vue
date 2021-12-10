<template>
    <MainLayout>

		<div class="card mt-4">
				<div class="card-body">

                        <div class="row">
                            <div class="col-md-4">
                                <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
                                    Order Details
                                </h2>                            
                                <table class="table table-striped">
                                    <tbody>
                                    <tr>
                                        <td>Order Id</td>
                                        <td>{{ order.order_id }}</td>
                                    </tr>
                                    <tr>
                                        <td>Traking #</td>
                                        <td>{{ order.tracking_number }}</td>
                                    </tr>
                                    <tr>
                                        <td>Shipping Total </td>
                                        <td>{{ order.shipping_total }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-md-4">
                                <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
                                    Package 
                                </h2>                            
                                <table class="table table-striped">
                                    <tbody>
                                    <tr>
                                        <td>Weight</td>
                                        <td>{{ order.package_weight }}</td>
                                    </tr>
                                    <tr>
                                        <td>Length</td>
                                        <td>{{ order.package_length }}</td>
                                    </tr>
                                    <tr>
                                        <td>Width</td>
                                        <td>{{ order.package_width }}</td>
                                    </tr>
                                    <tr>
                                        <td>Height</td>
                                        <td>{{ order.package_height }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>


                            <div class="col-md-4">
                                <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
                                    Notes
                                </h2>                            
                                <table class="table table-striped">
                                    <tbody>
                                    <tr>
                                        <td>Notes</td>
                                        <td>{{ order.notes }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">                                
                            <div class="col-md-6">
                                <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
                                    Sender Details
                                </h2>                            
                                <table class="table table-striped">
                                    <tbody>
                                    <tr>
                                        <td>Name</td>
                                        <td>{{ order.sender_fullname}}</td>
                                    </tr>
                                    <tr>
                                        <td>Address</td>
                                        <td>{{ order.sender_address }}</td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td>{{ order.sender_email }}</td>
                                    </tr>
                                    <tr>
                                        <td>Phone</td>
                                        <td>{{ order.sender_phone }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>


                            <div class="col-md-6">
                                <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
                                    Receiver Details
                                </h2>                            
                                <table class="table table-striped">
                                    <tbody>
                                    <tr>
                                        <td>Name</td>
                                        <td>{{ order.receiver_fullname}}</td>
                                    </tr>
                                    <tr>
                                        <td>Address</td>
                                        <td>{{ order.receiver_address }}</td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td>{{ order.receiver_email }}</td>
                                    </tr>
                                    <tr>
                                        <td>Phone</td>
                                        <td>{{ order.receiver_phone }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>      

                        <div class="row">                                

                            <div class="col-md-12">
                                <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
                                    Items
                                </h2>                            
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name </th>
                                        <th scope="col">Quantity</th>                            
                                        <th scope="col">Description</th>
                                        <th scope="col">Image</th>
                                        </tr>
                                    </thead>
                                    <tbody>                                        
                                     <tr v-for="item in order.items" :key="item.id">
                                        <td>{{ item.id}}</td> 
                                        <td>{{ item.name}}</td>
                                        <td>{{ item.quantity}}</td>
                                        <td>{{ item.description }}</td>
                                        <td>
                                            <div class="text-center">
                                                <img style="width:100px;height:auto;" class="img-thumbnail" :src="imgURL(item.image)" />
                                            </div>
                                        </td>
                                    </tr>                
                                    </tbody>
                                </table>
                            </div>
                        </div>  
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
            MainLayout
        },

        props: {
            auth: Object,
            order:Object,
            order_details:Object
        },
        methods : {
            imgURL(url) {
                return "/public/uploads/"+url;
            },
        }
    }
</script>
