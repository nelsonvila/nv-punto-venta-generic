<template>
    <main class="main">
        <!-- Breadcrumb -->
        <ol class="breadcrumb mb-0">


                    <div class="d-flex justify-content-between">
                    <div><a href="/">Inicio</a></div>
                        <div><h3 v-if="listado==0">Nueva Venta</h3></div>
                        <div></div>
                    </div>


        </ol>
        <div class="container-xxl">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">

                <template v-if="listado==1">
                    <div class="card-header">

                        <h2>Listado de Ventas</h2><br/>

                        <button class="btn btn-primary btn-lg" type="button" @click="mostrarDetalle()">
                            <i class="fa fa-plus fa-2x"></i>&nbsp;&nbsp;Nueva Venta
                        </button>

<!--                        <button class="btn btn-primary btn-lg" type="button" @click="mostrarPagos()">-->
<!--                            <i class="fa fa-plus fa-2x"></i>&nbsp;&nbsp;Pagos-->
<!--                        </button>-->


                    </div>
                    <!--listado-->

                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select class="form-control col-md-3" v-model="criterio">
                                        <option value="num_venta">Número Venta</option>
                                        <option value="fecha_venta">Fecha Venta</option>
                                    </select>
                                    <input type="text" @keyup.enter="listarVenta(1,buscar,criterio);" v-model="buscar"
                                           class="form-control" placeholder="Buscar texto">
                                    <button type="submit" @click="listarVenta(1,buscar,criterio);"
                                            class="btn btn-primary"><i class="fa fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                <tr class="bg-primary">

                                    <th>Ver Detalle</th>
                                    <th>Fecha Venta</th>
                                    <th>Número Venta</th>
                                    <th>Vendedor</th>
                                    <th>Total ($)</th>
                                    <th>Estado</th>
                                    <th>Cambiar estado</th>
                                </tr>
                                </thead>
                                <tbody>

                                <tr v-for="venta in arrayVenta" :key="venta.id">

                                    <td>
                                        <!--compra.id-->
                                        <button type="button" @click="verVenta(venta.id)" class="btn btn-warning btn-sm">
                                            <i class="fa fa-eye fa-2x"></i> Ver detalle
                                        </button> &nbsp;

                                    </td>
                                    <td v-text="venta.fecha_venta"></td>
                                    <td v-text="venta.num_venta"></td>
                                    <td v-text="venta.usuario"></td>
                                    <td v-text="venta.total"></td>
                                    <td>

                                        <button type="button" v-if="venta.estado=='Registrado'"
                                                class="btn btn-success btn-sm">
                                            <i class="fa fa-check fa-2x"></i> Registrado
                                        </button>

                                        <button type="button" v-else class="btn btn-danger btn-sm">
                                            <i class="fa fa-times fa-2x"></i> Anulado
                                        </button>

                                    </td>

                                    <td>
                                        <template v-if="venta.estado=='Registrado'">
                                            <button type="button" class="btn btn-danger btn-sm"
                                                    @click="desactivarVenta(venta.id)">
                                                <i class="fa fa-times fa-2x"></i> Anular Venta
                                            </button>
                                        </template>

                                        <template v-else>
                                            <button type="button" class="btn btn-danger btn-sm">
                                                <i class="fa fa-check fa-2x"></i> Cambiado
                                            </button>
                                        </template>
                                    </td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" v-f="pagination.current_page > 1">
                                    <a class="page-link" href="#"
                                       @click.prevent="cambiarPagina(pagination.current_page - 1,buscar,criterio)">Anterior</a>
                                </li>

                                <li class="page-item" v-for="page in pagesNumber" :key="page"
                                    :class="[page == isActived ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar,criterio)"
                                       v-text="page"></a>
                                </li>


                                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                    <a class="page-link" href="#"
                                       @click.prevent="cambiarPagina(pagination.current_page + 1,buscar,criterio)">Siguiente</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </template>
                <!--fin listado-->

                <!-- Detalle-->

                <template v-else-if="listado==0">
                    <div class="card-body pt-0">

                        <div class="form-group row border" style="display: none">

                            <div class="col-md-8">
                                <div class="form-group">x
                                    <label class="text-uppercase"><strong>Número Venta(*)</strong></label>
                                    <input type="text" class="form-control" v-model="this.ultimoNumeroVenta"
                                           readonly="readonly">
                                </div>
                            </div>
                        </div>


                        <div class="form-group row">

                            <div class="col-md-12">
                                <div v-show="errorVenta" class="form-group row div-error">
                                    <div class="text-center text-error">
                                        <div v-for="error in errorMostrarMsjVenta" :key="error" v-text="error">

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>


                        <div class="form-group row border mb-0">
                            <div class="col-md-10 mt-1">
                                <div class="form-group">
                                    <div class="form-inline">
                                        <input type="text" ref="inputCodigo" class="form-control" v-model="codigo" @blur="buscarProducto(codigo)"
                                               @keyup.enter="buscarProducto(codigo)" placeholder="Ingrese código">
                                        <button @click="abrirModal" class="btn btn-primary">

                                            <i class="fa fa-search"></i>&nbsp;Buscar Productos

                                        </button>
                                    </div>
                                    <div>
                                        <input type="text" style="font-weight: bold;font-size: 16px" readonly :class="(stock !== 0) ? 'form-control' : 'form-control text-danger'" v-model="producto">
                                    </div>


                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <button @click="agregarDetalle()" class="btn btn-primary form-control btnagregar"><i
                                        class="fa fa-plus fa-2x"></i> Añadir [F9]
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row border">

                            <h5 class="ml-3">Productos agregados</h5>

                            <div class="table-responsive col-md-12">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                    <tr class="bg-success">
                                        <th>Eliminar</th>
                                        <th>Producto</th>
                                        <th>Precio ($)</th>
                                        <th>Cantidad</th>
                                        <th>Total ($)</th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="arrayDetalle.length">
                                    <tr v-for="(detalle,index) in arrayDetalle" :key="detalle.id">
                                        <td>
                                            <button @click="eliminarDetalle(index)" type="button"
                                                    class="btn btn-danger btn-sm">
                                                <i class="fa fa-times fa-xs"></i>
                                            </button>
                                        </td>
                                        <td v-text="detalle.producto">
                                        </td>
                                        <td v-text="detalle.precio" >
                                        </td>
                                        <td>
                                            <span style="color:red;"
                                                  v-show="detalle.cantidad>detalle.stock">Stock: {{ detalle.stock }}</span>
                                            <input v-model="detalle.cantidad" type="number" class="form-control">
                                        </td>
                                        <td>
                                            {{ (detalle.precio * detalle.cantidad).toFixed(2) }}
                                        </td>
                                    </tr>
                                    <tr style="background-color: grey;">
                                        <td colspan="4" align="right"><h3><strong class="text-light">Total:</strong></h3></td>
                                        <td><h3><strong class="text-light">$ {{ total = calcularTotal }}</strong></h3></td>
                                    </tr>
                                    </tbody>
                                    <tbody v-else>
                                    <tr>
                                        <td colspan="6">
                                            No se han agregado productos
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-danger" @click="ocultarDetalle()"><i
                                    class="fa fa-times fa-2x"></i> Cerrar
                                </button>
                                <button type="button" class="btn btn-success" @click="registrarVenta()"><i
                                    class="fa fa-save fa-2x"></i> Registrar Venta [F8]
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Fin Detalle-->

                <!-- Ver Compra -->

                <template v-else-if="listado==2">

                    <h2 class="text-center">Detalle de Ventas</h2><br/>

                    <div class="card-body">
                        <div class="form-group row border">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="text-uppercase"><strong>Número Venta</strong></label>
                                    <p v-text="num_venta"></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="text-uppercase"><strong>Fecha</strong></label>
                                    <p v-text="fecha_venta"></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="text-uppercase"><strong>Usuario</strong></label>
                                    <p v-text="usuario"></p>
                                </div>
                            </div>
                        </div>


                        <div class="form-group row border">
                            <div class="table-responsive col-md-12">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                    <tr class="bg-success">
                                        <th>Producto</th>
                                        <th>Precio ($)</th>
                                        <th>Cantidad</th>
                                        <th>Total ($)</th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="arrayDetalle.length">
                                    <tr v-for="detalle in arrayDetalle" :key="detalle.id">
                                        <td v-text="detalle.producto">
                                        </td>
                                        <td v-text="detalle.precio">
                                        </td>
                                        <td v-text="detalle.cantidad">
                                        </td>
                                        <td>
                                            {{ (detalle.precio * detalle.cantidad).toFixed(2) }}
                                        </td>
                                    </tr>
                                    <tr style="background-color: grey;">
                                        <td colspan="3" align="right"><strong>Total:</strong></td>
                                        <td><strong>$ {{ total = calcularTotal }}</strong></td>
                                    </tr>
                                    </tbody>
                                    <tbody v-else>
                                    <tr>
                                        <td colspan="4">
                                            No se han agregado productos
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" @click="ocultarDetalle()" class="btn btn-danger"><i
                                    class="fa fa-times fa-2x"></i> Cerrar
                                </button>

                            </div>
                        </div>
                    </div>


                </template>

                <!-- fin ver compra-->

                <!-- Ver pagos -->

                <template v-else-if="listado==3">
                    <div class="card-header">

                        <h2>Listado de Pagos</h2><br/>

                    </div>
                    <!--listado-->

                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select class="form-control col-md-3" v-model="criterioS">
                                        <option value="tipo_identificacion">Tipo identificación</option>
                                        <option value="factura">num_factura</option>
                                    </select>
                                    <input type="text" @keyup.enter="listarPagos(1,buscarS,criterioS);"
                                           v-model="buscarS" class="form-control" placeholder="Buscar texto">
                                    <button type="submit" @click="listarPagos(1,buscarS,criterioS);"
                                            class="btn btn-primary"><i class="fa fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                            <tr class="bg-primary">

                                <th>Factura</th>
                                <th>Tipo_Pago</th>
                                <th>cliente</th>
                                <th>idBanco</th>
                                <th>Banco</th>
                                <th>idTarjeta</th>
                                <th>Tarjeta</th>
                                <th>Valor</th>
                                <th>Imp Pago</th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr v-for="pago in arrayPago" :key="pago.id">

                                <td v-text="pago.factura"></td>
                                <td v-text="pago.tipo_pago"></td>
                                <td v-text="pago.nombre"></td>
                                <td v-text="pago.idbanco"></td>
                                <td v-text="pago.nombre_banco"></td>
                                <td v-text="pago.idtarjeta"></td>
                                <td v-text="pago.nombre_tarjeta"></td>
                                <td v-text="pago.valor"></td>

                                <td>

                                    <button type="button" @click="pdfPago(pago.id,pago.idcliente)"
                                            class="btn btn-info btn-sm">
                                        <i class="fa fa-file fa-2x"></i> Descargar PDF
                                    </button> &nbsp;
                                </td>


                            </tr>


                            </tbody>
                        </table>
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" v-if="pagination2.current_page > 1">
                                    <a class="page-link" href="#"
                                       @click.prevent="cambiarPagina2(pagination2.current_page - 1,buscarS,criterioS)">Anterior</a>
                                </li>

                                <li class="page-item" v-for="page in pagesNumber" :key="page"
                                    :class="[page == isActived ? 'active' : '']">
                                    <a class="page-link" href="#"
                                       @click.prevent="cambiarPagina2(page,buscarS,criterioS)" v-text="page"></a>
                                </li>


                                <li class="page-item" v-if="pagination2.current_page < pagination2.last_page">
                                    <a class="page-link" href="#"
                                       @click.prevent="cambiarPagina2(pagination2.current_page + 1,buscarS,criterioS)">Siguiente</a>
                                </li>
                            </ul>
                        </nav>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" @click="ocultarPagos()" class="btn btn-danger"><i
                                    class="fa fa-times fa-2x"></i> Cerrar
                                </button>

                            </div>
                        </div>
                    </div>
                </template>


                <!-- Fin Ver pagos-->


            </div>
            <!-- Fin ejemplo de tabla Listado -->
        </div>
        <!--Inicio del modal agregar/actualizar-->
        <div class="modal fade" :class="{'mostrar':modal}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal"></h4>
                        <button type="button" @click="cerrarModal()" class="close" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select class="form-control col-md-3" v-model="criterioP">
                                        <option value="nombre">Producto</option>
                                        <option value="codigo">Código</option>
                                    </select>
                                    <input type="text" ref="focusInputBusqueda" @keyup.enter="listarProducto(buscarP,criterioP);"
                                           v-model="buscarP" class="form-control" placeholder="Buscar texto">
                                    <button type="submit" @click="listarProducto(buscarP,criterioP);"
                                            class="btn btn-primary"><i class="fa fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">

                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                <tr class="bg-primary">

                                    <th>Categoria</th>
                                    <th>Producto</th>
                                    <th>Codigo</th>
                                    <th>Precio Venta ($)</th>
                                    <th>Stock</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                                </thead>
                                <tbody>

                                <tr v-for="producto in arrayProducto" :key="producto.id">

                                    <td v-text="producto.nombre_categoria"></td>
                                    <td v-text="producto.nombre"></td>
                                    <td v-text="producto.codigo"></td>
                                    <td v-text="producto.precio_venta"></td>
                                    <td v-text="producto.stock"></td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-sm" v-if="producto.condicion">
                                            <i class="fa fa-unlock"></i>&nbsp;Activo
                                        </button>

                                        <button type="button" class="btn btn-danger btn-sm" v-else>
                                            <i class="fa fa-lock"></i>&nbsp;Desactivado
                                        </button>

                                    </td>

                                    <td>
                                        <button type="button" @click="agregarDetalleModal(producto)"
                                                class="btn btn-primary btn-sm">
                                            <i class="fa fa-plus fa-2x"></i> Agregar
                                        </button>
                                    </td>
                                </tr>

                                </tbody>
                            </table>


                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="cerrarModal()" class="btn btn-danger"><i
                            class="fa fa-times fa-2x"></i> Cerrar
                        </button>


                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--Fin del modal-->

        <!-- Inicial modal para cta banco de clientes-->
        <div class="modal fade" :class="{'mostrar':modal1}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-xs" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="titulo_modal1"></h4>
                        <button type="button" @click="cerrarModal1()" class="close" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    <div class="modal-body">

                            <div class="d-flex justify-content-around">
                                <div class="form-inline">
                                    <label >Total:</label>
                                    <h2 class="mb-2 ml-sm-2">${{totalVenta}}</h2>
                                    <!--                                <input type="text" class="form-control mb-2 ml-sm-2" id="recipient-name">-->
                                </div>
                                <div class="form-inline">
                                    <label for="message-text" >Dinero:</label>
                                    <input type="text" v-model="dinero" ref="btnregistrar" class="form-control mb-2 ml-sm-2" @keyup.enter="calcularDineroVuelto" id="message-text">
                                </div>
                        </div>

                            <div class="form-inline d-flex justify-content-center">
                                <label >Vuelto:</label>
                                <h2 :class="(resultadVuelto >= 0) ? 'mb-2 ml-sm-2 text-success' : 'mb-2 ml-sm-2 text-danger'">${{resultadVuelto}}</h2>
                                <!--                                <input type="text" class="form-control mb-2 ml-sm-2" id="recipient-name">-->

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" ref="confirmRegistry"  @click="confirmarRegistro()" @keyup.enter="confirmarRegistro()">Registrar</button>
                        <button type="button" class="btn btn-secondary"  @click="cerrarModalVuelto()">Cancelar</button>
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

