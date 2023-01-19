<template>
        <div class="row">
          <div class="col-md-10">
            <h1 class="font-semibold text-xl text-gray-800 leading-tight form-title">
              Package #{{ packag.id }} <a v-if="$page.props.auth.user.type == 'admin' || $page.props.auth.user.type == 'manager'" 
              class="btn btn-outline-danger ml-2" v-show="packag.status === 'open'|| packag.status === 'filled' || packag.status === 'labeled' " 
              v-on:click="confirmDeletion()">Delete Package</a>
            </h1>
          </div>
          <div class="col-md-2">
            <span v-bind:class="getLabelClass(packag.status)" class="text-sm">{{ packag.status }}</span>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="card mt-2">
              <div class="card-header">
                <h3>Packages/Orders Included</h3>
              </div>
              <div class="card-body">
                <table class="table table-sm table-striped">
                  <thead>
                  <tr>
                    <th>Package #</th>
                    <th>Dimension</th>
                    <th>Weight</th>
                    <th>Tracking In</th>
                    <th></th>
                  </tr>
                  </thead>
                  <tbody>
                  <template v-for="child_pkg in packag.child_packages" :key="child_pkg.id">
                    <tr>
                      <td>
                        <span class="badge badge-primary text-sm">PKG #{{ child_pkg.id }}</span>
                      </td>
                      <td>
                         {{ child_pkg.order.package_length }} {{ child_pkg.order.dim_unit }} x {{ child_pkg.order.package_width }} {{ child_pkg.order.dim_unit }} x {{ child_pkg.order.package_height }} {{ child_pkg.order.dim_unit }} 
                      </td>
                      <td>{{ child_pkg.order.package_weight }} {{ child_pkg.order.weight_unit }}</td>
                      <td>{{child_pkg.order.tracking_number_in }}</td>
                      <td>
                        <inertia-link class="btn btn-info btn-sm m-1" :href="route('orders.show', child_pkg.order.id)">
                          <i class="fa fa-list mr-1"></i>Detail</inertia-link>
                      </td>
                    </tr>
                  </template>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
</template>
<script>
    export default {
        name: "Child Package Component",
        props: {
            packag:Object,
        },
        data() {
            return {
                // 
            }
        },
        methods:{
            getLabelClass(status) {
                switch (status) {
                    case 'pending':
                    return 'text-uppercase badge badge-warning p-2 text-white';
                    break;
                    case 'open':
                    return 'text-uppercase badge badge-info p-2 text-white';
                    break;
                    case 'filled':
                    return 'text-uppercase badge badge-info p-2 text-white';
                    break;
                    case 'open':
                    return 'text-uppercase badge badge-success p-2 text-white';
                    break;
                    case 'labeled':
                    return 'text-uppercase badge badge-success p-2 text-white';
                    break;
                    case 'shipped':
                    return 'text-uppercase badge badge-primary p-2';
                    break;
                    case 'delivered':
                    return 'text-uppercase badge badge-success p-2 text-white';
                    break
                    case 'consolidation':
                    return 'text-uppercase badge badge-danger p-2 text-white';
                    break;
                    case 'served':
                    return 'label bg-success';
                    break;
                    case 'rejected':
                    return 'label bg-danger';
                    break;
                    default:
                    return 'text-uppercase badge badge-primary p-2 text-white';
                }
            },
        }
    }
</script>