<template>
   <main class="main">
            <!-- Breadcrumb -->
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><a href="/">Inicio</a></li>
            </ol>
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card">

                    <template v-if="listado==1">
                    <div class="card-header">

                       <h2>Listado de Cierres de Caja</h2><br/>

                        <button class="btn btn-primary btn-lg" type="button" @click="mostrarDetalle()">
                            <i class="fa fa-plus fa-2x"></i>&nbsp;&nbsp;Nueva Compra
                        </button>
                    </div>
                    <!--listado-->

                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select class="form-control col-md-3" v-model="criterio">
                                      <option value="monto_final">Tipo identificación</option>
                                      <option value="monto_inicial">Número Compra</option>
                                      <option value="fecha">Fecha Compra</option>
                                    </select>
                                    <input type="text"  @keyup.enter="listarCaja(1,buscar,criterio);" v-model="buscar" class="form-control" placeholder="Buscar texto">
                                    <button type="submit"  @click="listarCaja(1,buscar,criterio);" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr class="bg-primary">


                                        <th>Fecha</th>
                                        <th>Monto Inicial</th>
                                        <th>Monto Final</th>
                                        <th>Usuario</th>
                                        <th>Estado</th>
                                        <th>Cambiar estado</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr v-for="compra in arrayCaja" :key="compra.id">

                                        <td v-text="compra.fecha"></td>
                                        <td v-text="compra.monto_inicial"></td>
                                        <td v-text="compra.monto_final"></td>
                                        <td v-text="compra.usuario"></td>
                                        <td>

                                             <button type="button" v-if="compra.estado=='Registrado'" class="btn btn-success btn-sm">
                                                <i class="fa fa-check fa-2x"></i> Registrado
                                            </button>

                                            <button type="button" v-else class="btn btn-danger btn-sm">
                                                 <i class="fa fa-times fa-2x"></i> Anulado
                                            </button>

                                        </td>

                                         <td>
                                            <template v-if="compra.estado=='Registrado'">
                                                <button type="button" class="btn btn-danger btn-sm" @click="desactivarCompra(compra.id)">
                                                    <i class="fa fa-times fa-2x"></i> Anular Compra
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
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" v-f="pagination.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar,criterio)">Anterior</a>
                                </li>

                                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar,criterio)" v-text="page"></a>
                                </li>


                                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar,criterio)">Siguiente</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    </template>
                    <!--fin listado-->

                    <!-- Detalle-->

                    <template v-else-if="listado==0">

                    <span><strong>(*) Campo obligatorio</strong></span><br/>

                    <h3 class="text-center">LLenar el formulario</h3>

                    <div class="card-body">

                        <div class="form-group row border">

                             <div class="col-md-8">
                                <div class="form-group">
                                    <label class="text-uppercase"><strong>Número Compra(*)</strong></label>
                                    <input type="text" class="form-control" @keyup.enter="buscarNumCompra();" v-model="monto_inicial" placeholder="">
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="text-uppercase"><strong>Proveedor(*)</strong></label>
                                    <v-select
                                        :on-search="selectProveedor"
                                        label="nombre"
                                        :options="arrayProveedor"
                                        placeholder="Buscar Proveedores..."
                                        :onChange="getDatosProveedor"
                                    >

                                    </v-select>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="text-uppercase"><strong>Tipo Identificación(*)</strong></label>
                                    <select class="form-control" v-model="monto_final">
                                        <option value="0">Seleccione</option>
                                        <option value="FACTURA">Factura</option>
                                        <option value="TICKET">Ticket</option>

                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="form-group row">

                            <div class="col-md-12">
                                    <div v-show="errorCompra" class="form-group row div-error">
                                        <div class="text-center text-error">
                                            <div v-for="error in errorMostrarMsjCompra" :key="error" v-text="error">

                                            </div>
                                        </div>
                                    </div>
                                </div>

                        </div>


                        <div class="form-group row border">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Producto <span class="text-error" v-show="idproducto==0">(*Ingrese código del producto)</span></label>
                                    <div class="form-inline">
                                        <input type="text" class="form-control" v-model="codigo" @keyup.enter="buscarProducto()" placeholder="Ingrese código">
                                        <button @click="abrirModal()" class="btn btn-primary">

                                           <i class="fa fa-search"></i>&nbsp;Buscar Productos

                                        </button>
                                        <input type="text" readonly class="form-control" v-model="producto">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Precio <span class="text-error" v-show="precio==0">(*Ingrese un valor)</span></label>
                                    <input type="number" value="0" step="any" class="form-control" v-model="precio">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Cantidad <span class="text-error" v-show="cantidad==0">(*Ingrese un valor)</span></label>
                                    <input type="number" value="0" class="form-control" v-model="cantidad">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <button @click="agregarDetalle()" class="btn btn-primary form-control btnagregar"><i class="fa fa-plus fa-2x"></i> Agregar detalle</button>
                                </div>
                            </div>
                        </div>

                        <br/><br/>

                        <div class="form-group row border">

                            <h3>Lista de Compras a Proveedores</h3>

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
                                                <button @click="eliminarDetalle(index)" type="button" class="btn btn-danger btn-sm">
                                                    <i class="fa fa-times fa-2x"></i>
                                                </button>
                                            </td>
                                            <td v-text="detalle.producto">
                                            </td>
                                            <td>
                                                <input v-model="detalle.precio" type="number" value="3" class="form-control">
                                            </td>
                                            <td>
                                                <input v-model="detalle.cantidad" type="number" value="2" class="form-control">
                                            </td>
                                            <td>
                                                {{detalle.precio*detalle.cantidad}}
                                            </td>
                                        </tr>
                                        <tr style="background-color: grey;">
                                            <td colspan="4" align="right"><strong>Total:</strong></td>
                                            <td><strong>$ {{total=calcularTotal}}</strong></td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td colspan="5">
                                                No se han agregado productos
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-danger" @click="ocultarDetalle()"><i class="fa fa-times fa-2x"></i> Cerrar</button>
                                <button type="button" class="btn btn-success" @click="registrarCompra()"><i class="fa fa-save fa-2x"></i> Registrar Compra</button>
                            </div>
                        </div>
                    </div>
                    </template>

                    <!-- Fin Detalle-->

                    <template v-else-if="listado==2">

                        <h2 class="text-center">Detalle de Compra</h2><br/>

                        <div class="card-body">
                            <div class="form-group row border">
                                <div class="col-md-3">
                                    <div class="form-group">
                                         <label class="text-uppercase"><strong>Proveedor</strong></label>
                                         <p v-text="proveedor"></p>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                         <label class="text-uppercase"><strong>monto_final</strong></label>
                                         <p v-text="monto_final"></p>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                         <label class="text-uppercase"><strong>Num Compra</strong></label>
                                         <p v-text="monto_inicial"></p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="text-uppercase"><strong>Fecha</strong></label>
                                        <p v-text="monto_inicial"></p>
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
                                            {{detalle.precio * detalle.cantidad}}
                                        </td>
                                    </tr>
                                    <tr style="background-color: grey;">
                                        <td colspan="3" align="right"><strong>Total:</strong></td>
                                        <td><strong>$ {{total = calcularTotal}}</strong></td>
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
                            <button type="button" @click="ocultarDetalle()" class="btn btn-danger"><i class="fa fa-times fa-2x"></i> Cerrar</button>

                         </div>
                        </div>
                        </div>
                </template>

                 <!-- fin ver compra-->


                </div>
                <!-- Fin ejemplo de tabla Listado -->
            </div>