import vSelect from 'vue-select';

export default {
    created() {
        window.addEventListener('keydown', (e) => {
            console.log(e.key);
            if (e.key == 'F8') {
                this.registrarVenta();
            }
            if (e.key == 'F9') {
                this.agregarDetalle();
            }
        });
        this.focusInput();
    },
    data() {

        return {
            totalVenta: 0,
            dinero: '',
            venta_id: 0,
            idcliente: 0,
            idforma: 0,
            idbanco: 0,
            idbanco1: 0,
            idtarjeta: 0,
            idtarjeta1: 0,
            usuario: '',
            fecha_venta:'',
            tipo_identificacion: 'FACTURA',
            num_venta: '',
            total: 0.0,
            subTotal: 0.0,
            forma_pago: '',
            titulo_modal: '',
            titulo_modal1: '',
            arrayVenta: [],
            arrayVenta1: [],
            arrayDetalle: [],
            arrayCtaBcoCliente: [],
            arrayCtaTarCliente: [],
            arrayFormaPago: [],
            listado: 1,
            modal: 0,
            modal1: 0,
            modal2: 0,
            tituloModal: '',
            tipoAccion: 0,
            errorVenta: 0,
            errorMostrarMsjVenta: [],
            ultimoNumeroVenta: 0,
            pagination: {

                'total': 0,
                'current_page': 0,
                'per_page': 0,
                'last_page': 0,
                'from': 0,
                'to': 0,

            },

            pagination2: {

                'total': 0,
                'current_page': 0,
                'per_page': 0,
                'last_page': 0,
                'from': 0,
                'to': 0,

            },

            offset: 3,
            offset2: 3,
            criterio: 'num_venta',
            buscar: '',
            criterioP: 'nombre',
            buscarP: '',
            criterioQ: 'nombre_cliente',
            buscarQ: '',
            criterioR: 'nombre_cliente',
            buscarR: '',
            criterioS: 'factura',
            buscarS: '',
            arrayPago: [],
            arrayProducto: [],
            idproducto: 0,
            codigo: '',
            anulado: 1,
            producto: '',
            precio: 0,
            cantidad: 1,
            stock: 0,
            resultadVuelto: 0
        }

    },

    components: {
        vSelect
    },

    computed: {

        isActived: function () {

            return this.pagination.current_page;
            return this.pagination2.current_page;

        },


        //calcula los elementos de la paginacion
        pagesNumber: function () {

            if (!this.pagination.to) {

                return [];
            }

            if (!this.pagination2.to) {

                return [];
            }

            var from = this.pagination.current_page - this.offset;
            var desde = this.pagination2.current_page - this.offset2;

            if (from < 1) {

                from = 1;
            }

            if (desde < 1) {

                desde = 1;

            }

            var to = from + (this.offset * 2);
            var hasta = desde + (this.offset2 * 2);

            if (to >= this.pagination.last_page) {

                to = this.pagination.last_page;
            }

            if (hasta >= this.pagination2.last_page) {

                hasta = this.pagination2.last_page;

            }

            var pagesArray = [];
            var pageArray1 = [];

            while (from <= to) {

                pagesArray.push(from);
                from++;
            }

            while (desde <= hasta) {

                pageArray1.push(desde);
                desde++;
            }

            return pagesArray, pageArray1;


        },


        calcularTotal: function () {
            var resultado = 0.0;
            for (var i = 0; i < this.arrayDetalle.length; i++) {
                resultado = resultado + (this.arrayDetalle[i].precio * this.arrayDetalle[i].cantidad)
            }
            return resultado.toFixed(2);
        }

    },
    watch: {
        producto: function(newValue) {
            if(newValue != null){

            }
        },
    },
    methods: {

        listarVenta(page, buscar, criterio) {

            let me = this;

            const axios = require('axios');

            var url = '/venta?page=' + page + '&buscar=' + buscar + '&criterio=' + criterio;

            axios.get(url).then(function (response) {
                // handle success
                //console.log(response);
                var respuesta = response.data;
                me.arrayVenta = respuesta.ventas.data;
                me.pagination = respuesta.pagination;
            })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                });

        },

        listarPagos(page, buscarS, criterioS) {

            let me = this;

            const axios = require('axios');

            var url = '/pago/listar?page=' + page + '&buscar=' + buscarS + '&criterio=' + criterioS;

            axios.get(url).then(function (response) {
                // handle success
                //console.log(response);
                var respuesta = response.data;
                me.arrayPago = respuesta.pago.data;
                me.pagination2 = respuesta.pagination;
            })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                });


        },


        buscarNumVenta() {

            let me = this;

            const axios = require('axios');

            var url = '/venta/obtenerUltimoNumeroVenta';

            axios.get(url).then(function (response) {
                var respuesta = response.data;
                me.ultimoNumeroVenta = respuesta.numeroVenta;
                me.num_venta = respuesta.numeroVenta;
            })
                .catch(function (error) {
                    console.log(error);
                });


        },
        selectFormaPago(search, loading) {

            let me = this;
            loading(true)

            const axios = require('axios');

            var url = '/venta/selectFormaPago?filtro=' + search;
            axios.get(url).then(function (response) {
                let respuesta = response.data;
                q: search
                me.arrayFormaPago = respuesta.formas;
                loading(false)
            })
                .catch(function (error) {
                    console.log(error);
                });

        },

        getDatosForma(val1) {
            let me = this;
            me.loading = true;
            me.idforma = val1.id;

        },


        buscarProducto(value) {
            let me = this;

            const axios = require('axios');
            if (me.codigo.length > 3) {
                var url = '/producto/buscarProductoVenta?filtro=' + me.codigo;

                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arrayProducto = respuesta.productos;
                    if (me.arrayProducto.length > 0){
                        if (me.arrayProducto[0].stock > 0) {
                            me.producto = me.arrayProducto[0]['nombre'] + ' - ' + 'Stock: ' + me.arrayProducto[0]['stock'] + ' - ' + 'Precio: $' + me.arrayProducto[0]['precio_venta'];
                            me.idproducto = me.arrayProducto[0]['id'];
                            me.precio = me.arrayProducto[0]['precio_venta'];
                            me.stock = me.arrayProducto[0]['stock'];
                        } else {
                            me.producto = 'NO HAY STOCK - '+ me.arrayProducto[0]['nombre'] + ' - ' + 'Stock: ' + me.arrayProducto[0]['stock'] + ' - ' + 'Precio: $' + me.arrayProducto[0]['precio_venta'];
                            me.idproducto = me.arrayProducto[0]['id'];
                            me.precio = me.arrayProducto[0]['precio_venta'];
                            me.stock = me.arrayProducto[0]['stock'];
                        }
                    }
                    else {
                        me.producto = 'NO EXISTE EL PRODUCTO';
                        me.idproducto = 0;
                    }

                })
                    .catch(function (error) {
                        console.log(error);
                    });
            }


        },

        cambiarPagina(page, buscar, criterio) {

            let me = this;

            //Actualiza  la pagina actual

            me.pagination.current_page = page;

            me.listarVenta(page, buscar, criterio);

        },

        cambiarPagina2(page, buscarS, criterioS) {

            let me = this;

            //Actualiza la pagina actual

            me.pagination2.current_page = page;

            me.listarPagos(page, buscarS, criteriorS);

        },


        encuentra(id) {
            var sw = 0;
            for (var i = 0; i < this.arrayDetalle.length; i++) {
                if (this.arrayDetalle[i].idproducto == id) {
                    sw = true;
                }
            }
            return sw;
        },

        eliminarDetalle(index) {
            let me = this;
            me.arrayDetalle.splice(index, 1);
        },

        agregarDetalle(producto) {
            let me = this;
            if (me.idproducto == 0) {
            } else {
                if (me.encuentra(me.idproducto)) {
                    swal({
                        type: 'error',
                        title: 'Error...',
                        text: 'Ese producto ya fue agregado',
                    });
                    me.codigo = '';
                    me.idproducto = 0;
                    me.producto = '';
                    me.cantidad = 0;
                    me.precio = 0;
                    me.stock = 0;
                    this.$refs.inputCodigo.focus();
                } else {

                    if (1 > me.stock) {
                        swal({
                            type: 'error',
                            title: 'Error...',
                            text: 'No hay stock disponible',
                        })
                    } else {
                        me.arrayDetalle.push({
                            idproducto: me.idproducto,
                            producto: me.producto,
                            cantidad: 1,
                            precio: me.precio,
                            stock: me.stock
                        });
                        me.codigo = '';
                        me.idproducto = 0;
                        me.producto = '';
                        me.cantidad = 0;
                        me.precio = 0;
                        me.stock = 0;
                        this.$refs.inputCodigo.focus();
                    }
                }

            }


        },

        agregarDetalleModal(data = []) {

            let me = this;

            if (me.encuentra(data['id'])) {
                swal({
                    type: 'error',
                    title: 'Error...',
                    text: 'Ese producto ya fue agregado',
                })
            } else {
                me.arrayDetalle.push({
                    idproducto: data['id'],
                    producto: data['nombre'],
                    cantidad: 1,
                    precio: data['precio_venta'],
                    stock: data['stock']
                });
                me.buscarP;
                this.$refs.inputCodigo.focus();
                this.cerrarModal();
            }

        },

        /* para los datos del cliente de bancos*/
        asignardatosbcocliente(data = []) {

            let me = this;

            if (data['idcliente'] == 0) {
                swal({
                    type: 'error',
                    title: 'Error...',
                    text: 'El Cliente no ha sido seleccionado',
                })


            } else {

                /*Para pagos en cheque*/
                me.forma_pago = 'Cheque';
                me.idcliente = data["idcliente"];
                me.idbanco1 = data["idbanco"];
                me.nombre_banco = data["banco"];
                me.idtarjeta1 = data["idtarjeta"];
                me.nombre_tarjeta = 'Sin Tarjeta';
                me.valor = me.total;
                swal({
                    type: 'info',
                    title: 'Informacion',
                    text: 'Se ha asignado datos de clientes con Cheque'
                })
            }

            me.modal1 = 0;

        },

        asignadatostarcliente(data = []) {

            let me = this;

            if (data['idcliente'] == 0) {
                swal({
                    type: 'error',
                    title: 'Error...',
                    text: 'El Cliente no ha sido seleccionado',
                })

            } else {
                /*Para pagos en Tarjeta*/
                /*llenar el arreglo para los pagos en tarjeta*/
                me.forma_pago = 'Tarjeta';
                me.idcliente = data["idcliente"];
                me.idbanco1 = data["idbanco"];
                me.idtarjeta1 = data["idtarjeta"];
                me.nombre_tarjeta = data["ntarjeta"];
                me.valor = me.total;
                /*cuadro de dialogo en el momento que asigna datos*/
                swal({
                    type: 'info',
                    title: 'Informacion',
                    text: 'Se ha asignado datos de clientes con Tarjeta'
                })
            }

            me.modal2 = 0;

        },


        listarProducto(buscar, criterio) {
            let me = this;

            const axios = require('axios');
            var url = '/producto/listarProductoVenta?buscar=' + buscar + '&criterio=' + criterio;
            axios.get(url).then(function (response) {
                var respuesta = response.data;
                me.arrayProducto = respuesta.productos.data;
            })
                .catch(function (error) {
                    console.log(error);
                });
        },

        listarClienteBco(buscarQ, criterioQ) {

            let me = this;

            const axios = require('axios');
            var url = '/bancos/listarBcosCli?buscar=' + buscarQ + '&criterio=' + criterioQ;
            axios.get(url).then(function (response) {
                var respuesta = response.data;
                me.arrayCtaBcoCliente = respuesta.bancoc.data;
            })
                .catch(function (error) {
                    console.log(error);
                });

        },

        listarClienteTarjeta(buscarR, criterioR) {

            let me = this;

            const axios = require('axios');
            var url = '/tarjetas/listaclitarjeta?buscar=' + buscarR + '&criterio=' + criterioR;
            axios.get(url).then(function (response) {
                var respuesta = response.data;
                me.arrayCtaTarCliente = respuesta.tarjetas.data;
            })
                .catch(function (error) {
                    console.log(error);
                });


        },

        calcularVuelto() {

            this.modal1 = 1;
            this.totalVenta = this.total;
            this.titulo_modal1 = 'Registrar Venta';
            this.focusButtonRegistrar();

        },
        registrarVenta() {

            if (this.validarVenta()) {

                return;
            }

            this.calcularVuelto();


        },
