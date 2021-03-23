<?php

namespace App\Http\Controllers;

use App\Caja;
use App\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Compra;
use App\DetalleCompra;

class CajaController extends Controller
{
    //
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar==''){
            $cajas = Caja::join('users','cajas.idusuario','=','users.id')
            ->select('cajas.id','cajas.fecha',
            'cajas.monto_inicio','cajas.monto_final','cajas.estado','users.usuario')
            ->orderBy('cajas.id', 'desc')->paginate(3);
        }
        else{
            $cajas = Caja::join('users','cajas.idusuario','=','users.id')
            ->select('cajas.id','cajas.fecha',
                'cajas.monto_inicio','cajas.monto_final','cajas.estado','users.usuario')
            ->where('cajas.'.$criterio, 'like', '%'. $buscar . '%')->orderBy('cajas.id', 'desc')->paginate(3);
        }

        return [
            'pagination' => [
                'total'        => $cajas->total(),
                'current_page' => $cajas->currentPage(),
                'per_page'     => $cajas->perPage(),
                'last_page'    => $cajas->lastPage(),
                'from'         => $cajas->firstItem(),
                'to'           => $cajas->lastItem(),
            ],
            'cajas' => $cajas
        ];
    }

    public function verificarCaja(Request $request){

        if (!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;
        $caja = Caja::where('fecha','like','%'.$filtro.'%')->first();

        return ['caja' => $caja];

    }

    public function obtenerCabecera(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $caja = Compra::join('proveedores','cajas.idproveedor','=','proveedores.id')
        ->join('users','cajas.idusuario','=','users.id')
        ->select('cajas.id','cajas.tipo_identificacion',
        'cajas.num_compra','cajas.fecha_compra','cajas.total',
        'cajas.estado','proveedores.nombre','users.usuario')
        ->where('cajas.id','=',$id)
        ->orderBy('cajas.id', 'desc')->take(1)->get();

        return ['compra' => $caja];


    }

    public function obtenerDetalles(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $detalles = DetalleCompra::join('productos','detalle_cajas.idproducto','=','productos.id')
        ->select('detalle_cajas.cantidad','detalle_cajas.precio','productos.nombre as producto')
        ->where('detalle_cajas.idcompra','=',$id)
        ->orderBy('detalle_cajas.id','desc')->get();

        return ['detalles' => $detalles];

    }

    public function pdf(Request $request,$id){

        $cajas = Compra::join('proveedores','cajas.idproveedor','=','proveedores.id')
        ->join('users','cajas.idusuario','=','users.id')
        ->select('cajas.id','cajas.tipo_identificacion','cajas.num_compra',
        'cajas.created_at','cajas.total',
        'cajas.estado','proveedores.nombre','proveedores.tipo_documento',
        'proveedores.num_documento','proveedores.direccion','proveedores.email',
        'proveedores.telefono','users.usuario','users.nombre as nom_usuario')
        ->where('cajas.id','=',$id)
        ->orderBy('cajas.id', 'desc')->take(1)->get();

        $detalles = DetalleCompra::join('productos','detalle_cajas.idproducto','=','productos.id')
        ->select('detalle_cajas.cantidad','detalle_cajas.precio','productos.id',
        'productos.nombre as producto','productos.stock')
        ->where('detalle_cajas.id','=',$id)
        ->orderBy('detalle_cajas.id', 'desc')->get();

        $numcompra=Compra::select('num_compra')->where('id',$id)->get();

        $pdf = \PDF::loadView('pdf.compra',['cajas'=>$cajas,'detalles'=>$detalles]);
        return $pdf->download('compra-'.$numcompra[0]->num_compra.'.pdf');



    }



    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        try{
            DB::beginTransaction();

            $mytime= new \DateTime();
            $caja = new Caja();
            $caja->fecha = $mytime->format('Y-m-d H:i:s');
            $caja->monto_inicio = $request->monto;
            $caja->idusuario = \Auth::user()->id;
            $caja->estado = 'Activo';
            $caja->save();

            DB::commit();
        } catch (Exception $e){
            DB::rollBack();
        }
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $caja = Compra::findOrFail($request->id);
        $caja->estado = 'Anulado';
        $caja->save();
    }
    public function sumarStock($detalles){

        foreach($detalles as $a=>$det)
        {
            $detalle = Producto::where('id',$det['idproducto'])->first();
            $detalle->stock = $detalle->stock + $det['cantidad'];
            $detalle->save();
        }
    }
    public function cerrarCaja(Request $request){
        if (!$request->ajax()) return redirect('/');
        try{
            DB::beginTransaction();

            $mytime= new \DateTime();
            $caja = Caja::where('id',$request->id)->first();
            $caja->monto_final = $request->montoCierre;
            $caja->estado = 'Cerrada';
            $caja->save();

            DB::commit();
        } catch (Exception $e){
            DB::rollBack();
        }
    }
}