<!--            &lt;!&ndash;Inicio del modal agregar/actualizar&ndash;&gt;-->
<!--            <div class="modal fade" :class="{'mostrar':modal}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">-->
<!--                <div class="modal-dialog modal-primary modal-lg" role="document">-->
<!--                    <div class="modal-content">-->
<!--                        <div class="modal-header">-->
<!--                            <h4 class="modal-title" v-text="tituloModal"></h4>-->
<!--                            <button type="button" @click="cerrarModal()" class="close" aria-label="Close">-->
<!--                              <span aria-hidden="true">×</span>-->
<!--                            </button>-->
<!--                        </div>-->
<!--                        <div class="modal-body">-->
<!--                            <div class="table-responsive">-->
<!--                                <div class="form-group row">-->
<!--                                  <div class="col-md-6">-->
<!--                                     <div class="input-group">-->
<!--                                        <select class="form-control col-md-3" v-model="criterioP">-->
<!--                                           <option value="nombre">Producto</option>-->
<!--                                           <option value="codigo">Codigo</option>-->

<!--                                        </select>-->
<!--                                        <input type="text"  @keyup.enter="listarProducto(buscarP,criterioP);" v-model="buscarP" class="form-control" placeholder="Buscar texto">-->
<!--                                        <button type="submit"  @click="listarProducto(buscarP,criterioP);" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>-->
<!--                                      </div>-->
<!--                                     </div>-->
<!--                                    </div>-->
<!--                                <table class="table table-bordered table-striped table-sm">-->
<!--                                  <thead>-->
<!--                                    <tr class="bg-primary">-->

