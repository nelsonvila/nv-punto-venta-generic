<template>
    <main class="main">
        <!-- Breadcrumb -->
        <template v-if="ingreso==1">

            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><a href="/">Inicio</a></li>
            </ol>
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card">

                    <template v-if="listado==1">
                        <div class="card-header">
                            <div class="d-flex justify-content-between mt-1">
                                <div>
                                    <h2 class="ml-1">Listado de Cierres de Cajas</h2><br/>
                                </div>
                                <div>
                                    <h4>Fecha: {{ today }}</h4>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <div>
                                    <button class="btn btn-primary btn-lg" type="button" @click="abrirModal1()">
                                        <i class="fa fa-plus fa-2x"></i>&nbsp;&nbsp;Nueva Caja
                                    </button>
                                </div>
                                <VentasDia :today="{ today }"></VentasDia>
                                <!--                                <div>-->
                                <!--                                    <h4>Cant Ventas del dia: {{cantidadVentas}}</h4>-->
                                <!--                                </div>-->
                                <!--                                <div>-->
                                <!--                                    <h4>Monto del dia: $ {{montoVentas}}</h4>-->
                                <!--                                </div>-->
                            </div>

                        </div>
                        <!--listado-->

                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <select class="form-control col-md-3" v-model="criterioS">
                                            <option value="fecha" selected>Fecha</option>
                                        </select>
                                        <!--                                            <input type="text"  @keyup.enter="listarCaja(1,buscar,criterio);" v-model="buscar" class="form-control" placeholder="Buscar texto"><input type="text"  @keyup.enter="listarCaja(1,buscar,criterio);" v-model="buscar" class="form-control" placeholder="Buscar texto">-->
                                        <datepicker v-model="buscarS" format="yyyy-MM-dd"></datepicker>
                                        <button type="submit" @click="listarCaja(1,buscarS,criterioS);"
                                                class="btn btn-primary"><i class="fa fa-search"></i> Buscar
                                        </button>
                                    </div>
                                </div>
                                <button type="submit" @click="limpiar();" class="btn btn-warning"><i
                                    class="fa fa-remove"></i> limpiar
                                </button>
                            </div>
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                <tr class="bg-primary">


                                    <th>Fecha</th>
                                    <th>Monto Inicial</th>
                                    <th>Monto Final</th>
                                    <th>Ganancia</th>
                                    <th>Usuario</th>
                                    <th>Estado</th>
                                    <th>Cambiar estado</th>
                                </tr>
                                </thead>
                                <tbody v-if="arrayCaja.length">

                                <tr v-for="caja in arrayCaja" :key="caja.id">

                                    <td v-text="caja.fecha"></td>
                                    <td v-text="'$ '+ caja.monto_inicio"></td>
                                    <td>{{ caja.monto_final != null ? '$' + (caja.monto_final) : '$ 0.00' }}</td>
                                    <td>
                                        {{
                                            (caja.monto_final != null) ? '$' + (caja.monto_final - caja.monto_inicio) : '$' +
                                                '0.00'
                                        }}
                                    </td>
                                    <td v-text="caja.usuario"></td>
                                    <td>

                                        <button type="button" v-if="caja.estado=='Activo'"
                                                class="btn btn-danger btn-sm ">
                                            <i class="fa fa-times fa-2x"></i> Abierta
                                        </button>

                                        <button type="button" v-else class="btn btn-success btn-sm">
                                            <i class="fa fa-check fa-2x"></i> Cerrada
                                        </button>

                                    </td>

                                    <td>
                                        <template v-if="caja.estado=='Activo'">
                                            <button type="button" class="btn btn-danger btn-sm"
                                                    @click="cerrarCaja(caja.id)">
                                                <i class="fa fa-times fa-2x"></i> Cerrar Caja
                                            </button>
                                        </template>

                                        <template v-else>
                                            <button type="button" class="btn btn-success btn-sm">
                                                <i class="fa fa-check fa-2x"></i> Caja Cerrada
                                            </button>
                                        </template>
                                    </td>
                                </tr>

                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <td colspan="6">
                                        No se han agregado cierres de caja
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <nav>
                                <ul class="pagination">
                                    <li class="page-item" v-f="pagination.current_page > 1">
                                        <a class="page-link" href="#"
                                           @click.prevent="cambiarPagina(pagination.current_page - 1,buscar,criterio)">Anterior</a>
                                    </li>

                                    <li class="page-item" v-for="page in pagesNumber" :key="page"
                                        :class="[page == isActived ? 'active' : '']">
                                        <a class="page-link" href="#"
                                           @click.prevent="cambiarPagina(page,buscar,criterio)" v-text="page"></a>
                                    </li>


                                    <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                        <a class="page-link" href="#"
                                           @click.prevent="cambiarPagina(pagination.current_page + 1,buscar,criterio)">Siguiente</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <!--Inicio del modal agregar/actualizar-->
        <div class="modal fade" :class="{'mostrar':modal}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal"></h4>
                        <button type="button" @click="cerrarModal()" class="close" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div v-show="errorCaja" class="form-group row div-error">

                            <div class="text-center text-error">

                                <div v-for="error in errorMostrarMsjCaja" :key="error" v-text="error"></div>

                            </div>

                        </div>


                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Fecha</label>
                                <div class="col-md-9">
                                    <input type="text" readonly="readonly" v-model="fecha" class="form-control"
                                           placeholder="Nombre de categoría">

                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Monto Inicial ($)</label>
                                <div class="col-md-9">
                                    <input type="number" min="1" v-model="montoInicial" class="form-control"
                                           placeholder="Ingrese monto del inicio de caja">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="cerrarModal()" class="btn btn-danger"><i
                            class="fa fa-times fa-2x"></i> Cerrar
                        </button>
                        <button type="button" @click="registrarCaja()" v-if="tipoAccion==1" class="btn btn-success"><i
                            class="fa fa-save fa-2x"></i> Guardar
                        </button>
                        <button type="button" @click="actualizarCaja()" v-if="tipoAccion==2" class="btn btn-success"><i
                            class="fa fa-save fa-2x"></i> Actualizar
                        </button>

                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--Fin del modal-->
    </main>
