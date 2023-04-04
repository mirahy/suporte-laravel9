import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import { Arvore } from './arvore';

@Injectable()
export class PessoasEstatusLotacaoService {

    constructor(private http: Http) { }

    public readonly TIPO_PESSOA_ACADEMICO_GRADUACAO = "academico_graduacao";
    public readonly TIPO_PESSOA_ACADEMICO_POS = "academico_pos";
    public readonly TIPO_PESSOA_FUNCIONARIO = "funcionario";
    
    public readonly TIPOS_PESSOAS = [
        this.TIPO_PESSOA_ACADEMICO_GRADUACAO,
        this.TIPO_PESSOA_ACADEMICO_POS,
        this.TIPO_PESSOA_FUNCIONARIO
    ]

    estatusList = null;

    faculdades = [];
    cursos = [];

    arvoreLotacoes = new Arvore(0, null);
    lotacaoIndexFull = {};

    getEstatusList() {
        return this.http.get("/formulario-pessoas-estatus-lotacao/estatus")
            .toPromise()
            .then(response => {
                this.estatusList = response.json();
                return this.estatusList;
            });
            
    }
    getLotacoesList(estatus) {
        return this.http.post("/formulario-pessoas-estatus-lotacao/lotacoes", {estatus: estatus})
            .toPromise()
            .then(response => {
                var lotacaoIndex = {};
                var lotacoes = response.json();
                if (lotacoes) {
                    lotacoes = lotacoes.map (function (e) {
                        if (e.estatus) {
                            var l = e.lotacao.split(" - ");
                            return {caminho: l[0].trim().replace(" ", "/").split("/"), nome: l[1], caminhoFull: l[0].trim()};
                        }
                        return null;
                    });
                }
                var cont = 0
                for (var i in lotacoes) {
                    
                    if (!lotacoes[i]) {
                        lotacoes.splice (i, 1);
                        cont--;
                    }
                }
                for (var i in lotacoes) {
                    if(lotacoes[i] != null){
                    lotacoes[i].sigla = lotacoes[i].caminho[0];
                    lotacaoIndex[lotacoes[i].caminhoFull] = lotacoes[i];
                    }
                }
                this.arvoreLotacoes = this.geraArvoreLotacao(lotacaoIndex);
                return this.arvoreLotacoes;
            });
    }

    getDescLotacaoNodo(lotacaoNodo) {
        if (lotacaoNodo.dado) {
            if (lotacaoNodo.dado.sigla) 
                return lotacaoNodo.dado.nome ? lotacaoNodo.dado.sigla + " - " + lotacaoNodo.dado.nome : lotacaoNodo.dado.sigla;
            else 
                return lotacaoNodo.dado.nome ? lotacaoNodo.dado.nome : lotacaoNodo.dado.caminho;
        }
        return "null";
    }

    private geraArvoreLotacao (lotacaoIndex) {
        var nodoIndex = {};
        var incrementador = 0;
        var _this = this;

        var getCaminhoPars = function (caminho, idPars, caminhoFull) {
            var ret = caminho[idPars];
            for (var i = idPars+1; i < caminho.length; i++) {
                if (caminhoFull.search(" ") >= 0 && caminhoFull.search(ret+" "+caminho[i]) >= 0) 
                    ret += " " + caminho[i];
                else
                    ret += "/" + caminho[i];
            }
            return ret;
        }

        var sortFunctionPorDado = function (a1:Arvore, a2:Arvore) {
            var nome1 = _this.getDescLotacaoNodo(a1);
            var nome2 = _this.getDescLotacaoNodo(a2);
            if ( nome1.toLowerCase() ==  nome2.toLowerCase() )
                return 0;
            return nome1.toLowerCase() < nome2.toLowerCase() ? -1 : 1;
        };

        var arvoreLotacao = new Arvore(0, null);
        arvoreLotacao.sortFilhosArray = sortFunctionPorDado;
        
        for (var caminhoFull in lotacaoIndex) {
            var arvore = arvoreLotacao;
            var caminhoArr = lotacaoIndex[caminhoFull].caminho;
            for (var i = caminhoArr.length -1; i >= 0; i--) {
                var caminhoPars = getCaminhoPars(caminhoArr, i, caminhoFull);
                var nomeLot = "";
                var siglaLot = "";
                var lotacaoIndexCaminhoPars = this.lotacaoIndexFull[caminhoPars];
                if (lotacaoIndexCaminhoPars) {
                    nomeLot = lotacaoIndexCaminhoPars.nome;
                    siglaLot = lotacaoIndexCaminhoPars.sigla;
                }
                var nodo: Arvore = nodoIndex[caminhoPars];
                if (nodo) {

                }
                else {
                    incrementador++;
                    nodo = new Arvore(incrementador, {caminho: caminhoPars, nome: nomeLot, sigla: siglaLot, id: incrementador});
                    nodo.sortFilhosArray = sortFunctionPorDado;
                    nodoIndex[caminhoPars] = nodo;
                }
                arvore.insert(nodo);
                arvore = nodo;
            }
        }
        return arvoreLotacao;
    }

    getLotacoesFullList() {
        return this.http.get("/formulario-pessoas-estatus-lotacao/lotacoes-full")
            .toPromise()
            .then(response => {
                this.lotacaoIndexFull = {};
                var lotacoes = response.json();
                if (lotacoes) {
                    lotacoes = lotacoes.map (function (e) {
						if (e.lotacao) {
							var l = e.lotacao.split(" - ");
                        	return {caminho: l[0].trim().replace(" ", "/").split("/"), nome: l[1], caminhoFull: l[0].trim()};
						}
                        return null;
                    });
                }
				for (var cont = 0; cont < lotacoes.length; cont++) {
					if (!lotacoes[cont]) {
						lotacoes.splice (i, 1);
						cont--;
					}
				}
               
                for (var i in lotacoes) {
                    if(lotacoes[i] != null){
                        lotacoes[i].sigla = lotacoes[i].caminho[0];
                        this.lotacaoIndexFull[lotacoes[i].caminhoFull] = lotacoes[i];
                    }  
                }
                //this.arvoreLotacoes = this.geraArvoreLotacao(this.lotacaoIndexFull);
                return this.lotacaoIndexFull;
            });
    }
    getFaculdadesList(estatus) {
        return this.http.post("/formulario-pessoas-estatus-lotacao/faculdades", {estatus: estatus})
            .toPromise()
            .then(response => {
                var faculdades = response.json();
                if (faculdades)
                    faculdades = faculdades.map (function (e) {return e.faculdade});
                this.faculdades = faculdades;
                return faculdades
            });
    }
    getCursosFaculdadeList(estatus, faculdade) {
        return this.http.post("/formulario-pessoas-estatus-lotacao/cursos-faculdade", {estatus: estatus, faculdade: faculdade})
            .toPromise()
            .then(response => {
                var cursos = response.json();
                if (cursos)
                cursos = cursos.map (function (e) {return e.lotacao});
                this.cursos = cursos;
                return cursos
            });
    }

    getDadosAcademico(estatus, faculdade, curso) {
        return this.http.post("/formulario-pessoas-estatus-lotacao/academico", {estatus: estatus, faculdade: faculdade, curso: curso})
            .toPromise()
            .then(response => {
                return response.json();
            });
    }
    getDadosFuncionario(estatus, faculdade, curso) {
        return this.http.post("/formulario-pessoas-estatus-lotacao/funcionario", {estatus: estatus, faculdade: faculdade, curso: curso})
            .toPromise()
            .then(response => {
                return response.json();
            });
    }
}