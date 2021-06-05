<template>
    <div>
        <h4>Cant Ventas del dia: {{cantidadVentas}}</h4>
    </div>
    <div>
        <h4>Monto del dia: $ {{montoVentas}}</h4>
    </div>
</template>

<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script>
export default {
    props: ['today'],
    data(){
        return {
            cantidadVentas:0,
            montoVentas:0,
        }

    },
    methods:{
        obtenerDetallesVentas(){
            let me = this;

            const axios = require('axios');
            let url = '/venta/montoTotalVenta/' + '?fecha=' + this.today
            axios.get(url).then(function (response) {
                me.cantidadVentas = response.data.ventas.cantidad;
                me.montoVentas = response.data.ventas.total;
                console.log(response.data.ventas);

            }).catch(function (error) {
                // handle error
                console.log(error);
            });
        }
    },

    mounted() {
        //console.log('Component mounted.')
        this.obtenerDetallesVentas();
    }
}
</script>

<style scoped>

</style>
