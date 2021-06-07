<template>
    <div>
        <div>
            <h4>Cant Ventas del dia: {{ cantidadVentas }}</h4>
        </div>
        <div>
            <h4>Monto del dia: $ {{ montoVentas }}</h4>
        </div>
    </div>
</template>

<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script>
export default {
    props: ['today'],
    data() {
        return {
            cantidadVentas: 0,
            montoVentas: 0,
        }

    },
    methods: {
        obtenerDetallesVentas() {
            let me = this;
            let date = this.today.today;
            if(date === ''){
                date = this.dateCurrent();
            }
            const axios = require('axios');
            let url = '/venta/montoTotalVenta/' + '?fecha=' + date
            axios.get(url).then(function (response) {
                me.cantidadVentas = response.data.ventas.cantidad;
                me.montoVentas = response.data.ventas.total;
                console.log('Ventas:',response.data.ventas);

            }).catch(function (error) {
                // handle error
                console.log(error);
            });
        },
        dateCurrent() {
            let currentDay = new Date();
            let options = {
                year: "numeric",
                month: "2-digit",
                day: "2-digit"
            };
            return currentDay.toLocaleDateString("fr-CA", options).replace(/\//g, "-");
        },
    },

    mounted() {
        this.obtenerDetallesVentas();
    }
}
</script>

<style scoped>

</style>
