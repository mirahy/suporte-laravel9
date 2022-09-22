<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Validator;
use App;
use App\Registro;
use App\ItemInventario;
use App\ItemInventarioPatrimoniado;
use App\Patrimonio;
use App\EstadoConservacao;
use App\Status;
use App\Usuario;
use App\PendenciaInventario;

class AdministracaoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permissao:ADMINISTRADOR,PRESIDENTE,PATRIMONIO');
    }

    public function usuarioLogado() {
        $user = Auth::user();
        if ($user != null)
            return Usuario::where('login', $user->email)->get()->first();
        return null;
    }

    public function getItensSala() {
        $param = "1 ";
        $numero = Input::get('numero');
        if ($numero != null) {
            $param = "numero = $numero ";
        }
        else {
            $descricaoPart = Input::get('descricaoPart');
            if ($descricaoPart != null) {
                $param .= "AND (patrimonio.descricao like '%$descricaoPart%' OR item_inventario.descricao like '%$descricaoPart%' OR item_inventario.observacoes like '%$descricaoPart%') ";
            }
            $salaId = Input::get('salaId');
            $salaNovaId = Input::get('salaNovaId');
            if ($salaId != null && $salaId == $salaNovaId)
                $param .= "AND (patrimonio.sala_id = $salaId OR item_inventario.sala_id = $salaId) ";
            else {
                if ($salaId != null)
                    $param .= "AND patrimonio.sala_id = $salaId ";
                if ($salaNovaId != null)
                    $param .= "AND item_inventario.sala_id = $salaNovaId ";
            }
            /*switch ($salaId) {
                case "all":
                case null:
                    //$param .= " patrimonio.sala_id = 70 AND item_inventario.sala_id = 70";
                    break;
                case "not":
                    $param .= "AND (patrimonio.sala_id IS NULL AND item_inventario.sala_id IS NULL) ";
                    break;
                default:
                    $param .= "AND (patrimonio.sala_id = $salaId OR item_inventario.sala_id = $salaId) ";
            }  */
            $usuarioId = Input::get('usuarioId');
            if ($usuarioId != null) {
                $param .= "AND item_inventario.id IN (SELECT item_inventario.id FROM item_inventario
                    JOIN responsavel_inventario ON (responsavel_inventario.item_inventario_id = item_inventario.id)
                    WHERE responsavel_inventario.usuario_id = $usuarioId) ";
            }
            $estadoId = Input::get('estadoId');
            if ($estadoId != null) {
                if ($estadoId == "-1")
                    $param .= "AND item_inventario_patrimoniado.estado_id IS NULL ";
                else
                    $param .= "AND item_inventario_patrimoniado.estado_id = $estadoId ";
            }
        }


        //$param = "patrimonio.sala_id = 70 OR item_inventario.sala_id = 70";
        $sql = "SELECT item_inventario.id AS numero,
                patrimonio.status_id AS status_id,
                item_inventario_patrimoniado.estado_id AS estado_id,
                patrimonio.estado_conservacao_id AS estado_anterior_id,
                patrimonio.descricao AS descricao,
                item_inventario.descricao AS descricao_nova,
                patrimonio.sala_id AS sala_id,
                item_inventario.sala_id AS sala_nova_id,
                GROUP_CONCAT( responsavel_inventario.usuario_id SEPARATOR ',') as responsaveis,
                item_inventario.observacoes FROM patrimonio
            RIGHT JOIN item_inventario_patrimoniado ON patrimonio.numero = item_inventario_patrimoniado.patrimonio_id
            RIGHT JOIN item_inventario ON item_inventario.id = item_inventario_patrimoniado.item_inventario_id
            LEFT JOIN responsavel_inventario ON responsavel_inventario.item_inventario_id = item_inventario.id
            WHERE $param
            GROUP BY item_inventario.id, patrimonio.status_id, item_inventario_patrimoniado.estado_id, patrimonio.estado_conservacao_id, patrimonio.descricao, item_inventario.descricao, item_inventario.observacoes, patrimonio.sala_id, item_inventario.sala_id";
        //return DB::select($sql, [$sala->id,$sala->id]);
        //return $sql;
        return DB::select($sql);
    }

    private function getUltimoIdSP() {
        $sql = "SELECT MIN(id) as id FROM item_inventario";
        $id = DB::select($sql)[0]->id;
        $id =  $id < 0 ? $id : 0;
        return $id;
    }

    public function putRegistro() {
        $r = NULL;
        $itm = NULL;
        $ip = NULL;
        $pat = NULL;
        $chave = Input::get('numero');

        $validator = null;

        if ($chave == NULL || $chave == 0) {
            $r = new Registro();
            $r->numero = $this->getUltimoIdSP()-1;
            $itm = new ItemInventario();
            $itm->id = $r->numero;
            $validator = Validator::make(Input::all(), $this->getValidationRules('SP'));
        }
        else {
            $itm = ItemInventario::find($chave);
            if ($itm != NULL) {
                $ip = ItemInventarioPatrimoniado::where('item_inventario_id', $chave)->first();
                $r = Registro::getRegistroPorItem ($itm, $ip);
                $validator = Validator::make(Input::all(), $this->getValidationRules($ip == null ? 'SP' : 'LC'));
            }
            else {
                $r = new Registro();
                $r->numero = $chave;
                $itm = new ItemInventario();
                $itm->id = $chave;
                $ip = new ItemInventarioPatrimoniado();
                $ip->item_inventario_id = $chave;
                $pat = Patrimonio::find($chave);
                if ($pat != null) {
                    $r->status_id = $pat->status_id;
                    $r->estado_anterior_id = $pat->estado_conservacao_id;
                    $r->descricao = $pat->descricao;
                    $r->sala_id = $pat->sala_id;
                    $validator = Validator::make(Input::all(), $this->getValidationRules('BX'));
                }
                else {
                    $validator = Validator::make(Input::all(), $this->getValidationRules('NW'));
                }
            }
            $r->estado_id = Input::get('estado_id');
        }

        if ($validator->fails()) {
            abort(403, 'Erro de Validação!');
            return false;
        }

        $r->descricao_nova = Input::get('descricao_nova');
        $r->observacoes  = Input::get('observacoes');
        $r->sala_nova_id = intval(Input::get('sala_nova_id'));
        if ( is_array(Input::get('responsaveis_ids')) && !empty(Input::get('responsaveis_ids')))
            $r->responsaveis = Input::get('responsaveis_ids');
        else
            $r->responsaveis = [$this->usuarioLogado()->id];
        $this->salvarRegistro($r,$itm,$ip);
        return $r;
    }
    private function salvarRegistro(Registro $r, ItemInventario $itm, $ip){
        if ($this->usuarioLogado()->permissao == Usuario::PERMISSAO_PATRIMONIO) {
            abort(401, "Não Autorizado!");
            return;
        }
        $estadoNL = EstadoConservacao::where('sigla_inventario', 'NL')->first();
        //$itm = new ItemInventario();
        //$itm->id = $r->numero;
        if ($itm->id == $r->numero) {
            $rOld = Registro::getRegistroPorItem ($itm, $ip);

            $itm->descricao = $r->descricao == null || $r->descricao == "" ? $r->descricao_nova : null;
            $itm->sala_id = $r->estado_id != null && $r->estado_id == $estadoNL->id ? NULL : $r->sala_nova_id;
            $itm->observacoes = $r->observacoes;
            $itm->save();
            $itm->id = $r->numero;
            $itm->responsaveis()->sync($r->responsaveis);
            //$ip = new ItemInventarioPatrimoniado();
            //$ip->item_inventario_id = $r->numero;
            if ($ip != null) {
                $ip->estado_id = $r->estado_id;
                $ip->save();
            }

            if ($rOld->descricao == NULL && $rOld->descricao_nova == NULL)
                App::make('LogService')->geraLogCreate($r);
            else
                App::make('LogService')->geraLogUpdate($rOld,$r);
        }
        else
            abort(400,"Erro na identificação do Item");
    }
    public function deleteRegistro($id) {
        if ($this->usuarioLogado()->permissao == Usuario::PERMISSAO_PATRIMONIO) {
            abort(401, "Não Autorizado!");
            return;
        }
        $itm = ItemInventario::find($id);
        if ($itm != NULL) {
            $statusAtivo = Status::where('situacao', 'Ativo')->first();
            $pat = Patrimonio::find($id);
            if ($pat == null || $pat->status_id != $statusAtivo->id) {
                $ip = ItemInventarioPatrimoniado::where('item_inventario_id', $id)->first();
                $rOld = Registro::getRegistroPorItem ($itm, $ip);
                if ($ip != null) {
                    $ip->delete();
                }
                $itm->delete();
                App::make('LogService')->geraLogDelete($rOld);
                return $id;
            }
        }
        abort (400,"Este item não pode ser excluído!");
    }
    private function getValidationRules($tipo) {

        $rules = array();

        switch($tipo) {
            case "SP":
                $rules['sala_nova_id'] = 'required';
                $rules['descricao_nova'] = 'required';
                break;
            case "LC":
                $rules['numero'] = 'required';
                $rules['sala_nova_id'] = 'required';
                $rules['estado_id'] = 'required';
                break;
            case "BX":
                $rules['numero'] = 'required';
                $rules['sala_nova_id'] = 'required';
                $rules['estado_id'] = 'required';
                break;
            case "NW":
                $rules['numero'] = 'required';
                $rules['sala_nova_id'] = 'required';
                $rules['descricao_nova'] = 'required';
                $rules['estado_id'] = 'required';
                break;
        }
        return $rules;
    }

    public function getPendencias() {
        /*$ps = PendenciaInventario::all();
        foreach ($ps as $p) {

        }*/
        $sql = "SELECT item_inventario.id AS numero,
            pendencia_inventario.id AS pendencia_id,
            pendencia_inventario.descricao AS pendencia_descricao,
            pendencia_inventario.resolvido,
            patrimonio.status_id AS status_id,
            item_inventario_patrimoniado.estado_id AS estado_id,
            patrimonio.estado_conservacao_id AS estado_anterior_id,
            patrimonio.descricao AS descricao,
            item_inventario.descricao AS descricao_nova,
            patrimonio.sala_id AS sala_id,
            item_inventario.sala_id AS sala_nova_id,
            GROUP_CONCAT( responsavel_inventario.usuario_id SEPARATOR ',') as responsaveis,
            item_inventario.observacoes FROM pendencia_inventario
        INNER JOIN item_inventario ON pendencia_inventario.item_inventario_id = item_inventario.id
        LEFT JOIN item_inventario_patrimoniado ON item_inventario.id = item_inventario_patrimoniado.item_inventario_id
        LEFT JOIN patrimonio ON patrimonio.numero = item_inventario_patrimoniado.patrimonio_id
        LEFT JOIN responsavel_inventario ON responsavel_inventario.item_inventario_id = item_inventario.id
        WHERE 1
        GROUP BY item_inventario.id, pendencia_inventario.id, patrimonio.status_id, item_inventario_patrimoniado.estado_id, patrimonio.estado_conservacao_id, patrimonio.descricao, item_inventario.descricao, item_inventario.observacoes, patrimonio.sala_id, item_inventario.sala_id, pendencia_inventario.descricao, pendencia_inventario.resolvido
        ORDER BY pendencia_inventario.id";
        return  DB::select($sql);
        //return PendenciaInventario::all();
    }
}
