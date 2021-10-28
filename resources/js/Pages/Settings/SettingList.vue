<template>
    <MainLayout>
		<div class="card mt-4">
				<div class="card-body">                    
                    <FlashMessages />
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight form-title">
                        Settings
                    </h2>
                    <form @submit.prevent="submit">     
                    <table class="table table-striped">
                        <thead>
                            <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Description</th>
                            <th scope="col">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr v-for="setting in form.settings" :key="setting.id">
                            <td>{{ setting.name }}</td>
                            <td>{{ setting.description }}</td>
                            <td>                                
                                <input name="title" id="title" type="text" class="form-control" placeholder="Title" v-model="setting.value" required />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <input type="submit" value="Update Settings" class="btn btn-danger" />
                            </td>
                        </tr>
                        </tbody>
					</table>
                    </form>          
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
    import FlashMessages from '@/Components/FlashMessages'

    export default {
        components: {
            BreezeAuthenticatedLayout,
            MainLayout,
            FlashMessages
        },
        data() {
            return {
                form: this.$inertia.form({
                    settings: this.settings
                })
            };
        },
        props: {
            auth: Object,
            settings:Object,
        },
        methods : {
          	submit() {
              this.form.post(this.route('settings.update'))
            }
        }
    }
</script>
