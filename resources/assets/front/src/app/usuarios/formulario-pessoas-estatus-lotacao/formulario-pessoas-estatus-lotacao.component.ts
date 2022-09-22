import { Component, OnInit } from '@angular/core';
import { AbstractComponent } from 'src/app/abstract-component';
import { Arvore } from 'src/app/arvore';
import { CursosService } from 'src/app/cursos.service';
import { FaculdadeService } from 'src/app/faculdade.service';
import { Faculdade } from 'src/app/faculdades/faculdade';
import { PessoasEstatusLotacaoService } from 'src/app/pessoas-estatus-lotacao.service';
declare var jQuery: any;

@Component({
	selector: 'app-formulario-pessoas-estatus-lotacao',
	templateUrl: './formulario-pessoas-estatus-lotacao.component.html',
	styleUrls: ['./formulario-pessoas-estatus-lotacao.component.less']
})
export class FormularioPessoasEstatusLotacaoComponent extends AbstractComponent implements OnInit {

	constructor(private pessoasEstatusLotacaoService: PessoasEstatusLotacaoService,
			private faculdadeService:FaculdadeService, private cursosService:CursosService) {
		super();
	}

	estatusSelecionadoId = "";

	faculdadeTemp = "";
	cursoTemp = "";
	lotacoesTrees: Arvore[] = [null];
	lotacoesSelected: Array<number|string> = [""];

	get estatusList () {
		return this.pessoasEstatusLotacaoService.estatusList;
	}
	set estatusList (estatusList) {
		this.pessoasEstatusLotacaoService.estatusList = estatusList
	}
    /*get faculdades() {
        return this.faculdadeService.faculdades;
    }*/
	get faculdades () {
		return this.pessoasEstatusLotacaoService.faculdades;
	}
	get cursos () {
		return this.pessoasEstatusLotacaoService.cursos;
	}
    get arvoreLotacoes():Arvore {
        return this.pessoasEstatusLotacaoService.arvoreLotacoes;
    }
	get TIPO_PESSOA_ACADEMICO_GRADUACAO () {
		return this.pessoasEstatusLotacaoService.TIPO_PESSOA_ACADEMICO_GRADUACAO;
	}
	get TIPO_PESSOA_ACADEMICO_POS () {
		return this.pessoasEstatusLotacaoService.TIPO_PESSOA_ACADEMICO_POS;
	}
	get TIPO_PESSOA_FUNCIONARIO () {
		return this.pessoasEstatusLotacaoService.TIPO_PESSOA_FUNCIONARIO;
	}
	get TIPOS_PESSOAS () {
		return this.pessoasEstatusLotacaoService.TIPOS_PESSOAS;
	}
	get estatusSelecionadoTipoPessoa () {
		if (!isNaN(parseInt(this.estatusSelecionadoId)) ) {
			return this.estatusList[this.estatusSelecionadoId].tipo_pessoa;
		}
		return null;
	}
	get estatusSelecionadoEstatus () {
		if (!isNaN(parseInt(this.estatusSelecionadoId)) ) {
			return this.estatusList[this.estatusSelecionadoId].estatus;
		}
		return null;
	}
	get lotacaoTemp () {
		return this.lotacoesTrees[0] ? this.lotacoesTrees[this.lotacoesTrees.length-2].dado.caminho : null;
	}