<!--                                    <th>Categoria</th>-->
<!--                                    <th>Producto</th>-->
<!--                                    <th>Codigo</th>-->
<!--                                    <th>Precio Venta ($)</th>-->
<!--                                    <th>Stock</th>-->
<!--                                    <th>Estado</th>-->
<!--                                    <th>Accion</th>-->
<!--                                </tr>-->
<!--                            </thead>-->
<!--                            <tbody>-->

<!--                                <tr v-for="producto in arrayProducto" :key="producto.id">-->

<!--                                    <td v-text="producto.nombre_categoria"></td>-->
<!--                                    <td v-text="producto.nombre"></td>-->
<!--                                    <td v-text="producto.codigo"></td>-->
<!--                                    <td v-text="producto.precio_venta"></td>-->
<!--                                    <td v-text="producto.stock"></td>-->
<!--                                    <td>-->
<!--                                        <button type="button" class="btn btn-success btn-md" v-if="producto.condicion">-->

<!--                                          <i class="fa fa-check fa-2x"></i> Activo-->
<!--                                        </button>-->

<!--                                        <button type="button" class="btn btn-danger btn-md" v-else>-->

<!--                                          <i class="fa fa-check fa-2x"></i> Desactivado-->
<!--                                        </button>-->

<!--                                    </td>-->
<!--                                    <td>-->
<!--                                         <button type="button" @click="agregarDetalleModal(producto)" class="btn btn-success btn-md">-->

<!--                                             <i class="fa fa-check fa-2x"></i> Agregar Producto-->
<!--                                         </button>-->
<!--                                    </td>-->


<!--                                </tr>-->

<!--                            </tbody>-->
<!--                        </table>-->

<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="modal-footer">-->
<!--                            <button type="button" @click="cerrarModal()" class="btn btn-danger"><i class="fa fa-times fa-2x"></i> Cerrar</button>-->

<!--                        </div>-->
<!--                    </div>-->
<!--                    &lt;!&ndash; /.modal-content &ndash;&gt;-->
<!--                </div>-->
<!--                &lt;!&ndash; /.modal-dialog &ndash;&gt;-->
<!--            </div>-->
<!--            &lt;!&ndash;Fin del modal&ndash;&gt;-->


        </main>
</template>