confirmarRegistro(){
    let me = this;
    const axios = require('axios');

    axios.post('/venta/registrar', {
        'num_venta': this.num_venta,
        'total': this.total,
        'data': this.arrayDetalle

    }).then(function (response) {
        swal(
            'Venta registrada!',
            'La venta ha sido registrada con éxito.',
            'success'
        );
        /*no hace nada solo si es en efectivo*/
        me.listado = 1;
        me.listarVenta(1, '', 'num_venta');
        me.tipo_identificacion = 'FACTURA';
        me.idcliente = 0;
        me.num_venta = '';
        me.total = 0.0;
        me.idproducto = 0;
        me.producto = '';
        me.cantidad = 0;
        me.precio = 0;
        me.stock = 0;
        me.codigo = '';
        me.arrayDetalle = [];
        me.cerrarModal1();
        this.buscarNumVenta();

    }).catch(function (error) {
        console.log(error);
    });

},
        validarVenta() {

            let me = this;
            me.errorVenta = 0;
            me.errorMostrarMsjVenta = [];
            var prod;

            me.arrayDetalle.map(function (x) {
                if (x.cantidad > x.stock) {
                    prod = x.producto + " con stock insuficiente";
                    me.errorMostrarMsjVenta.push(prod);
                }
            });

            if (me.arrayVenta1.length > 0) me.errorMostrarMsjVenta.push("Ya existe un numero de venta");
            if (!me.num_venta) me.errorMostrarMsjVenta.push("Ingrese el número de venta");
            if (me.arrayDetalle.length <= 0) me.errorMostrarMsjVenta.push("Ingrese detalles");
            if (me.errorMostrarMsjVenta.length) me.errorVenta = 1;

            return me.errorVenta;
        },

        mostrarDetalle() {

            let me = this;

            me.listado = 0;
            me.idproveedor = 0;
            me.num_compra = '';
            me.total = 0.0;
            me.idproducto = 0;
            me.producto = '';
            me.cantidad = 0;
            me.precio = 0;
            me.arrayDetalle = [];
            me.buscarNumVenta();
            me.focusInput();
        },

        mostrarPagos() {

            let me = this;
            me.listado = 3;
            me.idproveedor = 0;
            me.tipo_identificacion = 'FACTURA';
            me.num_compra = '';
            me.impuesto = 0.12;
            me.arrayPago = [];


        },

        ocultarDetalle() {
            this.listado = 1;
        },

        ocultarPagos() {
            this.listado = 1;

        },
        calcularDineroVuelto(){
            let resultado = this.dinero - this.total
            this.resultadVuelto = resultado.toFixed(2);
            resultado >=0 ? this.$refs.confirmRegistry.focus() : this.$refs.btnregistrar.focus();
        },

        verVenta(id) {

            let me = this;
            me.listado = 2;

            //Obtener los datos de la compra
            var arrayVentaT = [];

            const axios = require('axios');

            var url = '/venta/obtenerCabecera?id=' + id;

            axios.get(url).then(function (response) {
                var respuesta = response.data;
                arrayVentaT = respuesta.ventas;

                me.usuario = arrayVentaT[0]['usuario'];
                me.fecha_venta = arrayVentaT[0]['fecha_venta'];
                me.num_venta = arrayVentaT[0]['num_venta'];
                me.total = arrayVentaT[0]['total'];
            })
                .catch(function (error) {
                    console.log(error);
                });

            //Obtener los datos de los detalles
            var urld = '/venta/obtenerDetalle?id=' + id;

            axios.get(urld).then(function (response) {
                var respuesta = response.data;
                console.log(respuesta.detalles);
                me.arrayDetalle = respuesta.detalles;
            })
                .catch(function (error) {
                    console.log(error);
                });
        },

        cerrarModal() {

            this.modal = 0;
            this.tituloModal = '';

        },
        cerrarModalVuelto() {

            this.modal1 = 0;
            this.titulo_modal1 = '';
            this.resultadVuelto = 0;
            this.resultadVuelto = 0;
            this.dinero= '';
            this.focusInput();

        },
        cerrarModal1() {

            this.modal1 = 0;
            this.titulo_modal1 = '';
            this.resultadVuelto = 0;
            this.resultadVuelto = 0;
            this.dinero= '';

        },

        cerrarModal2() {

            this.modal2 = 0;
            this.titulo_modal = '';

        },

        abrirModal() {

            this.arrayProducto = [];
            this.modal = 1;
            this.buscarP = '';
            this.tituloModal = 'Seleccione uno o varios productos';
            this.focusInputBusqueda();

        },

        desactivarVenta(id) {
            swal({
                title: 'Esta seguro de anular la compra?',
                //type: 'warning',
                showCancelButton: true,
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

                    axios.put('/venta/desactivar', {
                        'id': id
                    }).then(function (response) {
                        me.listarVenta(1, '', 'num_venta');
                        swal(
                            'Anulado!',
                            'La venta ha sido anulada con éxito.',
                            'success'
                        )
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

        pdfVenta(id, estado) {


            window.open('http://127.0.0.1:8080/venta/pdf' + id + ',' + '_blank');


        },

        pdfPago(id, cliente) {

            window.open('http://127.0.0.1:8080/pago/pdf' + id + ',' + '_blank');

        },
        focusInput() {
            this.$nextTick(() => {
                this.$refs.inputCodigo.focus();
            });

        },
        focusInputBusqueda() {
            this.$nextTick(() => {
                this.$refs.focusInputBusqueda.focus();
            });

        },
        focusButtonRegistrar() {
            this.$nextTick(() => {
                this.$refs.btnregistrar.focus();
            });
        },

    },

    mounted() {

        this.buscarNumVenta();
        this.listarVenta(1, this.buscar, this.criterio);
    }
}
</script>

<style>

.modal-content {

    width: 100% !important;
    position: absolute !important;
}

.mostrar {

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

@media (min-width: 600px) {
    .btnagregar {
        margin-top: 2rem;
    }
}

</style>
