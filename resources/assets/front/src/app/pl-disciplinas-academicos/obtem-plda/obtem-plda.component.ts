import { Component, Input, OnInit } from '@angular/core';
import { AbstractComponentChild } from 'src/app/abstract-component-child';
import { AbstractComponentInterface } from 'src/app/abstract-component-interface';
import { AbstractPLDAComponentInterface } from 'src/app/abstract-plda-component-interface';
import { CursosService } from 'src/app/cursos.service';
import { FaculdadeService } from 'src/app/faculdade.service';
import { Faculdade } from 'src/app/faculdades/faculdade';
import { PeriodoLetivosService } from 'src/app/periodo-letivos.service';
import { PlDisciplinasAcademicosService } from 'src/app/pl-disciplinas-academicos.service';
import { Estudante } from '../estudante';
import { PlDisciplinasAcademicos } from '../pl-disciplinas-academicos';
declare var jQuery: any;

@Component({
	selector: 'app-obtem-plda',
	templateUrl: './obtem-plda.component.html',
	styleUrls: ['./obtem-plda.component.less']
})
export class ObtemPldaComponent extends AbstractComponentChild implements OnInit {

	constructor(private periodoLetivosService:PeriodoLetivosService, private plDisciplinasAcademicosService:PlDisciplinasAcademicosService, 
        	private faculdadeService:FaculdadeService, private cursosService: CursosService) {
		super();
	}


	estudantes:Array<Estudante> = [];
    estudanteTemp: Estudante = Estudante.generateEstudante();
    faculdadeTemp = Faculdade.generateFaculdade();
    nomeDisciplinaTemp = "";
    disciplinaKeyTemp:any = "";

	carregandoEstudantesFlag = false;

    @Input() 
    plus = false;

	get periodoLetivos() {
        return this.periodoLetivosService.periodoLetivos;
    }
    get faculdades() {
        return this.faculdadeService.faculdades;
    }
    get plDisciplinasAcademicos () :Array<PlDisciplinasAcademicos>{
        return this.plDisciplinasAcademicosService.plDisciplinasAcademicos;
    }
	get plDisciplinasAcademicosTemp () : PlDisciplinasAcademicos {
		return (<AbstractPLDAComponentInterface>this.ancestral).plDisciplinasAcademicosTemp;
	}
	set plDisciplinasAcademicosTemp (plDisciplinasAcademicos : PlDisciplinasAcademicos)  {
		(<AbstractPLDAComponentInterface>this.ancestral).plDisciplinasAcademicosTemp = plDisciplinasAcademicos;
	}
	get plDisciplinasAcademicosTempList () : Array<PlDisciplinasAcademicos> {
		return (<AbstractPLDAComponentInterface>this.ancestral).plDisciplinasAcademicosTempList;
	}
	set plDisciplinasAcademicosTempList (plDisciplinasAcademicosList : Array<PlDisciplinasAcademicos>)  {
		(<AbstractPLDAComponentInterface>this.ancestral).plDisciplinasAcademicosTempList = plDisciplinasAcademicosList;
	}
	get modoLista () : boolean {
		return (<AbstractPLDAComponentInterface>this.ancestral).modoLista;
	}
	set modoLista (modoLista : boolean)  {
		(<AbstractPLDAComponentInterface>this.ancestral).modoLista = modoLista;
	}
	get periodoLetivoSelecionadoId () {
		return (<AbstractPLDAComponentInterface>this.ancestral).periodoLetivoSelecionadoId;
	}
	set periodoLetivoSelecionadoId (p) {
		(<AbstractPLDAComponentInterface>this.ancestral).periodoLetivoSelecionadoId = p;
	}
	get cursoSelecionadoId () {
		return (<AbstractPLDAComponentInterface>this.ancestral).cursoSelecionadoId;
	}
	set cursoSelecionadoId (cursoId) {
		(<AbstractPLDAComponentInterface>this.ancestral).cursoSelecionadoId = cursoId;
	}
	get faculdadeSelecionadaId () {
		return (<AbstractPLDAComponentInterface>this.ancestral).faculdadeSelecionadaId;
	}
	set faculdadeSelecionadaId (faculdadeId) {
		(<AbstractPLDAComponentInterface>this.ancestral).faculdadeSelecionadaId = faculdadeId;
	}
	get disciplinaSelecionadaNome () {
		return (<AbstractPLDAComponentInterface>this.ancestral).disciplinaSelecionadaNome;
	}
	set disciplinaSelecionadaNome (nomeDisciplina) {
		(<AbstractPLDAComponentInterface>this.ancestral).disciplinaSelecionadaNome = nomeDisciplina;
	}
	get disciplinaSelecionadaNomes () {
		return (<AbstractPLDAComponentInterface>this.ancestral).disciplinaSelecionadaNomes;
	}
	set disciplinaSelecionadaNomes (nomeDisciplinas) {
		(<AbstractPLDAComponentInterface>this.ancestral).disciplinaSelecionadaNomes = nomeDisciplinas;
	}