</template>

<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script>
import Datepicker from 'vuejs-datepicker';
import VentasDia from "./VentasDia";

export default {
    components: {
        Datepicker,
        VentasDia

    },
    data() {

        return {
            errorCaja: '',
            validator: '',
            montoInicial: '',
            errorMostrarMsjCaja: '',
            buscarS: '',
            criterioS: 'fecha',
            fecha: '',
            nombre: '',
            monto_inicio: '',
            monto_final: '',
            usuario: '',
            estado: '',
            ingreso: 1,
            listado: 1,
            descripcion: '',
            arrayCaja: [],
            arrayCaja1: [],
            arrayCliente: [],
            arrayCliBanco: [],
            modal: 0,
            modal1: 0,
            modal2: 0,
            tituloModal: '',
            imagen: '',
            tipoAccion: 0,
            tipoAsocia: 0,
            tipo_cta: '',
            cuenta: '',
            errorBanco: 0,
            errorClienteBanco: 0,
            errorMostrarMsjBanco: [],
            errorMostrarMsjCtleBanco: [],
            pagination: {

                'total': 0,
                'current_page': 0,
                'per_page': 0,
                'last_page': 0,
                'from': 0,
                'to': 0,

            },
            offset: 3,
            criterio: 'nombre',
            buscar: '',
            criterioP: 'nombre',
            buscarP: '',
            criterioQ: 'nombre',
            buscarQ: '',
            valor: '',
            cierreCaja: '',
            today: '',
            // cantidadVentas: 0,
            // montoVentas: 0
        }

    },

    computed: {

        isActived: function () {

            return this.pagination.current_page;

        },

        //calcula los elementos de la paginacion
        pagesNumber: function () {

            if (!this.pagination.to) {

                return [];
            }

            var from = this.pagination.current_page - this.offset;
            if (from < 1) {

                from = 1;
            }

            var to = from + (this.offset * 2);
            if (to >= this.pagination.last_page) {

                to = this.pagination.last_page;
            }

            var pagesArray = [];
            while (from <= to) {

                pagesArray.push(from);
                from++;
            }
            return pagesArray;


        }

    },

    methods: {

        listarCaja(page, buscar, criterio) {

            let me = this;
            if (buscar) {
                let options = {
                    year: "numeric",
                    month: "2-digit",
                    day: "2-digit"
                };
                buscar = buscar.toLocaleDateString("fr-CA", options).replace(/\//g, "-");
            }
            const axios = require('axios');

            var url = '/caja?page=' + page + '&buscar=' + buscar + '&criterio=' + criterio;

            axios.get(url).then(function (response) {
                // haoyundle success
                //console.log(response);
                var respuesta = response.data;
                me.arrayCaja = respuesta.cajas.data;
                me.pagination = respuesta.pagination;
            })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                });
        },
        limpiar() {
            this.arrayCaja = [];
            this.buscarS = '';
            this.pagination = 0;
            this.listarCaja(1, '', '');
        },
        listarCliente(buscarP, criterioP) {
            let me = this;

            const axios = require('axios');
            var url = '/cliente/listarCliente?buscar=' + buscarP + '&criterio=' + criterioP;
            axios.get(url).then(function (response) {
                var respuesta = response.data;
                me.arrayCliente = respuesta.clientes.data;
            })
                .catch(function (error) {
                    console.log(error);
                });
        },

        listarBanco2(buscarQ, criterioQ) {

            let me = this;

            const axios = require('axios');
            var url = '/bancos/listarBancos?buscar=' + buscarQ + '&criterio=' + criterioQ;
            axios.get(url).then(function (response) {
                var respuesta = response.data;
                me.arrayCaja1 = respuesta.cajas.data;
            })
                .catch(function (error) {
                    console.log(error);
                });

        },


        asignardatoscliente(data = []) {

            let me = this;

            if (data['id'] == 0) {
                swal({
                    type: 'error',
                    title: 'Error...',
                    text: 'El Cliente no ha sido seleccionado',
                })

            } else {
                /*selecciona datos del cliente*/
                me.cliente_id = data["id"];
                me.nombre_cliente = data["nombre"];
                /*cuadro de dialogo en el momento que asigna datos*/
                swal({
                    type: 'info',
                    title: 'Informacion',
                    text: 'Se ha asignado datos de clientes'
                })
            }

            me.modal1 = 0;


        },

        asignardatosbanco(data = []) {

            let me = this;

            if (data['id'] == 0) {
                swal({
                    type: 'error',
                    title: 'Error...',
                    text: 'El Banco no ha sido seleccionado',
                })

            } else {
                /*selecciona datos del cliente*/
                me.banco_id = data["id"];
                me.nombre_banco = data["nombre"];
                /*cuadro de dialogo en el momento que asigna datos*/
                swal({
                    type: 'info',
                    title: 'Informacion',
                    text: 'Se ha asignado datos de Banco'
                })
            }

            me.modal2 = 0;
            me.tituloModal = '';


        },


        cargarPDF() {


            window.open('http://127.0.0.1:8080/bancos/listarPDF', '_blank');

        },

        AsignaBanco() {

            this.ingreso = 2;

        },

        cancelaIng() {

            this.cliente_id = 0;
            this.nombre_cliente = '';
            this.banco_id = 0;
            this.nombre_banco = '';
            this.cuenta = '';
            this.modal = '';
            this.modal1 = '';
            this.modal2 = '';
            this.tituloModal = '';

            this.listarBanco(1, '', 'nombre');
            this.ingreso = 1;

        },


        cambiarPagina(page, buscar, criterio) {

            let me = this;

            //Actualiza  la pagina actual

            me.pagination.current_page = page;

            me.listarBanco(page, buscar, criterio);

        },

        registrarClienteBanco() {

            if (this.validarClienteBanco()) {

                return;
            }

            let me = this;

            const axios = require('axios');

            axios.post('/bancos/registrarclibanco', {

                'idcliente': this.cliente_id,
                'idbanco': this.banco_id,
                'banco': this.nombre_banco,
                'tipo_cta': this.tipo_cta,
                'cuenta': this.cuenta


            }).then(function (response) {
                // handle success
                //console.log(response);
                me.listarBanco(1, '', 'nombre');
                me.ingreso = 1;
            }).catch(function (error) {
                // handle error
                console.log(error);
            });

        },


        registrarBanco() {

            if (this.validarBanco()) {

                return;
            }

            let me = this;

            const axios = require('axios');

            axios.post('/bancos/registrar', {

                'nombre': this.nombre,
                'descripcion': this.descripcion


            }).then(function (response) {
                // handle success
                //console.log(response);
                me.cerrarModal();
                me.listarBanco(1, '', 'nombre');

            }).catch(function (error) {
                // handle error
                console.log(error);
            });

        },

        actualizarBancos() {

            if (this.validarBanco()) {

                return;
            }

            let me = this;

            const axios = require('axios');

            axios.put('/bancos/actualizar', {

                'nombre': this.nombre,
                'descripcion': this.descripcion,
                'id': this.banco_id


            }).then(function (response) {
                // handle success
                //console.log(response);
                me.cerrarModal();
                me.listarBanco(1, '', 'nombre');

            }).catch(function (error) {
                // handle error
                console.log(error);
            });

        },

        buscarCliente() {
            let me = this;

            const axios = require('axios');

            var url = '/cliente/buscaCliente?filtro=' + me.cliente_id;

            axios.get(url).then(function (response) {
                let respuesta = response.data;
                me.arrayCliente = respuesta.clientes;

                if (me.arrayCliente.length > 0) {
                    me.nombre_cliente = me.arrayCliente[0]['nombre'];

                } else {
                    me.nombre_cliente = 'No existe Cliente';
                    me.cliente_id = 0;

                }

            })
                .catch(function (error) {
                    console.log(error);
                });


        },


        buscarBanco() {
            let me = this;

            const axios = require('axios');

            var url = '/bancos/buscaBanco?filtro=' + me.banco_id;

            axios.get(url).then(function (response) {
                let respuesta = response.data;
                me.arrayCaja = respuesta.bancos;

                if (me.arrayCaja.length > 0) {
                    me.nombre_banco = me.arrayCaja[0]['nombre'];

                } else {
                    me.nombre_banco = 'No existe el banco';
                    me.banco_id = 0;

                }

            })
                .catch(function (error) {
                    console.log(error);
                });


        },


        abrirModal1() {

            this.dateCurrent();

            this.modal = 1;
            this.tituloModal = "Nueva Caja";
            this.fecha = this.today;
            this.montoInicial = "";
            this.tipoAccion = 1;

        },
        dateCurrent() {
            let today = new Date();
            let options = {
                year: "numeric",
                month: "2-digit",
                day: "2-digit"
            };
            this.today = today.toLocaleDateString("fr-CA", options).replace(/\//g, "-");
        },
        abrirModal2() {

            this.arrayCaja = [];
            this.modal2 = 1;
            this.tituloModal = 'Seleccione uno o varios bancos';
        },

        desactivarBancos(id) {

            const swalWithBootstrapButtons = Swal.mixin({
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false,
            })

            swalWithBootstrapButtons({
                title: 'Estas seguro de desactivar la Caja?',
                //type: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="fa fa-check fa-2x"></i> Aceptar',
                cancelButtonText: '<i class="fa fa-times fa-2x"></i> Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {

                    let me = this;

                    const axios = require('axios');

                    axios.put('/bancos/desactivar', {

                        'id': id


                    }).then(function (response) {
                        // handle success
                        //console.log(response);
                        me.listarBanco(1, '', 'nombre');

                        swalWithBootstrapButtons(
                            'Desactivado!',
                            'El registro ha sido desactivado con exito.',
                            'success'
                        )

                    }).catch(function (error) {
                        // handle error
                        console.log(error);
                    });


                } else if (
                    // Read more about handling dismissals
                    result.dismiss === Swal.DismissReason.cancel
                ) {

                }
            })

        },

        activarBanco(id) {

            const swalWithBootstrapButtons = Swal.mixin({
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false,
            })

            swalWithBootstrapButtons({
                title: 'Estas seguro de activar la Caja?',
                //type: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="fa fa-check fa-2x"></i> Aceptar',
                cancelButtonText: '<i class="fa fa-times fa-2x"></i> Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {

                    let me = this;

                    const axios = require('axios');

                    axios.put('/bancos/activar', {

                        'id': id


                    }).then(function (response) {
                        // handle success
                        //console.log(response);
                        me.listarCaja(1, '', 'nombre');

                        swalWithBootstrapButtons(
                            'Activado!',
                            'El registro ha sido activado con exito.',
                            'success'
                        )

                    }).catch(function (error) {
                        // handle error
                        console.log(error);
                    });


                } else if (
                    // Read more about handling dismissals
                    result.dismiss === Swal.DismissReason.cancel
                ) {

                }
            })

        },

        validarBanco() {

            this.errorBanco = 0;
            this.errorMostrarMsjBanco = [];

            if (!this.nombre) this.errorMostrarMsjBanco.push("(*)El nombre del banco no puede estar vacio");

            if (!this.descripcion) this.errorMostrarMsjBanco.push("(*)La descripción del banco no puede estar vacia");

            if (this.errorMostrarMsjBanco.length) this.errorBanco = 1;

            return this.errorBanco;
        },

        validarClienteBanco() {

            this.errorClienteBanco = 0;
            this.errorMostrarMsjCtleBanco = [];

            if (this.cliente_id == 0) this.errorMostrarMsjCtleBanco.push("(*)El cliente no debe de estar vacio");

            if (this.banco_id == 0) this.errorMostrarMsjCtleBanco.push("(*)El Banco no debe de estar vacio");

            if (!this.tipo_cta) this.errorMostrarMsjCtleBanco.push("(*)El tipo de cuenta no debe de estar vacio");

            if (!this.cuenta) this.errorMostrarMsjCtleBanco.push("(*) El número de cuenta no debe de estar vacio");

            if (this.errorMostrarMsjCtleBanco.length) this.errorClienteBanco = 1;

            return this.errorClienteBanco;


        },


        cerrarModal() {
            this.modal = 0;
            this.nombre = "";
            this.descripcion = "";
            this.tituloModal = "";

        },


        cerrarModal1() {

            this.modal1 = 0;
            this.tituloModal = "";

        },

        cerrarModal2() {

            this.modal2 = 0;
            this.tituloModal = "";


        },


        abrirModal(modelo, accion, data = []) {
            switch (modelo) {

                case "banco": {

                    switch (accion) {

                        case "registrar": {

                            this.modal = 1;
                            this.tituloModal = "Registrar Banco";
                            this.nombre = "";
                            this.descripcion = "";
                            this.tipoAccion = 1;
                            break;

                        }

                        case "actualizar": {
                            //console.log(data);
                            this.modal = 1;
                            this.tituloModal = "Editar Banco";
                            this.tipoAccion = 2;
                            this.Caja_id = data["id"];
                            this.nombre = data["nombre"];
                            this.descripcion = data["descripcion"];
                            break;
                        }

                    }


                }

            }


        },
        registrarCaja() {

            if (this.validarCaja()) {

                return;
            }

            let me = this;

            const axios = require('axios');

            axios.post('/caja/registrar', {

                'monto': me.montoInicial,
                'fecha': me.fecha,


            }).then(function (response) {
                // handle success
                //console.log(response);
                me.cerrarModal();
                me.listarCaja(1, '', 'nombre');

            }).catch(function (error) {
                // handle error
                console.log(error);
            });

        },
        validarCaja() {
            this.errorCaja = 0;
            this.errorMostrarMsjCaja = [];
            if (!this.montoInicial) this.errorMostrarMsjCaja.push("(*)El monton de la Caja no puede estar vacio");
            if (this.errorMostrarMsjCaja.length) this.errorCaja = 1;

            return this.errorCaja;
        },

        verficarCajaCreadaToday(fecha) {
            const axios = require('axios');
            let url = '/caja/verificarCaja?filtro=' + fecha;
            var valor;
            const val = axios.get(url).then(function (response) {
                var respuesta = response.data;
                if (response.data.caja != null) {
                    return valor = 1;

                } else {
                    return valor = 2;

                }
            })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                });

            return val;


        },
        cerrarCaja(id) {
            swal({
                title: 'Cierre de Caja',
                //type: 'warning',
                showCancelButton: true,
                input: 'text',
                inputValue: this.cierreCaja,
                inputPlaceholder: 'Ingrese el monto de cierre del dia',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '<i class="fa fa-check fa-2x"></i> Aceptar!',
                cancelButtonText: '<i class="fa fa-times fa-2x"></i> Cancelar',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false,
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    let me = this;
                    const axios = require('axios');

                    axios.post('/caja/cerrar', {
                        'id': id,
                        'montoCierre': result.value
                    }).then(function (response) {
                        swal(
                            'Caja Cerrada!',
                            'La caja ha sido cerrada con éxito.',
                            'success'
                        );
                        me.listarCaja(1, '', 'nombre');
                    }).catch(function (error) {
                        console.log(error);
                    });


                } else if (
                    // Read more about handling dismissals
                    result.dismiss === swal.DismissReason.cancel
                ) {

                }
            })
        },
        // obtenerDetallesVentas() {
        //     let me = this;
        //
        //     const axios = require('axios');
        //     let url = '/venta/montoTotalVenta/' + '?fecha=' + this.today
        //     axios.get(url).then(function (response) {
        //         me.cantidadVentas = response.data.ventas.cantidad;
        //         me.montoVentas = response.data.ventas.total;
        //         console.log(response.data.ventas);
        //
        //     }).catch(function (error) {
        //         // handle error
        //         console.log(error);
        //     });
        // }
    },

    mounted() {
        //console.log('Component mounted.')
        this.dateCurrent();
        this.obtenerDetallesVentas();
        let resultado = this.verficarCajaCreadaToday(this.today);
        console.log(resultado);
        this.listarCaja(1, this.buscar, this.criterio);
    }
}
</script>

<style>

.modal-content {

    width: 100% !important;
    position: absolute !important;
}

.mostrar {

    height: 1000px;
    display: list-item !important;
    opacity: 1 !important;
    position: absolute !important;
    background-color: #3c29297a !important;
}

.div-error {

    display: flex;
    justify-content: center;
}

.text-error {

    color: red !important;
    font-weight: bold;
    font-size: 20px;
}

</style>
