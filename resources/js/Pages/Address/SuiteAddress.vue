<template>
    <MainLayout>
        <div class="row mt-4">
            <div class="col-md-7 offset-md-1">
                    <div class="card mt-4">
                        <div class="card-header">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
                            Using your Addresses
                        </h2>
                    </div>
                    <div class="card-body">                        
                        <p>
                            How to use your addresses when purchasing from online merchants.
                            <ul>
                                <li>1: Purchase form merchants</li>
                                <li>2: Use one of these addresses</li>
                                <li>3: We will deliver items to your address outside US. (Listed in address book) </li>
                            </ul>
                        </p>
                    </div>
                </div>          
            </div>
            <div class="col-md-4 offset-md-1">
                <div class="card">
                    <div class="card-header">
                        California Mailing Address
                    </div>
                    <div class="card-body">
                        <p><strong>Full Name:</strong>{{ siuteNum }} – {{ $page.props.auth.user.name }} </p>
                        <p><strong>Street Address:</strong>3578 W SAVANNA ST </p>
                        <p><strong>City:</strong>ANAHEIM </p>
                        <p><strong>Suite #:</strong> {{ siuteNum }} </p>
                        <p><strong>State:</strong>CA </p>
                        <p><strong>Zip code:</strong>92804 </p>        
                        <p><strong>Phone Number:</strong>657-201-7881 </p>        
                        <br /><br />
                        <a href="javascript:void(0)" class="btn btn-primary" @click="copyToClipBoard(address1)">
                            <span v-if="!copied1">Click Here To Copy</span>
                            <span v-if="copied1">Copied</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 offset-md-1">
                <div class="card">
                    <div class="card-header">
                        Delaware Mailing Address
                    </div>
                    <div class="card-body">
                        <p><strong>Full Name:</strong>{{ siuteNum }} – {{ $page.props.auth.user.name }} </p>
                        <p><strong>Street Address:</strong>1217 OLD COOCH BRIDGE RD </p>
                        <p><strong>City:</strong>Newark </p>
                        <p><strong>Suite #:</strong>{{ siuteNum }} </p>
                        <p><strong>State:</strong>DE </p>
                        <p><strong>Phone Number:</strong>657-201-7881 </p>        
                        <br /><br /><br />
                        <a href="javascript:void(0)" class="btn btn-primary" @click="copyToClipBoard(address2)">
                            <span v-if="!copied2">Click Here To Copy</span>
                            <span v-if="copied2">Copied</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>


    </MainLayout>
</template>
<style scoped>
.card{
    margin-top: 25px;
}
.card-body p strong{
    color: #212529; margin-right: 12px;
    }
</style>
<script>
    import MainLayout from '@/Layouts/Main'
    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'

    export default {
        components: {
            BreezeAuthenticatedLayout,
            MainLayout
        },
        data(){
            return{
                address1 : '3578 w savanna st Suite #:AD - 400'+ this.$page.props.auth.user.id + ',Anaheim CA ,92804',
                address2 : '1217 old cooch bridge rd Suite #:AD - 400'+ this.$page.props.auth.user.id +' ,Newark, Delaware ',
                copied1:false,
                copied2:false,
            }
        },
        props: {
            auth: Object,            
            addresses:Object
        },
        computed:{
            siuteNum(){
                return 4000 + this.$page.props.auth.user.id;
            },
        },
        methods:{
            copyToClipBoard(address){
                if(address.includes("92804")){
                    this.copied1 = true;
                }else{
                    this.copied2 = true;
                }
                var text = address;
                navigator.clipboard.writeText(text).then(function() {
                }, function(err) {
                console.error('Async: Could not copy text: ', err);
                });
            }
        }
    }
</script>