	selecionaPeriodoLetivo(){
        if (this.periodoLetivoSelecionadoId) {
            this.faculdadeTemp = Faculdade.generateFaculdade();
            this.faculdadeSelecionadaId = "";
            this.cursoSelecionadoId = "";
            this.disciplinaSelecionadaNome = "";
            this.plDisciplinasAcademicosTemp = PlDisciplinasAcademicos.generatePlDisciplinasAcademicos();
            this.nomeDisciplinaTemp = "";
        }
    }
    selecionaFaculdade() {
        if (this.faculdadeSelecionadaId) {
            this.faculdadeTemp = this.faculdadeService.faculdadesIndex.get(this.faculdadeSelecionadaId);
            this.cursoSelecionadoId = "";
            this.disciplinaSelecionadaNome = "";
            this.plDisciplinasAcademicosTemp = PlDisciplinasAcademicos.generatePlDisciplinasAcademicos();
            this.nomeDisciplinaTemp = "";
        }
    }
    selecionaCurso() {
        if (this.cursoSelecionadoId) {
            this.disciplinaSelecionadaNome = "";
            this.plDisciplinasAcademicosTemp = PlDisciplinasAcademicos.generatePlDisciplinasAcademicos();
            this.nomeDisciplinaTemp = "";
            this.editavel = false;
			this.carregandoEstudantesFlag = true;
            this.plDisciplinasAcademicosService.getPlDisciplinasAcademicos(this.periodoLetivoSelecionadoId, this.cursoSelecionadoId, 
                this.plus ? this.cursosService.cursosKeyIndex : null, this.plus ? this.periodoLetivosService.periodoLetivosNameIndex : null)
                .then(r => {
                    this.editavel = true;
					this.carregandoEstudantesFlag = false;
                }).catch(response => {
                    this.erroAviso = true;
                    this.aviso = this.erroHttp(response);
                    alert(this.aviso)
                    this.editavel = true;
					this.carregandoEstudantesFlag = false;
                });
        }
    }
    selecionaDisciplina() {
        if (this.modoLista) {
            this.plDisciplinasAcademicosTempList = [];
            if (this.disciplinaSelecionadaNomes.length) 
                for (var i in this.disciplinaSelecionadaNomes)
                    this.plDisciplinasAcademicosTempList.push(this.plDisciplinasAcademicosService.plDisciplinasAcademicosNameIndex.get(this.disciplinaSelecionadaNomes[i]));
        }
        else if (this.disciplinaSelecionadaNome) 
            this.plDisciplinasAcademicosTemp = this.plDisciplinasAcademicosService.plDisciplinasAcademicosNameIndex.get(this.disciplinaSelecionadaNome);
        else 
            this.plDisciplinasAcademicosTemp = PlDisciplinasAcademicos.generatePlDisciplinasAcademicos();
    }

	ngOnInit() {
        this.faculdadeService.listar()
            .then(response => {
                this.periodoLetivosService.getPeriodoLetivos()
                    .then(r => {
                        this.status = this.COMPLETE;
                        this.editavel = true;
                    }).catch(response => {
                        this.erroAviso = true;
                        this.aviso = this.erroHttp(response);
                        this.status = this.ERROR;
                        alert(this.aviso);
                    });
            })
            .catch(response => {
                this.erroAviso = true;
                this.aviso = this.erroHttp(response);
                this.status = this.ERROR;
                alert(this.aviso);
            })
    }

}