<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script>

   import vSelect from 'vue-select';

   export default {
        data(){

            return {

                compra_id: 0,
                idproveedor:0,
                proveedor:'',
                nombre : '',
                monto_final : 'FACTURA',
                monto_inicial : '',
                total:0.0,
                subTotal: 0.0,
                arrayCaja : [],
                arrayCaja1 : [],
                arrayProveedor: [],
                arrayDetalle : [],
                listado:1,
                modal : 0,
                tituloModal : '',
                tipoAccion : 0,
                errorCompra : 0,
                errorMostrarMsjCompra : [],
                pagination:{

                    'total': 0,
                    'current_page': 0,
                    'per_page': 0,
                    'last_page': 0,
                    'from': 0,
                    'to': 0,

                },
                offset:3,
                criterio:'monto_inicial',
                buscar:'',
                buscarP:'',
                criterioP:'nombre',
                arrayProducto : [],
                idproducto : 0,
                codigo : '',
                producto : '',
                precio : 0.0,
                cantidad : 0
            }

        },

        components: {
           vSelect
        },

        computed:{

            isActived: function(){

              return this.pagination.current_page;

            },

             //calcula los elementos de la paginacion
            pagesNumber: function(){

                if(!this.pagination.to){

                    return[];
                }

                var from = this.pagination.current_page - this.offset;
                if(from < 1){

                   from = 1;
                }

                var to = from + (this.offset * 2);
                if(to >= this.pagination.last_page){

                   to = this.pagination.last_page;
                }

                var pagesArray = [];
                while(from <= to){

                   pagesArray.push(from);
                   from++;
                }
                return pagesArray;


            },


            calcularTotal: function(){
                var resultado=0.0;
                for(var i=0;i<this.arrayDetalle.length;i++){
                    resultado=resultado+(this.arrayDetalle[i].precio*this.arrayDetalle[i].cantidad)

                }
                return resultado;

            }

        },

        methods:{

           listarCaja(page,buscar,criterio){

               let me=this;

               const axios = require('axios');

               var url= '/caja?page=' + page + '&buscar='+ buscar + '&criterio='+criterio;

               axios.get(url).then(function (response) {
                    // handle success
                    //console.log(response);
                    var respuesta = response.data;
                    me.arrayCaja=respuesta.compras.data;
                    me.pagination= respuesta.pagination;
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                });
           },








           verCompra(id){

               let me = this;
               me.listado=2;
               //Obtener los datos de la compra
               var arrayCajaT=[];

               const axios = require('axios');

               var url='/compra/obtenerCabecera?id=' +id;

               axios.get(url).then(function (response){
                    var respuesta = response.data;
                    // me.arrayCajaT = respuesta.compra;
                   arrayCajaT = respuesta.compra;
                    me.proveedor = arrayCajaT[0]['nombre'];
                    me.monto_final = arrayCajaT[0]['monto_final'];
                    me.monto_inicial = arrayCajaT[0]['monto_inicial'];
                    me.total = arrayCajaT[0]['total'];

               })
                .catch(function (error) {
                    console.log(error);
                });

                //Obtener datos de los detalles
                var urld = '/compra/obtenerDetalles?id=' + id;

                axios.get(urld).then(function (response){
                     var respuesta = response.data;
                    console.log(respuesta.detalles);
                     me.arrayDetalle = respuesta.detalles;

                })
                 .catch(function (error) {
                    console.log(error);
                });



           },

           cambiarPagina(page,buscar,criterio){

              let me = this;

              //Actualiza  la pagina actual

               me.pagination.current_page=page;

               me.listarCompra(page,buscar,criterio);

           },

            cambiarPaginaP(pageP,buscarP,criterioP){

                let me = this;

                //Actualiza la pagina actual

                me.paginationP.current_page=pageP;

                me.listarProducto(pageP,buscarP,criterioP);

            },


           registrarCompra(){

               if(this.validarCompra()){

                   return;
               }

               let me=this;

               const axios = require('axios');

               axios.post('/compra/registrar',{

                    'idproveedor': me.idproveedor,
                    'monto_final': me.monto_final,
                    'monto_inicial' : me.monto_inicial,
                    'total' : me.total,
                    'data' : me.arrayDetalle

               }).then(function (response) {
                    // handle success
                    console.log(response);
                    me.listado=1;
                    me.listarCompra(1,'','monto_inicial');
                    me.idproveedor=0;
                    me.monto_final='FACTURA';
                    me.monto_inicial='';
                    me.total=0.0;
                    me.idproducto=0;
                    me.producto='';
                    me.cantidad=0;
                    me.precio=0;
                    me.arrayDetalle=[];

                }).catch(function (error) {
                    // handle error
                    console.log(error);
                });


           },

           validarCompra(){

               this.errorCompra=0;
               this.errorMostrarMsjCompra =[];

                if (this.arrayCaja1.length>0) me.errorMostrarMsjCompra.push("Ya existe un numero de compra");

                if (this.idproveedor===0) this.errorMostrarMsjCompra.push("(*)Debe de seleccionar un proveedor");
                if (this.monto_final===0) this.errorMostrarMsjCompra.push("(*)Debe de seleccionar tipo de identificacion");
                if (!this.monto_inicial) this.errorMostrarMsjCompra.push("(*)Debe ingresar numero de compra");
                if (this.arrayDetalle.length<=0) this.errorMostrarMsjCompra.push("(*)Debe haber datos de detalle de productos");

                if (this.errorMostrarMsjCompra.length) this.errorCompra = 1;

                return this.errorCompra;

           },



            mostrarDetalle(){
              let me = this;

              me.listado=0;
              me.listarCompra(1,'','monto_inicial');
              me.idproveedor=0;
              me.monto_final='FACTURA';
              me.monto_inicial='';
              me.total=0.0;
              me.idproducto=0;
              me.producto='';
              me.cantidad=0;
              me.precio=0;
              me.arrayDetalle=[];

            },
            ocultarDetalle(){
                this.listado=1;
            },

           cerrarModal(){

               this.modal=0;
               this.tituloModal='';

           },

           abrirModal(){

                this.arrayProducto=[];
                this.modal=1;
                this.tituloModal='Seleccione uno o varios articulos';


           },

              desactivarCompra(id){
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

                    axios.put('/compra/desactivar',{
                        'id': id
                    }).then(function (response) {
                        me.listarCompra(1,'','monto_inicial');
                        swal(
                        'Anulado!',
                        'La compra ha sido anulada con éxito.',
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

        },

        mounted() {
            //console.log('Component mounted.')
            this.listarCaja(1,this.buscar,this.criterio);
        }
    }
</script>

<style>

     .modal-content{

      width:100% !important;
      position:absolute !important;
  }

  .mostrar{

      display:list-item !important;
      opacity:1 !important;
      position:absolute !important;
      background-color:#3c29297a !important;
  }

   .div-error{

      display:flex;
      justify-content:center;
  }

  .text-error{

      color:red !important;
      font-weight:bold;
      font-size:20px;
  }

  @media (min-width: 600px) {
        .btnagregar {
            margin-top: 2rem;
        }
    }

</style>