	selecionaEstatus() {
		this.faculdadeTemp = "";
		if (this.estatusSelecionadoTipoPessoa == this.TIPO_PESSOA_ACADEMICO_GRADUACAO || this.estatusSelecionadoTipoPessoa == this.TIPO_PESSOA_ACADEMICO_POS) {
			this.editavel = false;
			this.pessoasEstatusLotacaoService.getFaculdadesList(this.estatusSelecionadoEstatus)
				.then(response => {
					this.status = this.COMPLETE;
					this.editavel = true;
				})
				.catch(response => {
					this.erroAviso = true;
					this.aviso = this.erroHttp(response);
					this.status = this.ERROR;
					alert(this.aviso);
				})
		}
		else if (this.estatusSelecionadoTipoPessoa == this.TIPO_PESSOA_FUNCIONARIO) {
			this.editavel = false;
			this.pessoasEstatusLotacaoService.getLotacoesList(this.estatusSelecionadoEstatus)
				.then(response => {
					this.status = this.COMPLETE;
					this.lotacoesSelected = [""];
					this.lotacoesTrees= [null];
					this.editavel = true;
				})
				.catch(response => {
					this.erroAviso = true;
					this.aviso = this.erroHttp(response);
					this.status = this.ERROR;
					alert(this.aviso);
				})
		}
	}
	selecionaFaculdade() {
        this.cursoTemp = "";
        if (this.faculdadeTemp) {
			this.editavel = false;
			this.pessoasEstatusLotacaoService.getCursosFaculdadeList(this.estatusSelecionadoEstatus, this.faculdadeTemp)
				.then(response => {
					this.status = this.COMPLETE;
					this.editavel = true;
				})
				.catch(response => {
					this.erroAviso = true;
					this.aviso = this.erroHttp(response);
					this.status = this.ERROR;
					alert(this.aviso);
				})
        }
    }
	selecionaCurso() {
        if (this.cursoTemp) {
            
        }
    }

	selecionaLotacao (paiId, index) {
		var nodoIndex = this.lotacoesSelected[index];
        var nodo = null;
        if (nodoIndex)
            nodo = this.arvoreLotacoes.find(<number> nodoIndex);
        if (nodo) {
            if (paiId) {
                for (var i = 0; i < this.lotacoesTrees.length; i++) {
                    if (this.lotacoesTrees[i] && this.lotacoesTrees[i].id == paiId) {
                        this.lotacoesTrees = this.lotacoesTrees.slice(0,i+1);
                        this.lotacoesSelected = this.lotacoesSelected.slice(0,i+1);
                        this.lotacoesTrees.push(nodo);
                        this.lotacoesSelected.push(nodo.id);
                        this.lotacoesTrees.push(null);
                        this.lotacoesSelected.push('');
                        break;
                    }
                }
            }
            else {
                this.lotacoesTrees = [nodo, null];
                this.lotacoesSelected = [nodo.id, ''];
            }
        }
        else {
            if (paiId) {
                for (var i = 0; i < this.lotacoesTrees.length; i++) {
                    if (this.lotacoesTrees[i] && this.lotacoesTrees[i].id == paiId) {
                        this.lotacoesTrees = this.lotacoesTrees.slice(0,i+1);
                        this.lotacoesSelected = this.lotacoesSelected.slice(0,i+1);
                        this.lotacoesTrees.push(null);
                        this.lotacoesSelected.push('');
                        break;
                    }
                }
            }
            else {
                this.lotacoesTrees = [null];
                this.lotacoesSelected = [''];
            }
        }
	}
	
	obterDadosAcademicos(ev) {
		ev.preventDefault();
		if (!this.faculdadeTemp && !this.cursoTemp && !confirm("Deseja obter dados de todas as pessoas da categoria '"+this.estatusSelecionadoEstatus+"' da instituição?"))
			return;
		var formDadosAcademicos = jQuery('#formDadosAcademicos');
		formDadosAcademicos.submit();
	}
	obterDadosFuncionarios(ev) {
		ev.preventDefault();
		if (!this.lotacaoTemp && !confirm("Deseja obter dados de todas as pessoas da categoria '"+this.estatusSelecionadoEstatus+"' da instituição?"))
			return;
		var formDadosFuncionarios = jQuery('#formDadosFuncionarios');
		formDadosFuncionarios.submit();
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

	ngOnInit() {
		this.pessoasEstatusLotacaoService.getEstatusList() 
			.then(response => {
				this.pessoasEstatusLotacaoService.getLotacoesFullList()
					.then(response => {
						this.status = this.COMPLETE;
						this.editavel = true;
					})
					.catch(response => {
						this.erroAviso = true;
						this.aviso = this.erroHttp(response);
						this.status = this.ERROR;
						alert(this.aviso);
					})
			})
			.catch(response => {
				this.erroAviso = true;
				this.aviso = this.erroHttp(response);
				this.status = this.ERROR;
				alert(this.aviso);
				this.estatusList = [];
			})
	}

}
